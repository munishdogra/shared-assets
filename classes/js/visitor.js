(function () {
  const TOKEN_KEY = 'visitor_token';

  // Generate a simple UUIDv4-style token
  function generateToken() {
    return 'vis_' + crypto.randomUUID();
  }

  // Extract visitor_token from URL query string
  function getTokenFromQuery() {
    const params = new URLSearchParams(window.location.search);
    return params.get('visitor_token');
  }

  // Store the token in localStorage
  function setLocalStorageToken(token) {
    localStorage.setItem(TOKEN_KEY, token);
  }

  // Read the token from localStorage
  function getLocalStorageToken() {
    return localStorage.getItem(TOKEN_KEY);
  }

  // Clean up the URL (remove visitor_token param from address bar)
  function cleanupUrl() {
    const url = new URL(window.location.href);
    url.searchParams.delete('visitor_token');
    window.history.replaceState({}, '', url.pathname + url.search);
  }

  // Main logic to ensure a consistent visitor token
  (function handleVisitorToken() {
    const tokenInUrl = getTokenFromQuery();
    const tokenInStorage = getLocalStorageToken();

    if (tokenInUrl) {
      // If token is passed via URL, store it
      setLocalStorageToken(tokenInUrl);
      cleanupUrl();
      console.log('[visitor.js] Token from URL set to localStorage:', tokenInUrl);
    } else if (!tokenInStorage) {
      // Generate a new token if none exists
      const newToken = generateToken();
      setLocalStorageToken(newToken);
      console.log('[visitor.js] New token generated:', newToken);
    } else {
      // Token already exists
      console.log('[visitor.js] Token already in localStorage:', tokenInStorage);
    }
  })();
})();