import axios from 'axios';

axios.get('/api/sso/ajax/user-info')
  .then(function (response) {
    if (response.status === 204) {
      return;
    }
    window.location.reload();
  });
