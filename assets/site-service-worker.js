// assets/site-service-worker.js - Site-wide PWA service worker
const CACHE_NAME = 'majed-portfolio-v4';
const STATIC_ASSETS = [
  '/',
  '/assets/css/style.css',
  '/assets/css/gallery.css',
  '/assets/css/auth.css',
  '/assets/css/persian-datepicker.min.css',
  '/assets/js/security.js',
  '/assets/js/jquery-3.6.0.min.js',
  '/assets/js/persian-date.min.js',
  '/assets/js/persian-datepicker.min.js',
  '/assets/js/pwa-install.js',
  '/assets/images/admin-icon-192.png',
  '/assets/images/admin-icon-512.png',
  '/assets/images/og-default.jpg'
];

self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache) {
      return cache.addAll(STATIC_ASSETS);
    }).catch(function() {})
  );
  self.skipWaiting();
});

self.addEventListener('activate', function(event) {
  event.waitUntil(
    caches.keys().then(function(keys) {
      return Promise.all(
        keys.filter(function(key) {
          return key !== CACHE_NAME;
        }).map(function(key) {
          return caches.delete(key);
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', function(event) {
  const request = event.request;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') return;

  // Skip admin and dynamic routes
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/database') || url.pathname.startsWith('/logout')) return;

  // Network-first static assets: a portfolio must show the latest CSS and imagery
  // after deployment, while still retaining an offline fallback.
  if (isStaticAsset(request.url)) {
    event.respondWith(
      fetch(request).then(function(networkResponse) {
        if (networkResponse && networkResponse.ok) {
          caches.open(CACHE_NAME).then(function(cache) {
            cache.put(request, networkResponse.clone());
          });
        }
        return networkResponse;
      }).catch(function() {
        return caches.match(request);
      })
    );
    return;
  }

  // Network-first for HTML pages
  if (request.headers.get('accept') && request.headers.get('accept').includes('text/html')) {
    event.respondWith(
      fetch(request).then(function(response) {
        return caches.open(CACHE_NAME).then(function(cache) {
          cache.put(request, response.clone());
          return response;
        });
      }).catch(function() {
        return caches.match(request).then(function(response) {
          return response || caches.match('/');
        });
      })
    );
  }
});

function isStaticAsset(url) {
  return /\.(css|js|png|jpg|jpeg|webp|svg|gif|ico|woff|woff2|ttf|mp4|webm)$/.test(url);
}
