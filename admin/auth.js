(function(){
  const origFetch = window.fetch;
  window.fetch = function(url, options = {}) {
    const token = localStorage.getItem('admin_token');
    options.headers = options.headers || {};
    if (token) {
      options.headers['Authorization'] = 'Bearer ' + token;
    }
    return origFetch(url, options);
  };
})();
