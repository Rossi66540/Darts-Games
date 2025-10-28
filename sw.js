const CACHE_NAME = 'V0.1';
const BASE_PATH = '/mono/';

const urlsToCache = [
  //BASE_PATH,
  BASE_PATH + 'index.html',
  BASE_PATH + 'style/style.css',
  BASE_PATH + 'script.js',
  //BASE_PATH + 'icons/icon-192.png'
];

// Installation du cache
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
      .then(() => self.skipWaiting())
  );
});

// Utilisation du cache
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});

// Nettoyage des anciens caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames =>
      Promise.all(
        cacheNames.map(name => {
          if (name !== CACHE_NAME) {
            return caches.delete(name);
          }
        })
      )
    ).then(() => self.clients.claim())
  );
});
