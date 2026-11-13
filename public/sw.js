const CACHE_NAME = 'studio-hris-v2';
const ASSETS = [
  '/hris',
  '/manifest.json',
  '/hris-icon-192.png',
  '/hris-icon-512.png',
  '/hris-icon-maskable-512.png'
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  if (e.request.method !== 'GET') return;
  const url = new URL(e.request.url);
  
  // Do not intercept API requests or non-http requests
  if (url.pathname.startsWith('/api/') || !url.protocol.startsWith('http')) return;

  e.respondWith(
    fetch(e.request)
      .catch(async () => {
        const cached = await caches.match(e.request);
        if (cached) return cached;
        if (e.request.mode === 'navigate' && url.pathname.startsWith('/hris')) {
          const fallback = await caches.match('/hris');
          if (fallback) return fallback;
        }
        return new Response('Network offline', { status: 503, statusText: 'Service Unavailable' });
      })
  );
});

