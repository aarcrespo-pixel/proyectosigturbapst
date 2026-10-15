self.addEventListener('push', (event) => {
    const payload = event.data && event.data.json ? event.data.json() : null;
    const title = payload?.title || 'SIGTUR';
    const options = {
        body: payload?.body || 'Tienes una nueva notificación.',
        icon: '/img/logoblanco.png',
        badge: '/img/logoblanco.png',
        data: {
            url: payload?.url || '/index.php',
        },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.preventDefault();
    const notification = event.notification;
    const url = notification.data?.url || '/index.php';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if ('focus' in client) {
                    client.focus();
                    client.postMessage({ type: 'open-notification', url });
                    return;
                }
            }

            return clients.openWindow(url);
        })
    );

    notification.close();
});
