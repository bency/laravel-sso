<?php

namespace Casperlaitw\LaravelSSO\Server;

use Casperlaitw\LaravelSSO\LaravelSSO;
use Casperlaitw\LaravelSSO\Models\Broker;
use Casperlaitw\LaravelSSO\Session\StoreSession;
use Casperlaitw\SSO\Server\BrokerException;
use Casperlaitw\SSO\Server\ExceptionInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Spatie\Url\Url;

/**
 * Class Server
 *
 * @package Casperlaitw\LaravelSSO\Server
 */
class Server extends \Casperlaitw\SSO\Server\Server
{
    /**
     * SSOServer constructor.
     */
    public function __construct()
    {
        parent::__construct(
            function (string $name) {
                return with(Broker::where('name', $name)->firstOrFail(), function (Broker $broker) {
                    return [
                        'secret' => $broker->secret,
                        'domains' => $broker->domains,
                    ];
                });
            },
            app('cache.store')
        );

        $this->session = app(StoreSession::class);
        if ($channel = config('laravel-sso.logging.channel')) {
            $this->logger = Log::channel($channel);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attachWithResponse(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $code = $this->attach();
            $error = null;
        } catch (ExceptionInterface $ex) {
            $code = null;
            $error = [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ];
        } catch (ModelNotFoundException $ex) {
            $code = null;
            $error = [
                'code' => $ex->getCode(),
                'message' => __('Invalid Broker'),
            ];
        }

        $url = Url::fromString(urldecode($request->input('return_url')));

        if ((bool)$error) {
            return redirect()->to((string)$url->withQueryParameter('sso_error', $error['message']));
        }

        return redirect()->to((string)$url->withQueryParameter('sso_verify', $code));
    }

    /**
     * @param \Illuminate\Http\Request                 $request
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function login(Request $request): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                // switch session id after get current user data
                $this->startBrokerSession();

                if (LaravelSSO::$serverAuthenticateUsingCallback) {
                    $user = call_user_func(LaravelSSO::$serverAuthenticateUsingCallback, $user);
                }

                $this->session->setUserSession($user);

                return $user;
            }
        } catch (Exception $ex) {
            throw new RuntimeException($ex->getMessage());
        }

        return null;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getUserInfo()
    {
        try {
            $this->startBrokerSession();
            return $this->session->getUserSession();
        } catch (Exception $ex) {
            throw new RuntimeException($ex->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getBroker()
    {
        [$brokerId] = $this->parseBearer($this->getBearerToken());
        return $brokerId;
    }

    /**
     */
    public function logout()
    {
        if (! $this->session->isActive()) {
            $this->startBrokerSession();
        }
        if ($user = $this->session->getUserSession()) {
            DB::table('sessions')->where('user_id', $user['id'])->delete();
        }
        $this->session->clearUserSession();
    }

    /**
     * Validate attach request and return broker id and token.
     *
     * Ignore validate return url
     *
     * @param ServerRequestInterface|null $request
     * @return array{broker:string,token:string}
     * @throws BrokerException
     */
    protected function processAttachRequest(?ServerRequestInterface $request): array
    {
        $brokerId = $this->getQueryParam($request, 'broker', true);
        $token = $this->getQueryParam($request, 'token', true);

        $checksum = $this->getQueryParam($request, 'checksum', true);
        $this->validateChecksum($checksum, 'attach', $brokerId, $token);

        $origin = $this->getHeader($request, 'Origin');
        if ($origin !== '') {
            $this->validateDomain('origin', $origin, $brokerId, $token);
        }

        return ['broker' => $brokerId, 'token' => $token];
    }
}
