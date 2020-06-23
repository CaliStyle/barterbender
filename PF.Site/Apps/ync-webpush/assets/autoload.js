var yncwebpush = {
    oMessaging: null,
    sBrowser: '',
    initService: false,
    bShowRequest: false,
    sCurrentToken: '',
    requestPermission: function () {
        yncwebpush.oMessaging.requestPermission()
            .then(function () {
                yncwebpush.bShowRequest = true;
                yncwebpush.getBrowserToken(true);
            })
            .catch(function (err) {
                console.log('Unable to get permission to notify.', err);
            });
    },
    handleGrantedToken: function (sToken) {
        $.ajaxCall('yncwebpush.handleGrantedToken', 'token=' + sToken + '&browser=' + yncwebpush.sBrowser, 'post');
        return true;
    },
    skipRequestBanner: function (obj) {
        yncwebpush.bShowRequest = false;
        obj.ajaxCall('yncwebpush.updateSkipTime', 'browser=' + yncwebpush.sBrowser,
            'post', null, function () {
                if (yncwebpush_params['iDelaySettingTime'] !== false) {
                    var iNewDelay = yncwebpush_params['iDelaySettingTime'] + 10;
                    yncwebpush.showRequestBanner(iNewDelay);
                }
            });
        return false;
    },
    detectBrowser: function () {
        var ua = navigator.userAgent, tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if (/trident/i.test(M[1])) {
            tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
            return 'IE';
        }
        if (M[1] === 'Chrome') {
            tem = ua.match(/\bOPR\/(\d+)/)
            if (tem != null) {
                return 'Opera';
            }
        }
        M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
        if ((tem = ua.match(/version\/(\d+)/i)) != null) {
            M.splice(1, 1, tem[1]);
        }
        return M[0];
    },
    getBrowserToken: function (bUpdateServer) {
        yncwebpush.oMessaging.getToken()
            .then(function (currentToken) {
                if (currentToken) {
                    if (bUpdateServer) {
                        yncwebpush.handleGrantedToken(currentToken);
                    } else {
                        yncwebpush.checkTokenIsExpire(currentToken);
                    }
                    yncwebpush.sCurrentToken = currentToken;
                    setCookie('ync_web_push_token', JSON.stringify({
                        'token': yncwebpush.sCurrentToken,
                        'browser': yncwebpush.sBrowser
                    }));
                } else {
                    // Show permission request.
                    console.log('No Instance ID token available. Request permission to generate one.');
                    setCookie('ync_web_push_token', JSON.stringify({
                        'token': yncwebpush.sCurrentToken,
                        'browser': yncwebpush.sBrowser
                    }));
                    return false;
                }
            })
            .catch(function (err) {
                console.log('An error occurred while retrieving token. ', err);
                yncwebpush.sCurrentToken = false;
                setCookie('ync_web_push_token', JSON.stringify({
                    'token': yncwebpush.sCurrentToken,
                    'browser': yncwebpush.sBrowser
                }));
                return false;
            });
    },
    showRequestBanner: function (iWaitingTime) {
        if (typeof(yncwebpush.oMessaging) == 'object' && yncwebpush.oMessaging != null &&
            (yncwebpush.sBrowser == 'Chrome' || yncwebpush.sBrowser == 'Firefox') &&
            getParam('sController').match('admincp') === null && !yncwebpush.bShowRequest && yncwebpush.sCurrentToken !== false
        ) {
            console.log('Request banner show after: ' + iWaitingTime + ' secs');
            yncwebpush.bShowRequest = true;
            setTimeout(function () {
                $.ajaxCall('yncwebpush.showRequestBanner', 'token=' + yncwebpush.sCurrentToken + '&browser=' + yncwebpush.sBrowser, 'post');
            }, iWaitingTime * 1000);
        }
    },
    checkTokenIsExpire: function (sToken) {
        if (!sToken) return false;
        $Core.ajax('yncwebpush.checkTokenExpired', {
            type: 'POST',
            params: {
                token: sToken,
                browser: yncwebpush.sBrowser
            },
            success: function (sOutput) {
                var response = JSON.parse(sOutput);
                if (!response.token_valid) {
                    //Token is expired
                    yncwebpush.oMessaging.deleteToken(sToken);
                    yncwebpush.getBrowserToken(true);
                }
            }
        });
    }
};
$Ready(function () {
    if (yncwebpush.sBrowser == '') {
        yncwebpush.sBrowser = yncwebpush.detectBrowser();
    }
    if (typeof  navigator.serviceWorker != 'undefined' && !yncwebpush.initService && typeof firebase != 'undefined') {
        yncwebpush.initService = true;
        navigator.serviceWorker.register(yncwebpush_params['sWebPushPath'] + 'assets/jscript/firebase-messaging-sw.js?iSenderId=' + yncwebpush_params['iSenderId'])
            .then(function (registration) {
                // Registration was successful
                yncwebpush.oMessaging = firebase.messaging();
                yncwebpush.oMessaging.useServiceWorker(registration);
                yncwebpush.oMessaging.onMessage(function (payload) {
                    console.log('Received Message', payload);
                });
                //Check if this browser have granted permission
                if (getParam('sController').match('admincp') === null) {
                    yncwebpush.getBrowserToken(false);
                }
                //Show request banner
                yncwebpush.showRequestBanner(yncwebpush_params['iWaitingTime']);
            }, function (err) {
                // registration failed :(
                console.log('ServiceWorker registration failed: ', err);
            });
    }
});