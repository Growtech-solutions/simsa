self.addEventListener('install', (event) => {
    console.log('SW instalado');
    event.waitUntil(
        caches.open('mi-app-cache').then((cache) => {
            return cache.addAll([
                './',
                './index.php',
                './manifest.json',
                './img/icon.png',
                './styles_celular.css'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
