// Import and configure the Firebase SDK
// These scripts are made available when the app is served or deployed on Firebase Hosting
// If you do not serve/host your project using Firebase Hosting see https://firebase.google.com/docs/web/setup

importScripts('https://www.gstatic.com/firebasejs/4.12.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.12.0/firebase-messaging.js');
// Initialize the Firebase app in the service worker by passing in the
// iSenderId.
var myId = get_sw_url_parameters('iSenderId');

function get_sw_url_parameters( param ) {
    var vars = {};
    self.location.href.replace( self.location.hash, '' ).replace(
        /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
        function( m, key, value ) { // callback
            vars[key] = value !== undefined ? value : '';
        }
    );
    if( param ) {
        return vars[param] ? vars[param] : null;
    }
    return vars;
}
var config = {
    messagingSenderId: myId
};
firebase.initializeApp(config);
const messaging = firebase.messaging();

// If you would like to customize notifications that are received in the
// background (Web app is closed or not in browser focus) then you should
// implement this optional method.
// [START background_handler]
messaging.setBackgroundMessageHandler(function (payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    var notification = payload.data;
    // Customize notification here
    var notificationTitle = notification.title,
        notificationOptions = {
            body: notification.body
        };
    if (notification.icon.length) {
        notificationOptions.icon = notification.icon;
    }
    if (notification.image.length) {
        notificationOptions.image = notification.image;
    }
    notificationOptions.data = notification;
    return self.registration.showNotification(notificationTitle,notificationOptions);
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    var data = event.notification.data,
        url = data.url;
    const markAsReadNotification = fetch(data.ync_ajax_url, {
        method: 'post',
        headers: {
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        body: 'notification_id=' + data.ync_push_id,
        success: function () {
            console.log('Mark as read notification success:', data.ync_push_id);
        }
    });
    const focusOnUrl = clients.matchAll({
            type: "window",
            includeUncontrolled: true
        }).then(function(clientList) {
            var matchingClient = null;
            for (var i = 0; i < clientList.length; i++) {
                const windowClient = clientList[i];
                if (windowClient.url === url) {
                    matchingClient = windowClient;
                    break;
                }
            }
            if (matchingClient) {
                return matchingClient.focus();
            } else {
                return clients.openWindow(url);
            }
        });

    const promiseChain = Promise.all([
        markAsReadNotification,
        focusOnUrl
    ]);

    event.waitUntil(promiseChain);
});
// [END background_handler]