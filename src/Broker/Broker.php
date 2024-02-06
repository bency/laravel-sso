<?php

namespace Casperlaitw\LaravelSSO\Broker;

use Casperlaitw\LaravelSSO\Exceptions\BrokerIsNotAttachedException;
use Casperlaitw\SSO\Broker\NotAttachedException;
use Casperlaitw\SSO\Broker\RequestException;
use Eastwest\Json\Json;
use Eastwest\Json\JsonException;
use Illuminate\Http\Request;
use Spatie\Url\Url;

/**
 * Class Broker
 *
 * @package Casperlaitw\LaravelSSO\Broker
 */
class Broker extends \Casperlaitw\SSO\Broker\Broker
{
    /**
     * SSOBroker constructor.
     */
    public function __construct()
    {
        parent::__construct(
            config('laravel-sso.sso_server_url') . '/api/sso/attach',
            config('laravel-sso.broker_name'),
            config('laravel-sso.broker_secret')
        );
    }

    /**
     * Send an HTTP request to the SSO server.
     *
     * @param string                     $method  HTTP method: 'GET', 'POST', 'DELETE'
     * @param string                     $path    Relative path
     * @param array<string,mixed>|string $data    Query or post parameters
     * @return mixed
     * @throws NotAttachedException
     */
    public function request(string $method, string $path, $data = '')
    {
        $url = $this->getRequestUrl($path, $method === 'POST' ? '' : $data);
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->getBearerToken(),
            'X-FORWARDED-FOR: '.request()->ip(),
        ];

        ['httpCode' => $httpCode, 'contentType' => $contentTypeHeader, 'body' => $body] =
            $this->getCurl()->request($method, $url, $headers, $method === 'POST' ? $data : '');

        if ($httpCode === 204) {
            return null;
        }

        [$contentType] = explode(';', (string)$contentTypeHeader, 2);

        if ($contentType !== 'application/json') {
            throw new RequestException(
                "Expected 'application/json' response, got '$contentType'",
                500,
                new RequestException($body, $httpCode)
            );
        }

        try {
            $data = Json::decode($body, true);
        } catch (JsonException $exception) {
            throw new RequestException("Invalid JSON response from server", 500, $exception);
        }

        if ($httpCode >= 400) {
            throw new RequestException($data['error'] ?? $body, $httpCode);
        }

        return $data;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function attach()
    {
        if ($this->isAttached()) {
            return null;
        }

        return redirect()->to($this->getAttachUrl(['return_url' => request()->fullUrl()]), 307);
    }

    /**
     * @param $email
     * @param $password
     *
     * @return mixed|null
     */
    public function login($email, $password)
    {
        if ($this->isAttached()) {
            return $this->request('POST', '/api/sso/login', compact('email', 'password'));
        }

        throw new BrokerIsNotAttachedException();
    }

    /**
     * @return mixed|null
     */
    public function userInfo($data = [])
    {
        if ($this->isAttached()) {
            return $this->request('GET', '/api/sso/user-info', $data);
        }
    }

    /**
     * @return mixed|null
     */
    public function logout()
    {
        if ($this->isAttached()) {
            return $this->request('POST', '/api/sso/logout');
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function hasErrors(Request $request)
    {
        return $request->input('sso_error');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function verifyWithResponse(Request $request)
    {
        if ($token = $request->input('sso_verify')) {
            $this->verify($token);

            return redirect()
                ->to((string)Url::fromString($request->fullUrl())->withoutQueryParameter('sso_verify'));
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function loginCurrentUser($data = [])
    {
        if ($this->isAttached()) {
            if (($user = $this->userInfo($data)) && $user['user']) {
                return $user['user'];
            }
        }
    }
}
