(function () {
  const CONFIG = {
    tokenKey: 'visitor_token',
    createdAtKey: 'visitor_token_created_at',
    expiryDays: 180,
    reportUrl: 'https://viennaadvantage.com/tracking/store_token.php', // e.g. 'https://tracker.mycompany.com/store_token'
    recoveryUrl: 'https://viennaadvantage.com/tracking/get_token.php', // e.g. 'https://tracker.mycompany.com/get_token'
    debug: false,
  };

  function log(...args) {
    if (CONFIG.debug || window.location.search.includes('debug=true')) {
      console.log('[visitor.js]', ...args);
    }
  }

  function generateToken() {
    return 'vis_' + crypto.randomUUID();
  }

  function setToken(token) {
    const now = new Date().toISOString();
    localStorage.setItem(CONFIG.tokenKey, token);
    localStorage.setItem(CONFIG.createdAtKey, now);
    log('Token set:', token, 'at', now);
  }

  function getToken() {
    return localStorage.getItem(CONFIG.tokenKey);
  }

  function isTokenExpired() {
    const createdAt = localStorage.getItem(CONFIG.createdAtKey);
    if (!createdAt) return true;
    const createdDate = new Date(createdAt);
    const now = new Date();
    const diff = (now - createdDate) / (1000 * 60 * 60 * 24); // days
    return diff > CONFIG.expiryDays;
  }

  function getQueryParam(name) {
    return new URLSearchParams(window.location.search).get(name);
  }

  function removeQueryParam(param) {
    const url = new URL(window.location.href);
    url.searchParams.delete(param);
    window.history.replaceState({}, '', url.pathname + url.search);
  }

  function reportToServer(token) {
    if (!CONFIG.reportUrl) return;
    fetch(CONFIG.reportUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        token: token,
        userAgent: navigator.userAgent,
        url: window.location.href,
        timestamp: new Date().toISOString(),
      }),
    }).then(res => log('Token reported to server:', res.status))
      .catch(err => log('Error reporting token:', err));
  }

  function recoverFromServer() {
    if (!CONFIG.recoveryUrl) return;
    fetch(CONFIG.recoveryUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        userAgent: navigator.userAgent,
        url: window.location.href,
        timestamp: new Date().toISOString(),
      }),
    })
    .then(res => res.json())
    .then(data => {
      if (data.token) {
        log('Recovered token from server:', data.token);
        setToken(data.token);
      } else {
        log('No token returned from recovery API');
        const token = generateToken();
        setToken(token);
        reportToServer(token);
      }
    })
    .catch(err => {
      log('Token recovery error:', err);
      const token = generateToken();
      setToken(token);
      reportToServer(token);
    });
  }

  function handleVisitorToken() {
    const tokenFromUrl = getQueryParam('visitor_token');
    const tokenInStorage = getToken();

    if (tokenFromUrl) {
      setToken(tokenFromUrl);
      removeQueryParam('visitor_token');
      reportToServer(tokenFromUrl);
    } else if (tokenInStorage && !isTokenExpired()) {
      log('Existing valid token:', tokenInStorage);
    } else if (CONFIG.recoveryUrl) {
      log('Trying to recover token from backend...');
      recoverFromServer();
    } else {
      const newToken = generateToken();
      setToken(newToken);
      reportToServer(newToken);
    }
  }

  handleVisitorToken();
})();
