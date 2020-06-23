;


<?php 
    $oYNChat->startWebSocketServer();
    $sSiteLink = Phpfox::getParam('core.path_file');
    $sApiUrl = $sSiteLink . 'module/ynchat/api.php/';
?>

/**
 * Created by lytk on 8/26/14.
 */
var totalWaitingMess = 0;
var YNWebSocket = function(url)
{
    var callbacks = {};
    var ws_url = url;
    var conn;
    this.bind = function(event_name, callback){
        callbacks[event_name] = callbacks[event_name] || [];
        callbacks[event_name].push(callback);
        return this;// chainable
    };

    this.send = function(event_name, event_data){
        this.conn.send( event_data );
        return this;
    };

    this.connect = function() {
        if ( typeof(MozWebSocket) == 'function' ){
            this.conn = new MozWebSocket(url);
        } else{
            this.conn = new WebSocket(url);
        }

        // dispatch to the right handlers
        this.conn.onmessage = function(evt){
            var oData = JSON.parse(evt.data);
            var action = null;
            if(undefined != oData.action && null != oData.action && oData.action.length > 0){
                action = oData.action;
            }
            var method = null;
            if(undefined != oData.method && null != oData.method && oData.method.length > 0){
                method = oData.method;
            }
            var data = null;
            if(undefined != oData.data && null != oData.data){
                data = oData.data;
            }

            switch (action){
                case 'echo':
                    dispatch('message', method, data);
                    break;
                default :
                    break;
            }
        };

        this.conn.onclose = function(){dispatch('close', null, null)}
        this.conn.onopen = function(){dispatch('open',null, null)}
    };

    this.disconnect = function() {
        if (null != this.conn) {
            this.conn.close();
            this.conn = null;
        }
    };

    this.reconnect = function() {
        if(null != this){
            this.disconnect();
        }

        this.connect();
    };

    this.getStatus = function() {
        if (null != this.conn) {
            return 'connect';
        }

        return 'disconnect';
    };

    var dispatch = function(event_name, method, message){
        var chain = callbacks[event_name];
        if(typeof chain == 'undefined') return; // no callbacks for this event
        for(var i = 0; i < chain.length; i++){
            chain[i](method, message )
        }
    }
};

if(typeof ynchat !== 'undefined'){
    if(ynchat.oConnection != null ){
        ynchat.oConnection.disconnect();
    }
}

var ynchat = {
	pt : []
    , bDebug : false
    , bRunning : false
    , bPasting : false
    , bFirstPasting : false
    , oConnection : null
    , parseByChattingBox : {}
    , lang : {}
    , config : {}
    , usersettings : {}
    , readStatus : {}
	, files: []
    , photos: []
    , isHttps: false
	, addFile: function(file) {
		ynchat.files.push(file);
	}
	,removeFile: function(index) {
		ynchat.files.splice(index, 1);
	}
	, getFiles: function() {
		return ynchat.files;
	}
    , setLang: function(key,value){
        ynchat.lang[key] = value;
    }
    , getLang: function(key){
        if (typeof ynchat.lang[key] != "undefined"){
            return ynchat.lang[key];
        }

        return '';
    }
    , setConfig: function(key,value){
        ynchat.config[key] = value;
    }
    , getConfig: function(key){
        if (typeof ynchat.config[key] != "undefined"){
            /*fix for IE*/
            if('sApiUrl' == key){
                if(ynchat.config[key].indexOf("////") != -1){
                    console.log('str replace');
                    ynchat.config[key] = ynchat.config[key].replace('////','//');
                }
            }
            /*fix for IE*/
            return ynchat.config[key];
        }

        return key;
    }
    , setUserSettings: function(key,value){
        ynchat.usersettings[key] = value;
    }
    , getUserSettings: function(key){
        if (typeof ynchat.usersettings[key] != "undefined"){
            return ynchat.usersettings[key];
        }

        return key;
    }
    , checkHttps: function(){
        if (window.location.protocol != "https:"){
            ynchat.isHttps = false;
        } else {
            ynchat.isHttps = true;
        }
    }
	, init: function()
	{
        ynchat.checkHttps();

        if(ynchat.bRunning == false){
            ynchat.initConnection();
        }

        ynchat.checkMobileView();
		ynchat.bindClickBuddyList();
        ynchat.bindEventOnSearchFriend();
		ynchat.bindOtherEvents();
		ynchat.friendListHeaderBindEvent();
        ynchat.getUnreadBox();

        jsynchat(window).resize(function () {            
            // ynchat.reloadCSS();

            if ( ynchat.isMobile() ) {
                // set min-height of content
                jsynchat('#ynchat-main-content').css('min-height', jsynchat(window).innerHeight() );
            } else {
                var hidetabs = jsynchat('#ynchat-hidetabs .chatboxuser');
                var hidelist = jsynchat('#ynchat-chatboxtabs #ynchat-hidelist');
                var length = hidetabs.length;
                for (var i = length-1 ; i >= 0; i--) {
                    var chatboxs = jsynchat('#ynchat-chatboxtabs .chatboxuser');
                    if (chatboxs.length > 0) {
                        var iframe = jsynchat('#ynchat-iframe-container');
                        var pos = jsynchat(chatboxs[chatboxs.length-1]).position();
                        if(null === pos){
                            break;
                        }
                        var right = jsynchat(window).width() - pos.left - jsynchat(chatboxs[chatboxs.length-1]).width();
                        var pos_remain = (jsynchat(iframe).hasClass('ynchat-floatleft')) ? right : pos.left;
                        if (pos_remain > 460) {
                            var id = jsynchat(hidetabs[i]).attr('data-userid');
                            var li = jsynchat('#hideuser_'+id);
                            var parent = jsynchat(li).parent();
                            li.remove();
                            jsynchat(hidetabs[i]).detach().insertBefore(hidelist);
                            ynchat.scrollToBottom('#ynchat-box-user-'+id + ' .ynchat-box-history', 100, 1000);
							ynchat.updateHideListNewMessage();
							ynchat.removeNewMessageInFriendsList(id);
                            if (jsynchat(parent).children().length == 0) {
                                jsynchat(jsynchat(parent).parent()).remove();
                            }
                        }
                        else {
                            break;
                        }
                    }
                }
                
                var chatboxs = jsynchat('#ynchat-chatboxtabs .chatboxuser');
                var length = chatboxs.length;
                var iframe = jsynchat('#ynchat-iframe-container');
                var pos = jsynchat(chatboxs[length - 1]).position();
                if(null !== pos){
                    var right = jsynchat(window).width() - pos.left - jsynchat(chatboxs[length - 1]).width();
                    var pos_remain = (jsynchat(iframe).hasClass('ynchat-floatleft')) ? right : pos.left;
                    while (length > 1 && pos_remain < 160) {
                        if (jsynchat(chatboxs[length - 1]).hasClass('focusedtab')) {
                            ynchat.moveToHide(chatboxs[length - 2]);
                        }
                        else ynchat.moveToHide(chatboxs[length - 1]);
                        chatboxs = jsynchat('#ynchat-chatboxtabs .chatboxuser');
                        length = chatboxs.length;
                        pos = jsynchat(chatboxs[length - 1]).position();
                        right = jsynchat(window).width() - pos.left - jsynchat(chatboxs[length - 1]).width();
                        pos_remain = (jsynchat(iframe).hasClass('ynchat-floatleft')) ? right : pos.right;
                    }    
                }                
            }
        });           

        // loop update friend list
        if(ynchat.bRunning == false){
            setInterval('ynchat.getUpdateFriendList();', ynchat.getConfig('iIntervalUpdateFriendList'));
            setInterval('ynchat.updateAgent();', 5 * 60 * 1000);
        }

        // if(ynchat.bRunning == false){
        //     ynchat.bRunning = true;
        // }

        jsynchat('html').click(function(e) {
            if(!jsynchat(e.target).closest('.ynopen').length) {
                ynchat.hideAllSettingMenuFriendList();
                ynchat.hideAllSettingMenuChatBox();
            }       
            if(!jsynchat(e.target).next('.active').length) {
                if(!jsynchat(e.target).closest('.panel').length) {
                    ynchat.hideAllEmoticonStickerPopup();
                }
                
            }       
        });

        // // DEFINE SOUNDS
        // jsynchat.mbAudio.sounds = {
        //     effectSprite: {
        //         id    : "effectSprite",
        //         ogg   : ynchat.getConfig('sSiteLink') + 'ynchat/static/media/notify.ogg',
        //         mp3   : ynchat.getConfig('sSiteLink') + 'ynchat/static/media/notify.mp3'
        //     }
        // };
	}
    , hideAllEmoticonStickerPopup: function(){
        jsynchat('#ynchat-iframe-container #ynchat-chatboxtabs .footerbar .emoticonsticker .panel').removeClass('active');
    }
    , hideAllSettingMenuFriendList: function(){
        jsynchat('#ynchat-iframe-container #ynchat-friend-list .setting .menu').removeClass('ynopen');
    }
    , hideAllSettingMenuChatBox: function(){
        jsynchat('#ynchat-iframe-container #ynchat-chatboxtabs .headerbar .buttonbar .setting .menu').removeClass('ynopen');
    }
    , initFrame: function(friendList, countOnine)
    {
        var PBDRContainer = document.getElementById('ynchat-iframe-container');

        if (PBDRContainer == null) {
            PBDRContainer = document.createElement("div");
            PBDRContainer.id = "ynchat-iframe-container";
            if(ynchat.getConfig('iPlacementOfChatFrame') == 1){
                PBDRContainer.className = "ynchat-floatright";
            } else if(ynchat.getConfig('iPlacementOfChatFrame') == 2){
                PBDRContainer.className = "ynchat-floatleft";
            }
            
            document.body.appendChild(PBDRContainer); 
        }    

        var sHtml = '';

        sHtml += '<div id="hiddenFieldBlock">';
            sHtml += '<div style="display: none;">';
                sHtml += '<div id="ynchatSound">';
                    sHtml += '<audio id="chatAudio"><source src="' + ynchat.getConfig('sSiteLink') + 'ynchat/static/media/notify.ogg" type="audio/ogg"><source src="' + ynchat.getConfig('sSiteLink') + 'ynchat/static/media/notify.mp3" type="audio/mpeg"><source src="' + ynchat.getConfig('sSiteLink') + 'ynchat/static/media/notify.wav" type="audio/wav"></audio>';
                sHtml += '</div>';
            sHtml += '</div>';
        sHtml += '</div>';

        sHtml += '<div id="ynchat-main-content">';
            sHtml += '<div id="ynchat-chatboxtabs">';
            sHtml += '</div>';
            sHtml += '<div id="ynchat-hidetabs" style="display:none"></div>';
            sHtml += '<div id="ynchat-friend-list" class="ynchat-hide">';
                sHtml += '<div class="headerbar">';
                    sHtml += '<div class="setting">';
                        sHtml += '<a href="#" class="btn-setting"></a>';
                        sHtml += '<div class="menu">';
                            sHtml += '<div>';
                                sHtml += '<ul>';
                                    if(ynchat.isMobile() == false || ynchat.isMobile() == null){
                                        sHtml += '<li data-action="advanced_settings">' + ynchat.getLang('advanced_settings') + '</li>';
                                    }
                                    if(ynchat.isMobile() == false || ynchat.isMobile() == null){
                                        sHtml += '<li data-action="close_all_tabs">' + ynchat.getLang('close_all_chat_tabs') + '</li>';
                                    }
                                    sHtml += '<li data-action="go_online_offline">' 
                                        if(ynchat.getUserSettings('iIsGoOnline')){
                                            sHtml += ynchat.getLang('go_offline');
                                        } else {
                                            sHtml += ynchat.getLang('go_online');
                                        }
                                    sHtml += '</li>';
                                    if(ynchat.isMobile() == false || ynchat.isMobile() == null){
                                        sHtml += '<li data-action="play_sound">' + ynchat.getLang('play_sound_on_new_message') + ': <span>'
                                            if(ynchat.getUserSettings('iIsNotifySound')){
                                                sHtml += ynchat.getLang('yes');
                                            } else {
                                                sHtml += ynchat.getLang('no');
                                            }
                                        sHtml += '</span></li>';
                                    }
                                sHtml += '</ul>';
                            sHtml += '</div>';
                        sHtml += '</div>';
                    sHtml += '</div>';
                    sHtml += '<div class="title">' + ynchat.getLang('chat') + ' (' + countOnine + ')</div>';
                sHtml += '</div>';

                sHtml += '<div class="bodybar">';
                    sHtml += '<div class="default">';
                        if(friendList.length > 0){
                            sHtml += '<ul>';
                                var idx = 0;
                                for(idx = 0; idx < friendList.length; idx ++){
                                    sHtml += '<li id="ynchat-friend-' + friendList[idx].user_id + '"';
                                    sHtml += 'data-user-id="' + friendList[idx].user_id + '"';
                                    sHtml += 'data-full-name="' + friendList[idx].full_name + '"';
                                    sHtml += 'data-user-name="' + friendList[idx].user_name + '"';
                                    sHtml += 'data-avatar="' + friendList[idx].avatar + '"';
                                    sHtml += 'data-link="' + friendList[idx].link + '"';
                                    sHtml += 'data-status="' + friendList[idx].status + '"';
                                    sHtml += '>';                                        
                                        sHtml += '<div class="avatar"><img src="' + friendList[idx].avatar + '" /></div>';
                                        sHtml += '<div class="user-status">'; 
                                            if(friendList[idx].status == 'available'){
                                                if(friendList[idx].agent == 'web'){
                                                    sHtml += ynchat.getLang('web'); 
                                                } else {
                                                    sHtml += ynchat.getLang('mobile'); 
                                                }
                                            }
                                        sHtml += '<div class="status ' + friendList[idx].status + '"></div></div>';
                                        sHtml += '<div class="name">' + friendList[idx].full_name + '</div>';                                        
                                    sHtml += '</li>';
                                }
                            sHtml += '</ul>';
                        } else {
                            sHtml += ynchat.getLang('nothing_friend_s_found');
                        }
                    sHtml += '</div>';
                    sHtml += '<div class="searching" style="display: none;">';
                        sHtml += '<img alt="" src="' + ynchat.getConfig('sSiteLink') + '/ynchat/static/image/add.gif">';
                    sHtml += '</div>';
                sHtml += '</div>';

                sHtml += '<div class="footerbar">';
                    sHtml += '<div class="wrap">';
                        sHtml += '<div class="innerwrap">';
                            sHtml += '<input id="searchFriend" placeholder="' + ynchat.getLang('search') + '" />';
                            sHtml += '<div style="display:none;" id="searchFriendResponse"></div>';
                        sHtml += '</div>';
                    sHtml += '</div>';
                sHtml += '</div>';
            sHtml += '</div>';
        sHtml += '</div>';

        PBDRContainer.innerHTML = sHtml;
        jsynchat('#ynchat-iframe-container #ynchat-main-content #ynchat-friend-list .bodybar .default ul li').tsort("", {order:'asc', attr:"data-status"}, {order:'asc', attr:"data-full-name"});
        
        // disable scroll
        ynchat.fixScrollingAction( jsynchat('#ynchat-iframe-container #ynchat-main-content #ynchat-friend-list .bodybar') );

        if ( ynchat.isMobile() ) {
            // set min-height of content
            jsynchat('#ynchat-main-content').css('min-height', window.innerHeight );
        }


        if($('#pf_admin').length){
            $('#ynchat-main-content').addClass('ynchat-admin-panel');

        }

        if($('.moderation_holder').length){
            $('#ynchat-main-content').addClass('ynchat-moderator');
        }
    }
    , initLangAndConfig: function()
    {
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl') + 'ynchat/initLangAndConfig',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "initLangAndConfig"
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                    for (x in oOutput.lang){
                        ynchat.setLang(x, oOutput.lang[x]);
                    }

                    for (y in oOutput.config){
                        ynchat.setConfig(y, oOutput.config[y]);
                    }

                    for (z in oOutput.usersettings){
                        ynchat.setUserSettings(z, oOutput.usersettings[z]);
                    }

                    ynchat.initFrame(oOutput.friendList, oOutput.countOnine);
                    ynchat.init();
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , initConnection: function()
    {
        var protocol = 'ws://';
        // if(ynchat.getConfig('bEnableSSL') == 1){
        //     protocol = 'wss://';
        // }
        var port = ynchat.getConfig('iPort');
        if(ynchat.isHttps){
            protocol = 'wss://';
            port = ynchat.getConfig('iStunnelPort');
        }
        // var serverUrl = protocol + ynchat.getConfig('sServerUrl') + ':' + ynchat.getConfig('iPort') + '/' + ynchat.getConfig('sAction') + '';
        if(ynchat.getConfig('sIpPublic').length > 0){
            var serverUrl = protocol + ynchat.getConfig('sIpPublic') + ':' + port + '/' + ynchat.getConfig('sAction') + '';
        } else {
            var serverUrl = protocol + ynchat.getConfig('sServerUrl') + ':' + port + '/' + ynchat.getConfig('sAction') + '';
        }
        if(ynchat.bDebug){
            console.log('serverUrl -- ' + serverUrl);
        }
        ynchat.oConnection = new YNWebSocket(serverUrl);

        //Let the user know we're connected
        ynchat.oConnection.bind('open', function() {
            ynchat.connectionOpen();
        });

        //OH NOES! Disconnection occurred.
        ynchat.oConnection.bind('close', function( data ) {
            ynchat.connectionClose(data);
        });

        //Log any messages sent from server
        ynchat.oConnection.bind('message', function( method, payload ) {
            // method is null -> using default method
            // otherwise      -> using method as function
            // payload can be text or object/array which depends method's purpose
            if(null == method){
                ynchat.connectionMessage(payload);
            } else {
                if(ynchat.bDebug){
                    console.log('method: ' + method);
                }
                ynchat[method](payload);
            }
        });

        ynchat.oConnection.connect();
    }
    /**
     * We do 2 actions in this method
     *      . if socket is disconnected -> re-connect
     *      . update friends list
     */
    , getUpdateFriendList: function()
    {
        /* re-connect */
        if(ynchat.bDebug){
            console.log('re-connect');
        }

        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            ynchat.handleInvalidConnection();
            ynchat.oConnection.reconnect();
        }
    }
    , connectionOpen: function()
    {
        ynchat.enableIframe();
        ynchat.authConnectionWithPlatform();
        ynchat.updateAgent();

        if(ynchat.bDebug){
            console.log('Connected.');
        }
    }
    , connectionClose: function(data)
    {
        ynchat.disableIframe();
        if(ynchat.oConnection != null){
            ynchat.oConnection.disconnect();
        }

        if(ynchat.bDebug){
            console.log('Disconnected.');
        }
    }
    , connectionMessage: function(payload)
    {
        if(ynchat.bDebug){
            console.log('connectionMessage - payload: ' + payload);
        }
    }
    , connectionSend: function(text)
    {
        ynchat.oConnection.send( 'message', text );
    }
    , handleInvalidConnection: function() {
        ynchat.disableIframe();
    }
    /* sometimes we disconnect with websocket-server, so we should disable iframe */
    , disableIframe: function() {
        ynchat.bRunning = false;
        jsynchat('#ynchat-iframe-container').addClass('ynchat-inactive');
        if ( !jsynchat('.ynchat-inactive-text').length ) {
            jsynchat('#ynchat-friend-list .bodybar').append('<div class="ynchat-inactive-text">' + ynchat.getLang('unable_to_connect_to_chat_check_your_internet_connection') + '</div>');    
        }        

        if(ynchat.isMobile()){
            ynchat.mobileViewHide();
        }
    }
    /* we enable iframe when connection is valid  */
    , enableIframe: function() {
        ynchat.bRunning = true;
        jsynchat('#ynchat-iframe-container').removeClass('ynchat-inactive');
        jsynchat('.ynchat-inactive-text').remove();
    }
    , reloadCSS: function() {
        var ynchat_link_css_mobile = jsynchat('#ynchat_link_css_mobile');
            css_src = ynchat_link_css_mobile.attr('href');
        
        if ( ynchat.isMobile() ) {
            ynchat_link_css_mobile.attr('href', css_src + '1');    
        }        
        // document.body.appendChild(PBDRContainer); 
    }
    , isMobile: function() {
        // return parseInt(ynchat.getConfig('iIsMobile')) == 1 ? true : 0;

        var isMobile = {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };
        var result =  isMobile.any();
		if (result != null) return true; else return false; 
	}
    , checkMobileView: function() {

        if ( ynchat.isMobile() ) {
            var jsynchat_mobile = jsynchat('#ynchat-iframe-container');

            // mobile icon sHtml
            var sHtml = '';
            sHtml += '<div id="ynchat-mobileview-icon"';
            if(ynchat.getConfig('iPlacementOfChatFrame') == 2){
                sHtml += 'style="left:5px !important"><div id="ynchat_number_waiting_sender"></div>';
            }
            else{
                sHtml += '><div id="ynchat_number_waiting_sender"></div>';
            }
                sHtml += '<span class="ynchat-mobileview-notification"></span>';
            sHtml += '</div>';

            jsynchat_mobile.addClass('ynchat-mobileview');
            jsynchat_mobile.append( sHtml );

            // add icon minHtml
            var minHtml = '';
            minHtml = '<span id="btn-setting-minimize"></span>';

            jsynchat_mobile.find('#ynchat-friend-list .headerbar .setting').append( minHtml );

            if($('#pf_admin').length){
                jsynchat('#ynchat-mobileview-icon').addClass('ynchat-mobileview-panel');
            }
            
            jsynchat('#ynchat-mobileview-icon').click(function(){
                $("#ynchat_number_waiting_sender").html("");
                ynchat.mobileViewShow();
            });

            jsynchat('#btn-setting-minimize').click(function(){
                ynchat.mobileViewHide();            
            });

            // set viewport on MobileView
            var viewport = jsynchat('meta[name="viewport"]');
            if ( viewport.length ) {
                viewport.attr('content','width=device-width, initial-scale=1, user-scalable=no');                
            } else {
                jsynchat('head').append('<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">');
            }
        }        
    }
    , mobileViewShow: function(){
        jsynchat('body').addClass('show-ynchat-mobileview');
        jsynchat('#ynchat-iframe-container').addClass('show-mobilechat');
        if(ynchat.isMobile() 
            && jsynchat('body').hasClass('show-ynchat-mobileview')
            && jsynchat('#ynchat-iframe-container').hasClass('ynchat-box-chatting')){
            jsynchat('body').animate({
                scrollTop: jsynchat('body')[0].scrollHeight
            }, 1000);    

            jsynchat('#ynchat-iframe-container #ynchat-chatboxtabs').find('.chatboxuser').each(function(){
                var user_id = jsynchat(this).attr('data-userid')
                ynchat.updateStatusMessage(user_id);
                var bodybar = jsynchat(this).find('.bodybar');
                jsynchat(bodybar).animate({
                    scrollTop: jsynchat(bodybar)[0].scrollHeight
                }, 1000);    

            });
        }
    }
    , mobileViewHide: function(){
        jsynchat('body').removeClass('show-ynchat-mobileview');
        jsynchat('#ynchat-iframe-container').removeClass('show-mobilechat');
		jsynchat('#ynchat-iframe-container').removeClass('ynchat-box-chatting');
    }
    , showLoadingPanel: function()
    {
/*
        ynchat.hideError();
        jsynchat('#loadingPanel').show();
*/
    }
    , hideLoadingPanel: function()
    {
        /*jsynchat('#loadingPanel').hide();*/
    }
    , showError: function(error_message){
        /*jsynchat('#errorBlock').html('<span>' + error_message + '</span>');*/
    }
    , hideError: function(){
        /*jsynchat('#errorBlock').html('');*/
    }
    /*
        add sUserIdHash into data object
        return toString object
    */
    , generateSendingData: function(action, data)
    {
        if(undefined == data.sUserIdHash || null == data.sUserIdHash){
            data.sUserIdHash = ynchat.getConfig('sUserIdHash');
        }

        var genData =  {
            action: action
            , data: data
        };

        return JSON.stringify(genData);
    }
    /*
        this is request function, each request will have 1 response function
        we create object which is sent to websocket-server
              object contains:
                  - action : name of function which will be called on websocket-server with name is _acti<Action>
                  - data : object/array with any data we has sent to websocket-server
              we need to toString above object, because websocket=server accepts ONLY text
        we will put sUserIdHash which is used to authenticate into above object
        we call send method at the end
    */
    , authConnectionWithPlatform: function()
    {
        var action = 'authConnectionWithPlatform';
        var data = {};
        var sObject = ynchat.generateSendingData(action, data);
        ynchat.connectionSend(sObject);
    }
    /*
        this is response function, data can be string or object/array which depends method's purpose
        sometimes you do not do any actions in response method but we recommend this function should be created with empty
    */
    , authConnectionWithPlatformRes: function(oData)
    {
        // nothing to do
    }
    , sendMessageAsText: function(iReceiverId, sText, iMessageId, iStickerId)
    {
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }
        var action = 'sendMessageAsText';
        var data = {
            iReceiverId: iReceiverId
            , sText: sText
            , iMessageId: iMessageId
            , iStickerId: iStickerId
        };
        var sObject = ynchat.generateSendingData(action, data);
        ynchat.connectionSend(sObject);
    }
	
    , sendMessageAsTextRes: function(oData) {
		// display as receiver
		if (oData.iSenderId != ynchat.getUserSettings('iUserId')) {
			var ele = '#ynchat-box-user-' + oData.iSenderId + ' .footerbar .chatting';
            var eleParent = '#ynchat-box-user-' + oData.iSenderId + ' .ynchat-box-history';
            var oldHeight = 0;
            // draw chat box
            var bShouldAddMessage = ynchat.drawChatBoxByUserId(oData.iSenderId, true, oData, true, false);
            oldHeight = jsynchat(eleParent)[0].scrollHeight;
            if(bShouldAddMessage){
                oData.sText = ynchat.prepareDisplay(oData.sText);
                ynchat.displayIntoBoxHistoryAsReceiver(oData);
            } else {
                ynchat.__getOneMessage(oData.iSenderId, true, oData);
            }
			ynchat.playSoundNotify(oData.iSenderId);
			ele = '#ynchat-box-user-' + oData.iSenderId + ' .footerbar .chatting';
            eleParent = '#ynchat-box-user-' + oData.iSenderId + ' .ynchat-box-history';
            if(jsynchat(ele).is(":focus")){
                //input and text area has focus
                ynchat.scrollToBottom(eleParent, 100, 1000);
            }
			else {
				if (jsynchat('#ynchat-box-user-' + oData.iSenderId).hasClass('active')) {
					if (jsynchat(eleParent)[0].scrollHeight > jsynchat(eleParent).height()) {

						if (jsynchat(eleParent).scrollTop() + jsynchat(eleParent).innerHeight() < oldHeight) {
							if (jsynchat('#ynchat-box-user-' + oData.iSenderId + ' .newMessageNotice').length == 0){
								var newMess = jsynchat('<div />', {
									'class': 'newMessageNotice',
									text: 'Scroll down to see new messages.',
									click : function() {
										ynchat.scrollToBottom(eleParent, 100, 1000);
										jsynchat(this).remove();
										ynchat.removeNewMessageInFriendsList(oData.iSenderId);
									},
									css : {
										'z-index': '2009',
										'background': 'white',
										'position': 'fixed',
										'padding': '5px',
										'text-align': 'center',
										'width': '235px',
										'color': 'gray',
										'cursor': 'pointer'
									}
								}).prependTo(jsynchat('#ynchat-box-user-' + oData.iSenderId + ' .ynchat-box-history'));
							}
							ynchat.updateNewMessageInFriendsList(oData.iSenderId);
						}
						else {
							ynchat.scrollToBottom(eleParent, 100, 1000);
						}
					}
				}
				else {
					ynchat.updateNewMessage(oData.iSenderId);
				}
			}
		}
		// display as sender
		else {
			var bShouldAddMessage = ynchat.drawChatBoxByUserId(oData.iReceiverId, true, oData, false, false);
            oData.sText = ynchat.prepareDisplay(oData.sText);
			ynchat.displayIntoBoxHistoryAsSender(oData, false, false, false);
            var eleParent = '#ynchat-box-user-' + oData.iReceiverId + ' .ynchat-box-history';
            ynchat.scrollToBottom(eleParent, 100, 1000);
		}
    }
	, bindClickBuddyList: function()
	{
        jsynchat('#ynchat-main-content #ynchat-friend-list .bodybar .default li').click(function(e)
	    { 
            var iUserId = jsynchat(this).data('user-id');
            ynchat.selectFriendInList(iUserId, false, null);
	    });
	}
    , showLoadingSearchingFriend: function()
    {
        jsynchat('#ynchat-main-content #ynchat-friend-list .bodybar .searching').show();
    }
    , hideLoadingSearchingFriend: function()
    {
        jsynchat('#ynchat-main-content #ynchat-friend-list .bodybar .searching').hide();
    }
    , startSearchingFriend: function()
    {
        // show loading
        ynchat.showLoadingSearchingFriend();

        // hide default friend list
        jsynchat('#ynchat-main-content #ynchat-friend-list .bodybar .default').hide();
    }
    , endSearchingFriend: function()
    {

        ynchat.hideLoadingSearchingFriend();

        if(jsynchat('#ynchat-main-content #ynchat-friend-list .footerbar #searchFriend').val() == ''){
            jsynchat('#ynchat-main-content #ynchat-friend-list .bodybar .default').show();
        }
    }
    , fixScrollingAction: function(element) {
        element.bind( 'mousewheel DOMMouseScroll', function ( e ) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            
            this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
            e.preventDefault();
        });
    }    
    , callbackSearchingFriend: function(obj)
    {
        // add new row in friend list if not existing
        if(undefined != obj.user_id && null != obj.user_id){
            ynchat.__addNewFriendIntoFriendList(obj);
        }

        // draw box chat
        jsynchat('#ynchat-friend-' + obj.user_id).trigger( "click" );

        // end
        ynchat.endSearchingFriend();
    }
    , bindEventOnSearchFriend: function()
    {
        var options = {
            script: ynchat.getConfig('sApiUrl')+ 'ynchat/searchFriend?json=true&',
            varname:"input",
            json:true,						// Returned response type
            shownoresults:true,				// If disable, display nothing if no results
            noresults: ynchat.getLang('nothing_friend_s_found'),			// String displayed when no results
            maxresults: ynchat.getConfig('iNumberOfFriendList'),					// Max num results displayed
            cache:false,					// To enable cache
            minchars:1,						// Start AJAX request with at leat 1 chars
            timeout:100000,					// AutoHide in XX ms
            eleParent: '#ynchat-iframe-container #ynchat-main-content #ynchat-friend-list .bodybar',                 // AutoHide in XX ms
            callback: function (obj) { 		// Callback after click or selection
                ynchat.callbackSearchingFriend(obj);
            }
        };
        // Init autosuggest
        var as_json = new bsn.AutoSuggest('searchFriend', options);
    }
    , bindOtherEvents: function()
    {
        jsynchat(document).mouseup(function (e)
        {
            var container = jsynchat("#ynchat-chatboxtabs");

            if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
            {
                ynchat.removeFocusClassAllChatBoxes();
            }
        });

        // blur for Firefox and Chrome and focusout form IE
        jsynchat(document).bind('blur', function () {
            ynchat.removeFocusClassAllChatBoxes();
        });

        // end searching friend when value is empty
        jsynchat("#searchFriend").keyup(function() {

            if (!this.value) {
                ynchat.endSearchingFriend();
            }

        });
    }
    , displayIntoBoxHistoryAsReceiver: function(oData)
    {
        if(ynchat.bDebug){
            console.log(oData.sText);
        }
        var elId = '#ynchat-friend-' + oData.iSenderId;
        if(jsynchat(elId).length > 0){
            var sFullName = jsynchat(elId).data('full-name');
            var sUserName = jsynchat(elId).data('user-name');
            var sAvatar = jsynchat(elId).data('avatar');
            var sLink = jsynchat(elId).data('link');
            var sBoxId = "ynchat-box-user-" + oData.iSenderId;
            var element = '#' + sBoxId + ' .ynchat-box-history';
            var sHtml = '';

            sHtml += '<div class="line-message" data-reactid="' + oData.iTimeStamp + '_' + oData.iMessageId + '" >';
                sHtml += '<div>';
                    sHtml += '<div class="message">';
                        sHtml += '<span class="message-name">';
                            sHtml += '<a href="' + sLink + '" target="_blank"><image alt="' + sFullName + '" src="' + sAvatar + '" /></a>';
                        sHtml += '</span>';
                        sHtml += '<span class="message-content">';
                            switch(oData.type){
                                case 'link': 
                                case 'video': 
                                    var attachment = JSON.parse(oData.data);
                                    sHtml += '<div>';
                                        sHtml += '<div>';
                                            sHtml += '<span>' + oData.sText + '</span>';
                                            if('video' == oData.type){
                                                sHtml += '<a class="ynchat-playvideo" onclick="ynchat.showVideoInPopup(this); return false;" data-iframe="' + attachment.iframe + '" data-width="' + attachment.widthIframe + '" data-height="' + attachment.heightIframe + '"></a>';
                                            }
                                        sHtml += '</div>';
                                        sHtml += '<div class="ynchat-link">';
                                            sHtml += '<div>';
                                                sHtml += '<div>';
                                                    sHtml += '<a target="_blank" href="' + ynhelper.base64_decode(attachment.url) + '" >';
                                                        sHtml += '<img src="' + ynhelper.base64_decode(attachment.imageUrl) + '" alt="">';
                                                    sHtml += '</a>';
                                                sHtml += '</div>';
                                                sHtml += '<div>';
                                                    sHtml += '<div>';
                                                        sHtml += '<a href="' + ynhelper.base64_decode(attachment.url) + '" target="_blank" >' + (attachment.title) + '</a>';
                                                    sHtml += '</div>';
                                                    sHtml += '<div>';
                                                        sHtml += ynhelper.base64_decode(attachment.url);
                                                    sHtml += '</div>';
                                                sHtml += '</div>';
                                            sHtml += '</div>';
                                        sHtml += '</div>';
                                    sHtml += '</div>';
                                    break;
                                case 'file': 
									sHtml += oData.sText;
                                    var upload_files = JSON.parse(oData.data);
                                    for (var i = 0; i < upload_files.length; i++) {
                                        var url = ynchat.getConfig('sApiUrl') + 'ynchat/download?id=' + upload_files[i].id;
                                        if (upload_files[i].type && upload_files[i].type.substring(0, 5) == 'image') {
                                            sHtml += '<div class="link_image"><img onclick="ynchat.showPhotoInPopup(this); return false;" data-url="'+url+'" src="'+url+'"/></div>';
                                        }
                                        sHtml += '<p><a href='+url+' download="'+upload_files[i].name+'" style="color:#333;" target="_blank">'+upload_files[i].name+'</a></p>';
                                    }
                                    
                                    break;
                                default: 
                                    sHtml += oData.sText;
                                    break;
                            }                   
                        sHtml += '</span>';
                    sHtml += '</div>';
                sHtml += '</div>';
            sHtml += '</div>';

            jsynchat( element ).append( sHtml );

            // update unread status
            ynchat.readStatus[oData.iSenderId] = false;            
        } else {
            // context: userA sends to userB, but list of userB's friends does not have userA
            // flow:
            //      . update friends list of userB, after that we callback that function
            // we do NOT use this logic currently, because we show all friends (offline/online)
        }   
    }
    , displayIntoBoxHistoryAsSender: function(oData, parse, sticker, checkban)
    {
        var sBoxId = "ynchat-box-user-" + oData.iReceiverId;
        var element = '#' + sBoxId + ' .ynchat-box-history';
        var sHtml = '';
        var text = oData.sText;
        if(oData.iMessageId == 0 && undefined != oData.sType && null != oData.sType && 'separator' == oData.sType){
            // add separator
            sHtml += '<div class="line-message" data-reactid="' + oData.iTimeStamp + '_' + oData.iMessageId + '" >';
                sHtml += '<div>';
                    sHtml += '<div class="message">';
                        sHtml += '<span class="message-break">';
                            sHtml += text;
                        sHtml += '</span>';
                    sHtml += '</div>';
                sHtml += '</div>';
            sHtml += '</div>';
        } else {
            if(parse == true){
                if(sticker !== false){
                    text = ynchat.parseSticker(text, sticker);
                } else {
                    if(undefined != checkban && null != checkban && true == checkban){
                        text = ynchat.checkBan(text, 'word');
                    }

                    text = ynhelper.htmlspecialchars(text);
                    text = ynhelper.nl2br(text);
                }
            }

            text = ynchat.parseEmoticon(text);
            sHtml += '<div class="line-message" data-reactid="' + oData.iTimeStamp + '_' + oData.iMessageId + '" >';
                sHtml += '<div>';
                    sHtml += '<div class="message message-owner">';
                        sHtml += '<span class="message-content">';
                            switch(oData.type){
                                case 'link': 
                                case 'video': 
                                    var attachment = JSON.parse(oData.data);
                                    sHtml += '<div>';
                                        sHtml += '<div>';
                                            sHtml += '<span>' + text + '</span>';
                                            if('video' == oData.type){
                                                sHtml += '<a class="ynchat-playvideo" onclick="ynchat.showVideoInPopup(this); return false;" data-iframe="' + attachment.iframe + '" data-width="' + attachment.widthIframe + '" data-height="' + attachment.heightIframe + '"></a>';
                                            }
                                        sHtml += '</div>';
                                        sHtml += '<div class="ynchat-link">';
                                            sHtml += '<div>';
                                                sHtml += '<div>';
                                                    sHtml += '<a target="_blank" href="' + ynhelper.base64_decode(attachment.url) + '" >';
                                                        sHtml += '<img src="' + ynhelper.base64_decode(attachment.imageUrl) + '" alt="">';
                                                    sHtml += '</a>';
                                                sHtml += '</div>';
                                                sHtml += '<div>';
                                                    sHtml += '<div>';
                                                        sHtml += '<a href="' + ynhelper.base64_decode(attachment.url) + '" target="_blank" >' + (attachment.title) + '</a>';
                                                    sHtml += '</div>';
                                                    sHtml += '<div>';
                                                        sHtml += ynhelper.base64_decode(attachment.url);
                                                    sHtml += '</div>';
                                                sHtml += '</div>';
                                            sHtml += '</div>';
                                        sHtml += '</div>';
                                    sHtml += '</div>';
                                    break;
                                case 'file': 
									sHtml += text;
                                    var upload_files = JSON.parse(oData.data);
                                    for (var i = 0; i < upload_files.length; i++) {
                                        var url = ynchat.getConfig('sApiUrl') + 'ynchat/download?id=' + upload_files[i].id;
                                        if (upload_files[i].type && upload_files[i].type.substring(0, 5) == 'image') {
                                            sHtml += '<div class="link_image"><img onclick="ynchat.showPhotoInPopup(this); return false;" data-url="'+url+'" src="'+url+'"/></div>';
                                        }
                                        sHtml += '<p><a href='+url+' download="'+upload_files[i].name+'" style="color:#333;" target="_blank">'+upload_files[i].name+'</a></p>';
                                    }
                                    
                                    break;
                                default:
                                    sHtml += text;
                                    break;
                            }					
                        sHtml += '</span>';
                    sHtml += '</div>';
                sHtml += '</div>';
            sHtml += '</div>';
        }

        jsynchat( element ).append( sHtml );
    }
    , moveToHide: function(chatbox) {
        var hideList = jsynchat( "#ynchat-chatboxtabs #ynchat-hidelist ul" );
        if (jsynchat(hideList).length == 0) {
            hideList = jsynchat('<div />', {
                id: 'ynchat-hidelist',
                css: {
                    'width': '40px'
                },
            }).append(jsynchat('<div />', {
                text: '',
                css: {
                    'width': '40px',
                    'height': '20px'
                },
                click: function(){
                    var ul = jsynchat(this).next();
                    if (jsynchat(ul).css('display') == 'none') {
                        jsynchat(ul).show();
                    }
                    else {
                        jsynchat(ul).hide();
                    }
                }
            })).append(jsynchat('<ul />', {
                css: {
                    'position': 'absolute',
                    'bottom': '20px',
                    'background': 'white',
                    'display': 'none',
                    'min-width': '100px',
                }
            }));
            jsynchat( "#ynchat-chatboxtabs" ).append(hideList);
            hideList = jsynchat( "#ynchat-chatboxtabs #ynchat-hidelist ul" );
        }
        var user_id = jsynchat(chatbox).attr('data-userid');
        var user_name = jsynchat('#ynchat-friend-'+user_id).data('full-name');
        var hideItem = jsynchat('<li />', {
            'id': 'hideuser_'+user_id,
            'value': user_id,
            text: user_name,
            click: function() {
                var chatboxs = jsynchat('#ynchat-chatboxtabs .chatboxuser');
				var chatbox = jsynchat('#ynchat-box-user-'+user_id);
                var chatboxslength = chatboxs.length;
                if (chatboxslength > 0) {
                    chatbox.detach().insertBefore(jsynchat('#ynchat-hidelist'));   
					if (!chatbox.hasClass('active')) {
						ynchat.boxChatHeaderBarClick(user_id);
					}
					ynchat.scrollToBottom('#ynchat-box-user-'+user_id + ' .ynchat-box-history', 100, 1000);
                    ynchat.moveToHide(chatboxs[chatboxslength-1]);
                    jsynchat(this).remove();
					ynchat.updateHideListNewMessage();
					ynchat.removeNewMessageInFriendsList(user_id);
                    if (jsynchat(parent).children().length == 0) {
                        jsynchat(jsynchat(parent).parent()).remove();
                    }
                }
            }
        }).append(jsynchat('<span />', {
            'text': '',
            'class': 'delete-hidelist',
            'click': function(){
                var parent = jsynchat(this).parent();
                var parent_parent = jsynchat(parent).parent();
                var id = jsynchat(parent).val();
                jsynchat(parent).remove();
				ynchat.updateHideListNewMessage();
				ynchat.removeNewMessageInFriendsList(user_id);
                jsynchat('#ynchat-hidetabs #ynchat-box-user-'+id).remove();
                if (jsynchat(parent_parent).children().length == 0) {
                    jsynchat(jsynchat(parent_parent).parent()).remove();
                }
            }
        })).appendTo(hideList);
        
        jsynchat(chatbox).detach().appendTo(jsynchat('#ynchat-hidetabs'));

        var countNewMessage = jsynchat(chatbox).find('.count-new-message');
        if (countNewMessage.length > 0) {
            var count = jsynchat(countNewMessage[0]).text();
            countNewMessage = jsynchat('<span />', {
                'class': 'count-new-message',
                text: count,
                css: {
                    'padding': '3px',
                    'background': 'red',
                    'color': 'white'
                }
            }).prependTo(hideItem);
            ynchat.updateHideListNewMessage();
        } 

		var newMessageNotice = jsynchat(chatbox).find('.newMessageNotice');
        if (newMessageNotice.length > 0) {
            var count = jsynchat('#ynchat-friend-'+user_id + ' .count-new-message:eq(0)').text();
            countNewMessage = jsynchat('<span />', {
                'class': 'count-new-message',
                text: count,
                css: {
                    'padding': '3px',
                    'background': 'red',
                    'color': 'white'
                }
            }).prependTo(hideItem);
            ynchat.updateHideListNewMessage();
        }
    }    
    , updateUserBoxSetting: function(iUserId, sType){
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/updateUserBoxSetting',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "updateUserBoxSetting"
                , iUserId: iUserId
                , sType: sType
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){},
            error: function(x, t, m) {}
        });
    }
    , drawChatBoxByUserId: function(iUserId, bReceiver, oExtra, bUpdateStatus, bFocus)
    {
        var bShouldAddMessage = true;   // true: add ONE msg, false: get MORE msg
        var elId = '#ynchat-friend-' + iUserId;
        if(jsynchat(elId).length > 0){
            ynchat.updateUserBoxSetting(iUserId, 'open');

            // existing in friend list
            if(undefined != bUpdateStatus && bUpdateStatus){
                jsynchat(elId + ' .status').removeClass('offline');
                jsynchat(elId + ' .status').addClass('available');
            }
            var sFullName = jsynchat(elId).data('full-name');
            var sUserName = jsynchat(elId).data('user-name');
            var sLink = jsynchat(elId).data('link');

            // create html-container of chat box
            var sBoxId = "ynchat-box-user-" + iUserId;
            if(jsynchat('#' + sBoxId).length == 0){
                bShouldAddMessage = false;
                var sHtml = '';
                sHtml += '<div id="' + sBoxId + '" class="chatboxuser" data-userid="' + iUserId + '">';
                    sHtml += '<div class="outer">';
                        sHtml += '<div class="inner">';
                            sHtml += '<div class="headerbar">';
                                sHtml += '<div class="buttonbar">';
                                    sHtml += '<div class="ynchat-close">';
                                        sHtml += '<a href="#">close';
                                        sHtml += '</a>';
                                    sHtml += '</div>';
                                    sHtml += '<div class="setting">';
                                        sHtml += '<a href="#" class="btn-setting"></a>';
                                        sHtml += '<div class="menu">';
                                            sHtml += ynchat.boxButtonBarGetFormSetting(iUserId);
                                        sHtml += '</div>';
                                    sHtml += '</div>';
                                sHtml += '</div>';
                                sHtml += '<div class="title">';
                                    sHtml += '<a href="#" data-href="' + sLink + '">' + sFullName + '</a>';
                                sHtml += '</div>';
                            sHtml += '</div>';
							
							//add event function for drag drop upload files.
                            sHtml += '<div class="bodybar ynchat-box-history" draggable ondrop="ynchat.dropFiles(event, this)" ondragover="ynchat.dragoverFiles(event)" ondragenter="ynchat.dragenterFiles(event, this)" onscroll="ynchat.checkNewMessage(this)">';
                            
							sHtml += '</div>';
                            sHtml += '<div class="footerbar ynchat-box-actions">';
                                sHtml += '<div class="chat-content"><textarea class="chatting"></textarea></div>';
                                sHtml += '<div class="chat-content-attach">';
                                    if(ynchat.getConfig('bIsEnablePhotoAction')){
										//add a div to load upload photos
										//change event callback function
                                        sHtml += '<div class="photo" title="' + ynchat.getLang('upload_photo') + '"> <div class="ynchat-button" onclick="ynchat.uploadFilesClick(this, ' + iUserId + ')"></div> '
										sHtml += '<input type="file" class="upload_input" onchange="ynchat.uploadPhotosExec(this, ' + iUserId + ')" multiple style="position:absolute;left:-999px;width:0px;height:0px;" accept="image/*"/>'
										sHtml += '<div class="panel"></div></div>';
                                    }
                                    if(ynchat.getConfig('bIsEnableEmoticonStickerAction')){
                                        sHtml += '<div title="' + ynchat.getLang('choose_a_sticker_or_emoticon') + '" class="emoticonsticker"> <div class="ynchat-button" ></div> <div class="panel"></div></div>';
                                    }
                                sHtml += '</div>';
                            sHtml += '</div>';
                            sHtml += '<div class="chat-attachment">';
                                sHtml += '<div class="ynchat-loading"><img src="' + ynchat.getConfig('sSiteLink') + 'ynchat/static/image/add.gif" alt="Please Wait"/></div>';
                                sHtml += '<div class="ynchat-content"></div>';
                            sHtml += '</div>';
                        sHtml += '</div>';
                    sHtml += '</div>';
                sHtml += '</div>';

                var chatBoxs = jsynchat("#ynchat-chatboxtabs .chatboxuser");
                var chatBoxsLength = chatBoxs.length;
                if (chatBoxsLength > 0) {
					var iframe = jsynchat('#ynchat-iframe-container');
                    var pos = jsynchat(chatBoxs[chatBoxsLength-1]).position();
					var right = jsynchat(window).width() - pos.left - jsynchat(chatBoxs[chatBoxsLength-1]).width();
					var pos_remain = (jsynchat(iframe).hasClass('ynchat-floatleft')) ? right : pos.left;
                    if (pos_remain < 460) {
						if (!jsynchat(chatBoxs[chatBoxsLength-1]).hasClass('focusedtab')) {	
							jsynchat( sHtml ).insertBefore( jsynchat(chatBoxs[chatBoxsLength-1]) );
							ynchat.moveToHide(chatBoxs[chatBoxsLength-1]);
						}
						else {
							if (chatBoxsLength > 1) {
								jsynchat( sHtml ).insertBefore( jsynchat(chatBoxs[chatBoxsLength-2]) );
								ynchat.moveToHide(chatBoxs[chatBoxsLength-2]);
							}
							else {
								ynchat.moveToHide(sHtml);
							}
						}
                    }
                    else {
                        var hideList = jsynchat( "#ynchat-chatboxtabs #ynchat-hidelist" );
                        if (hideList.length > 0) {
                            jsynchat( sHtml ).insertBefore( hideList );
                        }
                        else jsynchat( "#ynchat-chatboxtabs" ).append( sHtml );
                    }
                } else {
                    var hideList = jsynchat( "#ynchat-chatboxtabs #ynchat-hidelist" );
                    if (hideList.length > 0) {
                        jsynchat( sHtml ).insertBefore( hideList );
                    }
                    else jsynchat( "#ynchat-chatboxtabs" ).append( sHtml );
                }
                ynchat.parseByChattingBox['#' + sBoxId] = true;                

                // bind click emoticon button 
                jsynchat('#' + sBoxId + ' .headerbar .title a').click(function(e) {
                    window.location.href = jsynchat(this).data('href');
                    e.stopPropagation();
                });
                jsynchat(function() {
                    jsynchat('#' + sBoxId + ' .ynchat-box-actions .chatting').keyup(function() {
                        var pos = 0;
                        if (this.selectionStart) {
                            pos = this.selectionStart;
                        } else if (document.selection) {
                            this.focus();

                            var r = document.selection.createRange();
                            if (r == null) {
                                pos = 0;
                            } else {

                                var re = this.createTextRange(),
                                rc = re.duplicate();
                                re.moveToBookmark(r.getBookmark());
                                rc.setEndPoint('EndToStart', re);

                                pos = rc.text.length;
                            }
                        }
                        var sBoxId_height = this.value.substr(0, pos).split("\n").length*18;
                        jsynchat(this).css('height', sBoxId_height );
                        if (sBoxId_height > 50) {
                            jsynchat('#' + sBoxId + ' .chat-content-attach').css('right', '17px');
                        } else {
                            jsynchat('#' + sBoxId + ' .chat-content-attach').css('right', '0');
                        }
                    });
                });

                // disable scroll
                ynchat.fixScrollingAction( jsynchat('#' + sBoxId + ' .bodybar') );

                /* bind parse link event in chat textarea */
                jsynchat('#' + sBoxId + ' .ynchat-box-actions .chatting').liveUrl({
                    apiKeyEmbedly : ynchat.getConfig('sApiKeyEmbedly'),
                    eleIdContainer : '#' + sBoxId,
                    subKeyUp : ynchat.subKeyupChattingTextarea,
                    subPaste : ynchat.subPasteChattingTextarea,
                    loadStart : function(){
                        jsynchat('#' + sBoxId + ' .chat-attachment .ynchat-loading').show();
                    },
                    loadEnd : function(){
                        jsynchat('#' + sBoxId + ' .chat-attachment .ynchat-loading').hide();
                    },
                    success : function(data){
                        if(true == ynchat.parseByChattingBox[this.eleIdContainer]){

                            var title, subtitle, description, url, imageUrl, iframe = '';
                            var media = null;
                            var widthIframe, heightIframe = 0;
                            if(undefined != data.title && null != data.title){
                                title = data.title;
                            }
                            if(undefined != data.provider_display && null != data.provider_display){
                                subtitle = data.provider_display;
                            }
                            if(undefined != data.description && null != data.description){
                                description = data.description;
                            }
							else {
								description = '';
							}
                            if(undefined != data.url && null != data.url){
                                url = data.url;
                            }
                            if(undefined != data.images && null != data.images && data.images.length > 0){
                                imageUrl = data.images[0].url;
                            }
							else {
								imageUrl = '';
							}
							
                            if(undefined != data.media && null != data.media && data.media.type == 'video'){
                                media = data.media;
                                widthIframe = media.width;
                                heightIframe = media.height;
                                var width = 'width="' + media.width + '"';
                                var height = 'height="' + media.height + '"';
                                iframe = media.html;
                            }

                            $sHtml = '';
                            $sHtml += '<div>';
                                $sHtml += '<div class="ynchat-attachment-close">close</div>';
                                $sHtml += '<div class="ynchat-attachment">';
                                    $sHtml += '<div>';
                                        $sHtml += '<div class="ynchat-image">';
											if (imageUrl != '')
												$sHtml += '<img alt="" src="' + imageUrl + '">';
                                        $sHtml += '</div>';
                                        $sHtml += '<div class="ynchat-titledesc">';
                                            $sHtml += '<div class="ynchat-title">';
                                                $sHtml += '<span>' + title + '</span>';
                                            $sHtml += '</div>';
                                            $sHtml += '<div class="ynchat-subtitle">';
                                                $sHtml += '<span>' + subtitle + '</span>';
                                            $sHtml += '</div>';
											if (description != '') {
												$sHtml += '<div class="ynchat-summary">';
													$sHtml += '<span>' + description + '</span>';
												$sHtml += '</div>';
											}
                                        $sHtml += '</div>';
                                    $sHtml += '</div>';
                                    $sHtml += '<input type="hidden" id="title" value="' + title + '" />';
                                    $sHtml += '<input type="hidden" id="subtitle" value="' + subtitle + '" />';
									if (description != '')
										$sHtml += '<input type="hidden" id="description" value="' + description + '" />';
                                    $sHtml += '<input type="hidden" id="url" value="' + ynhelper.base64_encode(url) + '" />';
									if (imageUrl != '')
										$sHtml += '<input type="hidden" id="imageUrl" value="' + ynhelper.base64_encode(imageUrl) + '" />';
                                    $sHtml += '<input type="hidden" id="iframe" value="' + ynhelper.base64_encode(iframe) + '" />';
                                    $sHtml += '<input type="hidden" id="widthIframe" value="' + widthIframe + '" />';
                                    $sHtml += '<input type="hidden" id="heightIframe" value="' + heightIframe + '" />';
                                $sHtml += '</div>';
                            $sHtml += '</div>';
                            jsynchat('#' + sBoxId + ' .chat-attachment .ynchat-content').html($sHtml);

                            jsynchat('#' + sBoxId + ' .chat-attachment .ynchat-attachment-close').click(function(){
                                jsynchat('#' + sBoxId + ' .chat-attachment .ynchat-content').html('');
                                jsynchat('#' + sBoxId + ' .ynchat-box-actions .chatting').trigger('clear');
                            });
                        } else {
                            this.objCore.init();
                        }
                        ynchat.parseByChattingBox[this.eleIdContainer] = true;
                    }
                });

                /* bind press Enter button */
                jsynchat('#' + sBoxId + ' .ynchat-box-actions .chatting').keypress(function(e) {
                    if ( e.keyCode == 13 && !e.shiftKey) {
                        var sBoxId = "ynchat-box-user-" + iUserId;
                        ynchat.parseByChattingBox['#' + sBoxId] = false;
                        console.log('#' + sBoxId + ' .chat-attachment .ynchat-content');
                        if(jsynchat('#' + sBoxId + ' .chat-attachment .ynchat-content').html().length > 0){
							var upload_files = ynchat.getFiles();
							if (upload_files.length > 0) {
								var box_actions = jsynchat(this).closest('.ynchat-box-actions');
								var chat_attachment = jsynchat(box_actions).next('.chat-attachment');
                                var loadding = jsynchat(chat_attachment).find('.ynchat-loading')[0];
								var files_container = jsynchat(chat_attachment).find('.ynchat-content')[0];
								var files = [];
								var i = 0;
								var value = jsynchat(this).val();
								for (i = 0; i < upload_files.length; i++) {
									ynchat.readFile(iUserId, files, upload_files[i], value);
									
								}
								e.preventDefault();
                                jsynchat(loadding).show();
								jsynchat(this).val('');
								jsynchat(files_container).empty();
							}
							
							else {
								ynchat.sendMessageByAjax(iUserId);
                                ynchat.emptyChatting(iUserId);
							}
                        } else if ((jsynchat.trim(this.value).length)){
                            // display on box history
                            var iTimeStamp = 0;
                            if(jsynchat('#' + sBoxId + ' .ynchat-box-history div:last-child').length > 0){
                                iTimeStamp = jsynchat('#' + sBoxId + ' .ynchat-box-history div:last-child').data('reactid');
                            }
                            // send via socket
                            ynchat.sendMessageAsText(iUserId, jsynchat.trim(this.value), 0, 0);
                            ynchat.emptyChatting(iUserId);
							ynchat.hideAllEmoticonStickerPopup();
                        }
                    }
                });

                // bind focus event
                jsynchat('#' + sBoxId).click(function() {
                    ynchat.updateStatusMessage(iUserId);
                });

                // bind click emoticon button 
                jsynchat('#' + sBoxId + ' .footerbar .emoticonsticker .ynchat-button').click(function(e) {
                    ynchat.clickEmoticonStickerInBox(this, iUserId);
                    // e.stopPropagation();
                });
                // bind click add files menu
                jsynchat('#' + sBoxId + ' .headerbar .setting .menu input').click(function(e) {
                    e.stopPropagation();
                });

                // bind click event on header box chat
                jsynchat('#' + sBoxId + ' .headerbar').click(function(e) {
                    ynchat.boxChatHeaderBarClick(iUserId);
                });
                ynchat.boxChatHeaderBarClick(iUserId);

                jsynchat('#' + sBoxId + ' .headerbar .buttonbar .ynchat-close a').click(function(e) {
                    ynchat.boxButtonBarClickClose(iUserId);
                    e.stopPropagation();
                    return false;
                });
                jsynchat('#' + sBoxId + ' .headerbar .buttonbar .setting a').click(function(e) {
                    ynchat.hideAllEmoticonStickerPopup();
                    ynchat.boxButtonBarClickSetting(iUserId);
                    e.preventDefault();
                    e.stopPropagation();
                });
                jsynchat('#' + sBoxId + ' .headerbar .buttonbar .setting li').click(function(e) {
                    var action = jsynchat(this).data('action');

                    jsynchat('#' + sBoxId + ' .headerbar .buttonbar .setting .menu').removeClass('active');
                    switch (action){
                        case 'get_old_conversation':
                            ynchat.getOldConversation(iUserId, 'yesterday');
                            break;
                        case 'add_files':
                            ynchat.uploadFilesClick(this, ' + iUserId + ');
                            break;                            
                    }
                    e.stopPropagation();
                });
            } else {
                var parent = jsynchat('#' + sBoxId).parent();
                if (jsynchat(parent).attr('id') == 'ynchat-hidetabs') {
                    var chatboxs = jsynchat('#ynchat-chatboxtabs .chatboxuser');
                    var chatboxslength = chatboxs.length;
                    if (chatboxslength > 0) {
						if (jsynchat(chatboxs[chatboxslength-1]).hasClass('focusedtab')) {
							if (chatboxslength > 1) {
								jsynchat('#' + sBoxId).detach().insertBefore(chatboxs[chatboxslength-1]);
								jsynchat("#ynchat-hidelist #hideuser_"+iUserId).remove();
								ynchat.moveToHide(chatboxs[chatboxslength-2]);
							}
							else {
								ynchat.updateNewMessage(iUserId);
							}
						}
						else {
							ynchat.moveToHide(chatboxs[chatboxslength-1]);
							jsynchat('#' + sBoxId).detach().insertBefore('#ynchat-hidelist');
							jsynchat("#ynchat-hidelist #hideuser_"+iUserId).remove();
						}
                    }
                    
                }
                jsynchat('#' + sBoxId).addClass('active');
            }

            /* focus text box */
            if(bFocus){
                setTimeout(function()
                {
                    jsynchat('#' + sBoxId + ' .ynchat-box-actions .chatting').focus();
                    ynchat.updateStatusMessage(iUserId);
                }, 500);
            }

            // add class ynchat-box-chatting
            if ( !jsynchat('#ynchat-iframe-container').hasClass('ynchat-box-chatting') ) {
                jsynchat('#ynchat-iframe-container').addClass('ynchat-box-chatting');
            }
        } else {
            if(bReceiver){
                // get new message and sender's info
                ynchat.__getOneMessage(iUserId, bReceiver, oExtra);
                bShouldAddMessage = false;
            }
        }
        if ( ynchat.isMobile() ) {
            totalWaitingMess = $('#ynchat-chatboxtabs .chatboxuser').length + $('#ynchat-hidetabs .chatboxuser').length;
            if(parseInt(totalWaitingMess) > 0 ) {
                if(ynchat.getConfig('iPlacementOfChatFrame') == 2){
                    $("#ynchat_number_waiting_sender").html("<i style=\"right:-5px;left:auto\">"+totalWaitingMess+"</i>");
                }
                else{
                    $("#ynchat_number_waiting_sender").html("<i>"+totalWaitingMess+"</i>");
                }
            }
            else{
                $("#ynchat_number_waiting_sender").html("");
            }
        }
        return bShouldAddMessage;
    }
    , sendMessageByAjax: function(iUserId){
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }

        var sBoxId = "ynchat-box-user-" + iUserId;
        var eleAttachment = '#' + sBoxId + ' .chat-attachment .ynchat-content';
        var text = jsynchat('#' + sBoxId + ' .ynchat-box-actions .chatting').val();
        var title = jsynchat(eleAttachment).find('#title').val();
        var url = jsynchat(eleAttachment).find('#url').val();
        var imageUrl = jsynchat(eleAttachment).find('#imageUrl').val();
        var iframe = jsynchat(eleAttachment).find('#iframe').val();
        var widthIframe = jsynchat(eleAttachment).find('#widthIframe').val();
        var heightIframe = jsynchat(eleAttachment).find('#heightIframe').val();

        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/sendMessageByAjax',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "sendMessageByAjax"
                , iUserId: iUserId
                , text: text
                , title: title
                , url: url
                , imageUrl: imageUrl
                , iframe: iframe
                , widthIframe: widthIframe
                , heightIframe: heightIframe
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                    var sBoxId = '#ynchat-box-user-' + oOutput.iUserId;
                    jsynchat(sBoxId + ' .chat-attachment .ynchat-attachment-close').trigger('click');
                    ynchat.parseByChattingBox[sBoxId] = true;
					
                    ynchat.sendMessageAsText(oOutput.aMessage.iReceiverId, '', oOutput.aMessage.iMessageId, 0);
                }

                /* end */
            },
            error: function(x, t, m) {
                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                }
            }
        });
    }
    , subKeyupChattingTextarea: function(e, ele){
        if ( e.keyCode == 13 && !e.shiftKey && ynchat.bPasting == false) {
        }
        ynchat.bPasting = false;
    }
    , emptyChatting: function(userid){
        var ele = '#ynchat-box-user-' + userid + ' .footerbar .chatting';
        jsynchat(ele).val('');
        jsynchat(ele).html('');
        jsynchat(ele).removeAttr('style');
        jsynchat(ele).closest(".inner").find('.chat-attachment .ynchat-attachment-close').trigger('click');
        setTimeout(function()
        {
            jsynchat(ele).val('');
            jsynchat(ele).html('');
            jsynchat(ele).removeAttr('style');
        }, 100);        
        
    }
    , subPasteChattingTextarea: function(e, ele){
        ynchat.bPasting = true;
        if(ynchat.bFirstPasting == false){
            /* filter link (video, other link) */
            ynchat.bFirstPasting = true;
            /*e.preventDefault();*/
            var text = (e.originalEvent || e).clipboardData.getData('text/plain') || prompt('');
        }
    }
    , boxButtonBarClickClose: function(userid)
    {
        totalWaitingMess = parseInt(totalWaitingMess) - 1;
        if(parseInt(totalWaitingMess) > 0 ) {
            if(ynchat.getConfig('iPlacementOfChatFrame') == 2){
                $("#ynchat_number_waiting_sender").html("<i style=\"right:-5px;left:auto\">"+totalWaitingMess+"</i>");
            }
            else{
                $("#ynchat_number_waiting_sender").html("<i>"+totalWaitingMess+"</i>");
            }
        }
        else{
            $("#ynchat_number_waiting_sender").html("");
        }
        ynchat.updateUserBoxSetting(userid, 'close');
        var ele = '#ynchat-box-user-' + userid;
        if(jsynchat(ele).length > 0){
            jsynchat(ele).remove();
        }

        var chatboxs = jsynchat('#ynchat-chatboxtabs .chatboxuser');
        var hidelist = jsynchat('#ynchat-chatboxtabs #ynchat-hidelist');
        var hidetabs = jsynchat('#ynchat-hidetabs .chatboxuser');
        if (chatboxs.length > 0 && hidetabs.length > 0) {
			var iframe = jsynchat('#ynchat-iframe-container');
			var pos = jsynchat(chatboxs[chatboxs.length-1]).position();
			var right = jsynchat(window).width() - pos.left - jsynchat(chatboxs[chatboxs.length-1]).width();
			var pos_remain = (jsynchat(iframe).hasClass('ynchat-floatleft')) ? right : pos.left;
			if (pos_remain > 460) {
                var id = jsynchat(hidetabs[0]).attr('data-userid');
                ynchat.updateStatusMessage(id);
                var li = jsynchat('#hideuser_'+id);
                var parent = li.parent();
                li.remove();
                jsynchat(hidetabs[0]).detach().insertBefore(hidelist);
				ynchat.scrollToBottom('#ynchat-box-user-' + id + ' .ynchat-box-history', 100, 1000);
                if (jsynchat(parent).children().length == 0) {
                    jsynchat(jsynchat(parent).parent()).remove();
                }
            }
        }
        else if (hidetabs.length > 0) {
            var id = jsynchat(hidetabs[0]).attr('data-userid');
            ynchat.updateStatusMessage(id);
            var li = jsynchat('#hideuser_'+id);
            var parent = li.parent();
            li.remove();
            jsynchat(hidetabs[0]).detach().insertBefore(hidelist);
            if (jsynchat(parent).children().length == 0) {
                jsynchat(jsynchat(parent).parent()).remove();
            }
        }       

        // remove class ynchat-box-chatting
        if ( jsynchat('#ynchat-chatboxtabs > div').length == 0 ) {
            jsynchat('#ynchat-iframe-container').removeClass('ynchat-box-chatting');
        } 
    }
    , boxButtonBarClickSetting: function(userid)
    {
        var ele = '#ynchat-box-user-' + userid;
        var eleSettingMenu = '#ynchat-box-user-' + userid + ' .headerbar .buttonbar .setting .menu';
        if(jsynchat(ele).length > 0){
            if( jsynchat(eleSettingMenu).hasClass('ynopen') == false){
                jsynchat('.ynopen').removeClass('ynopen');
                jsynchat(eleSettingMenu).addClass('ynopen');
            } else {
                jsynchat(eleSettingMenu).removeClass('ynopen');
            }
        }
    }
    , boxChatHeaderBarClick: function(userid)
    {
        var eleBoxChat = "#ynchat-box-user-" + userid;
        jsynchat(eleBoxChat).toggleClass('active');
		if (jsynchat(eleBoxChat + ' .count-new-message').length > 0) {
			jsynchat(eleBoxChat + ' .count-new-message').remove();
			ynchat.scrollToBottom(eleBoxChat + ' .ynchat-box-history', 100, 1000);
			ynchat.removeNewMessageInFriendsList(userid);
		}
        ynchat.scrollToBottom(jsynchat(eleBoxChat + ' .bodybar'), 100, 1000);
    }
    , boxButtonBarGetFormSetting: function(userid){
        var sForm = '';
        sForm += '<div data-userid="' + userid + '">';
            sForm += '<ul>';
                sForm += '<li data-action="get_old_conversation">' + ynchat.getLang('view_old_conversation') + '</li>';
                if(!ynchat.isMobile()){
                    sForm += '<li data-action="add_files">' + ynchat.getLang('add_files') + '</li>';
                }                
                sForm += '<input type="file" class="upload_input" onchange="ynchat.uploadFilesExec(this, ' + userid + ')" multiple style="position:absolute;left:-999px;width:0px;height:0px;"/>'                
            sForm += '</ul>';
        sForm += '</div>';

        return sForm;
    }
    , getOldConversation: function(userid, type){
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/getOldConversation',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "getOldConversation"
                , iUserId: userid
                , sType: type
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                    ynchat.renderOldConversation(oOutput);
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , renderOldConversation: function(data){
        var sForm = '';
        var idx = 0;
        var aMessages = data.aMessages;
        var iFriendId = data.iUserId;
        var type = data.sType;
        sForm += '<div class="headeroldconversation padding-bottom">';
            sForm += '<span>'+ ynchat.getLang('show_message_from') + ':</span> ';
            if('yesterday' == type){
                sForm += '<b>'+ynchat.getLang('yesterday')+'</b>';
            } else {
                sForm += '<a href="#" onclick="ynchat.getOldConversation(' + iFriendId + ', \'yesterday\'); return false;">' + ynchat.getLang('yesterday') + '</a>';
            }
            if('week' == type){
                sForm += ' - <b>7 ' + ynchat.getLang('days') + '</b>';
            } else {
                sForm += ' - <a href="#" onclick="ynchat.getOldConversation(' + iFriendId + ', \'week\'); return false;">7 ' + ynchat.getLang('days') + '</a>';
            }
            if('month' == type){
                sForm += ' - <b>30 ' + ynchat.getLang('days') + '</b>';
            } else {
                sForm += ' - <a href="#" onclick="ynchat.getOldConversation(' + iFriendId + ', \'month\'); return false;">30 ' + ynchat.getLang('days') + '</a>';
            }
            if('quarter' == type){
                sForm += ' - <b>3 ' + ynchat.getLang('month') + '</b>';
            } else {
                sForm += ' - <a href="#" onclick="ynchat.getOldConversation(' + iFriendId + ', \'quarter\'); return false;">3 ' + ynchat.getLang('month') + '</a>';
            }
        sForm += '</div>';
        sForm += '<div class="bodyoldconversation">';
            if(aMessages.length > 0){
                var sDate = '';
                for(idx = 0; idx < aMessages.length; idx ++){
                    if(sDate != aMessages[idx].sDate){
                        sDate = aMessages[idx].sDate;
                        // insert separator 
                        sForm += '<div class="message-date"><span>';
                            sForm += sDate;
                        sForm += '</span></div>';
                    }
                    
                    if(aMessages[idx].iSenderId == iFriendId){
                        sForm += ynchat.displayIntoBoxOldConversationAsReceiver(aMessages[idx]);
                    } else {
                        sForm += ynchat.displayIntoBoxOldConversationAsSender(aMessages[idx]);
                    }
                }
            }
        sForm += '</div>';

        var ele = '#oldconversation_' + iFriendId;
        if(jsynchat(ele).length > 0){
            jsynchat(ele).html(sForm);
        } else {
            sForm = '<div id="oldconversation_' + iFriendId + '">' + sForm + '</div>';

            jsynchat('.ynopen').removeClass('ynopen');
            ynchat.openPopupModal(ynchat.getLang('message_history'), sForm, '500px', 'auto'); 

            ynchat.fixScrollingAction( jsynchat('.bodyoldconversation') );
        }
    }
    , displayIntoBoxOldConversationAsReceiver: function(oData)
    {
        var elId = '#ynchat-friend-' + oData.iSenderId;
        if(jsynchat(elId).length > 0){
            var sFullName = jsynchat(elId).data('full-name');
            var sAvatar = jsynchat(elId).data('avatar');
            var sLink = jsynchat(elId).data('link');
            var sHtml = '';

            sHtml += '<div class="line-message receiver" data-reactid="' + oData.iTimeStamp + '_' + oData.iMessageId + '" >';
                sHtml += '<div>';
                    sHtml += '<div class="message">';
                        sHtml += '<span class="message-name">';
                        sHtml += '<a href="' + sLink + '" target="_blank"><image alt="' + sFullName + '" src="' + sAvatar + '" /></a>';
                        sHtml += '</span>';
                        sHtml += '<span class="message-content">';
                            switch(oData.type){
                                case 'link': 
                                case 'video': 
                                    var attachment = JSON.parse(oData.data);
                                    sHtml += '<div>';
                                        sHtml += '<div>';
                                            sHtml += '<span>' + oData.sText + '</span>';
                                            if('video' == oData.type){
                                                sHtml += '<a class="ynchat-playvideo" onclick="ynchat.showVideoInPopup(this); return false;" data-iframe="' + attachment.iframe + '" data-width="' + attachment.widthIframe + '" data-height="' + attachment.heightIframe + '"></a>';
                                            }
                                        sHtml += '</div>';
                                        sHtml += '<div class="ynchat-link">';
                                            sHtml += '<div>';
                                                sHtml += '<div>';
                                                    sHtml += '<a target="_blank" href="' + ynhelper.base64_decode(attachment.url) + '" >';
                                                        sHtml += '<img src="' + ynhelper.base64_decode(attachment.imageUrl) + '" alt="">';
                                                    sHtml += '</a>';
                                                sHtml += '</div>';
                                                sHtml += '<div>';
                                                    sHtml += '<div>';
                                                        sHtml += '<a href="' + ynhelper.base64_decode(attachment.url) + '" target="_blank" >' + (attachment.title) + '</a>';
                                                    sHtml += '</div>';
                                                    sHtml += '<div>';
                                                        sHtml += ynhelper.base64_decode(attachment.url);
                                                    sHtml += '</div>';
                                                sHtml += '</div>';
                                            sHtml += '</div>';
                                        sHtml += '</div>';
                                    sHtml += '</div>';
                                    break;
                                case 'file': 
                                    var upload_files = JSON.parse(oData.data);
                                    for (var i = 0; i < upload_files.length; i++) {
                                        var url = ynchat.getConfig('sApiUrl') + 'ynchat/download?id=' + upload_files[i].id;
                                        if (upload_files[i].type && upload_files[i].type.substring(0, 5) == 'image') {
                                            sHtml += '<div class="link_image"><img onclick="ynchat.showPhotoInPopup(this); return false;" data-url="'+url+'" src="'+url+'"/></div>';
                                        }
                                        sHtml += '<p><a href='+url+' download="'+upload_files[i].name+'" style="color:#333;" target="_blank">'+upload_files[i].name+'</a></p>';
                                    }
                                    
                                    sHtml += oData.sText;
                                    break;
                                default: 
                                    sHtml += oData.sText;
                                    break;
                            }                   
                        sHtml += '</span>';
                    sHtml += '</div>';
                    sHtml += '<div class="datetime">';
                        sHtml += '<span>' + oData.sTime + '</span>';
                    sHtml += '</div>';                
                sHtml += '</div>';
            sHtml += '</div>';

            return sHtml;
        }
    }
    , displayIntoBoxOldConversationAsSender: function(oData)
    {
        var sHtml = '';
        var text = oData.sText;
        sHtml += '<div class="line-message sender" data-reactid="' + oData.iTimeStamp + '_' + oData.iMessageId + '" >';
            sHtml += '<div>';
                sHtml += '<div class="message message-owner">';
                    sHtml += '<span class="message-content">' 
                        switch(oData.type){
                            case 'link': 
                            case 'video': 
                                var attachment = JSON.parse(oData.data);
                                sHtml += '<div>';
                                    sHtml += '<div>';
                                        sHtml += '<span>' + text + '</span>';
                                        if('video' == oData.type){
                                            sHtml += '<a class="ynchat-playvideo" onclick="ynchat.showVideoInPopup(this); return false;" data-iframe="' + attachment.iframe + '" data-width="' + attachment.widthIframe + '" data-height="' + attachment.heightIframe + '"></a>';
                                        }
                                    sHtml += '</div>';
                                    sHtml += '<div class="ynchat-link">';
                                        sHtml += '<div>';
                                            sHtml += '<div>';
                                                sHtml += '<a target="_blank" href="' + ynhelper.base64_decode(attachment.url) + '" >';
                                                    sHtml += '<img src="' + ynhelper.base64_decode(attachment.imageUrl) + '" alt="">';
                                                sHtml += '</a>';
                                            sHtml += '</div>';
                                            sHtml += '<div>';
                                                sHtml += '<div>';
                                                    sHtml += '<a href="' + ynhelper.base64_decode(attachment.url) + '" target="_blank" >' + (attachment.title) + '</a>';
                                                sHtml += '</div>';
                                                sHtml += '<div>';
                                                    sHtml += ynhelper.base64_decode(attachment.url);
                                                sHtml += '</div>';
                                            sHtml += '</div>';
                                        sHtml += '</div>';
                                    sHtml += '</div>';
                                sHtml += '</div>';
                                break;
                            case 'file': 
                                var upload_files = JSON.parse(oData.data);
                                for (var i = 0; i < upload_files.length; i++) {
                                    var url = ynchat.getConfig('sApiUrl') + 'ynchat/download?id=' + upload_files[i].id;
                                    if (upload_files[i].type && upload_files[i].type.substring(0, 5) == 'image') {
                                        sHtml += '<div class="link_image"><img onclick="ynchat.showPhotoInPopup(this); return false;" data-url="'+url+'" src="'+url+'"/></div>';
                                    }
                                    sHtml += '<p><a href='+url+' download="'+upload_files[i].name+'" style="color:#333;" target="_blank">'+upload_files[i].name+'</a></p>';
                                }
                                
                                sHtml += text;
                                break;
                            default: 
                                sHtml += text;
                                break;
                        }   
                    sHtml += '</span>';
                sHtml += '</div>';
                sHtml += '<div class="datetime">';
                    sHtml += '<span>' + oData.sTime + '</span>';
                sHtml += '</div>';
            sHtml += '</div>';
        sHtml += '</div>';

        return sHtml;
    }
    , __getOneMessage: function(iFriendId, bReceiver, oExtra)
    {
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/threadInfo',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "threadInfo"
                , iFriendId: iFriendId
                , iReceiver: bReceiver ? 1 : 0
                , iMessageId: (oExtra == null ? 0 : oExtra.iMessageId)
                , iNew: 1
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    ynchat.showError(oOutput.error_message);
                } else {
                    var aFriend = oOutput.aFriend;
                    var aMessage = oOutput.aMessage;
                    if(undefined != aFriend.user_id && null != aFriend.user_id){
                        ynchat.__addNewFriendIntoFriendList(aFriend);
                    }

                    var bReceiver = false;
                    if(oOutput.iReceiver == '1'){
                        bReceiver = true;
                    }
                    // draw chat box
                    ynchat.drawChatBoxByUserId(aMessage.iSenderId, bReceiver, aMessage, true, false);
                    // display as receiver
                    ynchat.displayIntoBoxHistoryAsReceiver(aMessage);

                    // load old message
                    ynchat.__getMoreMessages(aMessage.iSenderId, bReceiver, aMessage);

                    // sort message
                    var eleParent = '#ynchat-box-user-' + aMessage.iSenderId + ' .ynchat-box-history';
                    var eleChild = '.line-message';
                    ynchat.sortMessageInUserChatBox(eleParent, eleChild);
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , __getMoreMessages: function(iFriendId, bReceiver, oExtra)
    {
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/threadInfo',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "threadInfo"
                , iFriendId: iFriendId
                , iReceiver: bReceiver ? 1 : 0
                , iMessageId: (oExtra == null ? 0 : oExtra.iMessageId)
                , iNew: 0
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    ynchat.showError(oOutput.error_message);
                } else {
                    var aMoreMessages = oOutput.aMoreMessages;
                    var iFriendId = oOutput.iFriendId;
                    if(aMoreMessages.length > 0){
                        for(var idx=0; idx < aMoreMessages.length; idx++){
                            if(aMoreMessages[idx].iSenderId == iFriendId){
                                ynchat.displayIntoBoxHistoryAsReceiver(aMoreMessages[idx]);
                            } else {
                                ynchat.displayIntoBoxHistoryAsSender(aMoreMessages[idx], false, false, false);
                            }
                        }
                        // sort message
                        var eleParent = '#ynchat-box-user-' + iFriendId + ' .ynchat-box-history';
                        var eleChild = '.line-message';
                        ynchat.sortMessageInUserChatBox(eleParent, eleChild);
                        ynchat.scrollToBottom(eleParent, 750, 1000);                    
                    }
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , sortMessageInUserChatBox: function(eleParent, eleChild)
    {
        jsynchat(eleParent + ' ' + eleChild).tsort("", {order:'asc', attr:"data-reactid"});
    }
    , __addNewFriendIntoFriendList: function(aUser)
    {
        var elId = '#ynchat-friend-' + aUser.user_id;
        if(jsynchat(elId).length == 0){
            var sHtml = '';
            sHtml += '<li id="ynchat-friend-' + aUser.user_id + '" data-user-id="' + aUser.user_id + '" data-status="' + aUser.status + '" data-avatar="' + aUser.avatar + '" data-link="' + aUser.link + '" data-full-name="' + aUser.full_name + '" data-user-name="' + aUser.user_name + '" >';            
            sHtml += '<div class="avatar"><img src="' + aUser.avatar + '" /></div>';
            sHtml += '<div class="user-status">'; 
                if( aUser.status == 'available' ){
                    if( aUser.agent == 'web' ){
                        sHtml += ynchat.getLang('web'); 
                    }
                    else
                    if( aUser.agent == 'mobile' ){
                        sHtml += ynchat.getLang('mobile'); 
                    }
                }
            sHtml += '<div class="status ' + aUser.status + '"></div></div>';
            sHtml += '<div class="name">' + aUser.full_name + '</div>';
            sHtml += '</li>';

            jsynchat( "#ynchat-main-content #ynchat-friend-list .bodybar .default > ul" ).prepend( sHtml );
            jsynchat('#ynchat-friend-' + aUser.user_id).click(function(e)
            {
                var iUserId = jsynchat(this).data('user-id');
                ynchat.selectFriendInList(iUserId, false, null, false);
            });
        }
    }
    , selectFriendInList: function(iUserId, bReceiver, oExtra, bUpdateStatus)
    {
		ynchat.updateHideListNewMessage();
		ynchat.removeNewMessageInFriendsList(iUserId);
        var bShouldAddMessage = ynchat.drawChatBoxByUserId(iUserId, bReceiver, oExtra, bUpdateStatus, true);
        ynchat.removeNewMessageInFriendsList(iUserId);
        // display old message
        if(bShouldAddMessage == false){
            ynchat.__getMoreMessages(iUserId, bReceiver, oExtra);
        }
    }
    , getUnreadBox: function(){
        if(!ynchat.getUserSettings('iIsGoOnline')){
            return false;
        }

        /*sUrl = ynchat.getConfig('sApiUrl');
        if(sUrl.indexOf('index.php/')){
           sUrl = sUrl.replace('index.php/','PF.Base/');
        }*/
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl') + 'ynchat/getUnreadBox',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "getUnreadBox"
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    ynchat.showError(oOutput.error_message);
                } else {
                    var aUnreadBox = oOutput.aUnreadBox;
                    for(var idx=0; idx < aUnreadBox.length; idx++){
                        var aSenderInfo = aUnreadBox[idx].aSenderInfo;
                        var aMessages = aUnreadBox[idx].aMessages;
                        ynchat.__addNewFriendIntoFriendList(aSenderInfo);
                        ynchat.drawChatBoxByUserId(aSenderInfo.user_id, true, aMessages[0], false, false);
                        if(aUnreadBox[idx].iUnread == 1){
                            ynchat.updateNewMessage(aSenderInfo.user_id);
                        }
                        for(var idxMsg = (aMessages.length - 1); idxMsg >= 0; idxMsg--){
                            if(aMessages[idxMsg].iSenderId == aSenderInfo.user_id){
                                ynchat.displayIntoBoxHistoryAsReceiver(aMessages[idxMsg]);
                            } else {
                                ynchat.displayIntoBoxHistoryAsSender(aMessages[idxMsg], false, false, false);
                            }
                        }
                        // sort message
                        var eleParent = '#ynchat-box-user-' + aSenderInfo.user_id + ' .ynchat-box-history';
                        var eleChild = '.line-message';
                        ynchat.sortMessageInUserChatBox(eleParent, eleChild);
                        ynchat.scrollToBottom(eleParent, 750, 1000);
                        // hide when reloading
                        jsynchat('#ynchat-box-user-' + aSenderInfo.user_id).removeClass('active');
                    }
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
                if(ynchat.bRunning == false){
                    ynchat.disableIframe();
                }                
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , addFocusClass: function(iFriendId)
    {
        ynchat.removeFocusClassAllChatBoxes();
        jsynchat('#ynchat-box-user-' + iFriendId).addClass('focusedtab');
    }
    , removeFocusClassAllChatBoxes: function()
    {
        jsynchat('#ynchat-main-content #ynchat-chatboxtabs .chatboxuser').removeClass('focusedtab');
    }
    , updateStatusMessage: function(iFriendId)
    {
        ynchat.addFocusClass(iFriendId);
        if(undefined != ynchat.readStatus[iFriendId] && null != ynchat.readStatus[iFriendId] && false == ynchat.readStatus[iFriendId]){
            ynchat.readStatus[iFriendId] = true;

            jsynchat.ajax({
                url: ynchat.getConfig('sApiUrl')+ 'ynchat/updateStatusMessage',
                type: "POST",
                timeout: ynchat.getConfig('iTimeOut'),
                data: {
                    action : "updateStatusMessage"
                    , iFriendId: iFriendId
                    , sUserIdHash: ynchat.getConfig('sUserIdHash')
                },
                success:function(sOutput){
                    var oOutput = jsynchat.parseJSON(sOutput);

                    /* process */
                    if(oOutput.error_code > 0){
                        ynchat.showError(oOutput.error_message);
                    }

                    /* end */
                    /*ynchat.hideLoadingPanel();*/
                },
                error: function(x, t, m) {
                    /*ynchat.hideLoadingPanel();*/

                    if(t === "timeout") {
                        ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                    } else {
                        /*alert(t);*/
                    }
                }
            });
        }
    }
    , playSoundNotify: function(iFriendId)
    {
        // if(window.parent.document.hasFocus() == false
        //     || jsynchat('#ynchat-box-user-' + iFriendId).hasClass('focusedtab') == false){
            if(ynchat.getUserSettings('iIsNotifySound')){
                // jsynchat.mbAudio.play('effectSprite');
                jsynchat('#chatAudio')[0].play();
            }
        // }            
    }
    , getFormUploadPhoto: function(userid)
    {
        var sForm = '';
        sForm += '<form action="' + ynchat.getConfig('sApiUrl') + 'ynchat/uploadPhoto" onSubmit="return false" method="post" enctype="multipart/form-data" id="uploadphoto_' + userid + '">';
            sForm += '<input type="hidden" value="' + userid + '" id="iUserId" name="iUserId">';
            sForm += '<input name="image" id="imageInput" type="file" />';
            sForm += '<input type="submit" id="submit-btn" value="' + ynchat.getLang('upload') + '" />';
            sForm += '<img src="' + ynchat.getConfig('sSiteLink') + 'ynchat/static/image/add.gif" id="loading-img" style="display:none;" alt="Please Wait"/>';
        sForm += '</form>';
        sForm += '<div id="progressbox" style="display:none;"><div id="progressbar"></div><div id="statustxt">0%</div></div>';
        sForm += '<div class="output"></div>';

        return sForm;
    }
	
	//add a function for call event click on input file field
	, uploadFilesClick: function(ele, userid) {
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }

        var parent = jsynchat(ele).parent();
        if(jsynchat('#uploadphoto_' + userid).length == 0){
            var input = jsynchat(ele).next('.upload_input');
			jsynchat(input).trigger('click');
        }

        jsynchat(parent).closest('.menu').toggleClass('ynopen');
    }
	
	//add a function for uploading multiple data
	, uploadFilesExec: function(ele, userid) {
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }
		
        var files = jsynchat(ele).prop('files');
        var inner = jsynchat(ele).closest('.inner');
        var box_actions = jsynchat(inner).find('.ynchat-box-actions')[0];
		var chat_attachment = jsynchat(box_actions).next('.chat-attachment');
		var files_container = jsynchat(chat_attachment).find('.ynchat-content')[0];
		var chatinput = jsynchat(box_actions).find('.chatting')[0];
		if (files.length > 0) {
			for (var i = 0; i < files.length; i++ ) {
				ynchat.addFile(files[i]);
				var file_div = jsynchat('<div />', {
					'class': 'file_div'
				});
				
				var file_name = jsynchat('<span />', {
					'class': 'file_name',
					text: files[i].name
				});
				
				var remove_btn = jsynchat('<span />', {
					'class': 'remove_file',
					'text': '',
					'click': function(){
						var parent = jsynchat(this).parent();
						var index = jsynchat(parent).index();
						jsynchat(parent).remove();
						ynchat.removeFile(index);
					}
				});
				file_div.append(remove_btn);
                file_div.append(file_name);				
				jsynchat(files_container).append(file_div);
				jsynchat(chatinput).focus();
			}
		}
        jsynchat(ele).val('');
    }
    //add a function for uploading multiple photo
    , uploadPhotosExec: function(ele, userid) {
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }
        var box_actions = jsynchat(ele).closest('.ynchat-box-actions')[0];
        var chat_attachment = jsynchat(box_actions).next('.chat-attachment');
        var loadding = jsynchat(chat_attachment).find('.ynchat-loading')[0];
        var chatinput = jsynchat(box_actions).find('.chatting')[0];
        var photos = jsynchat(ele).prop('files');
        if (photos.length > 0) {
            var files = [];
            for (var i = 0; i < photos.length; i++ ) {
				var type = photos[i].type;
				switch(type) {
					case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
						ynchat.photos.push(photos[i]);
						ynchat.readFile(userid, files, photos[i], '', true);
						break;
				}
            }
			if (ynchat.photos.length > 0) {
				jsynchat(loadding).show();
				jsynchat(chatinput).focus();
			}
			else {
				alert(ynchat.getLang('please_try_again_make_sure_you_are_uploading_a_valid_photo'));
			}
        }
    }	
	//read file
	,readFile: function(id, files, file, value, isPhoto) {
    	var name = file.name;
		if(window.FileReader) {
            var reader = new FileReader();
            var rawData = new ArrayBuffer();
            reader.onload = function(e) {
                rawData = e.target.result;
                var file_item = {};
                file_item['file_name'] = name;
                file_item['file_data'] = rawData;
                files.push(file_item);
                if (!isPhoto){
                    if (files.length == ynchat.files.length) {
                        var upload_files = [];
                        ynchat.sendMessageWithFiles(id, value, files, upload_files, isPhoto);
                    }
                }
                else {
                    if (files.length == ynchat.photos.length) {
                        var upload_files = [];
                        ynchat.sendMessageWithFiles(id, value, files, upload_files, isPhoto);
                    }
                }       
            };
            reader.readAsDataURL(file);
        }
    }
	
	//send Message with Files - recursive
	, sendMessageWithFiles: function(iReceiverId, message, files, upload_files, isPhoto) {
		if (files.length > 0) {
			jsynchat.ajax({
		        url: ynchat.getConfig('sApiUrl')+ 'ynchat/upload',
		        type: "POST",
		        timeout: ynchat.getConfig('iTimeOut'),
		        data: {
		              iReceiverId: iReceiverId
					, fileName: files[0].file_name
		            , fileData: files[0].file_data
		        },
		        success: function(sOutput){
		            var oOutput = jsynchat.parseJSON(sOutput);
		
		            if(oOutput.error_code > 0){
                        var sBoxId = "ynchat-box-user-" + iReceiverId;
                        var element = '#' + sBoxId + ' .ynchat-box-history';
                        var inner = jsynchat(element).closest('.inner');
                        var loadding = jsynchat(inner).find('.ynchat-loading')[0];
                        jsynchat(loadding).hide();
                        ynchat.files = [];
		                alert(oOutput.error_message);
		            } else {
			            var file = {
			            	'id': oOutput.id,
			            	'name': oOutput.name,
			            	'type': oOutput.type
			            };
			            upload_files.push(file);
			            files.splice(0, 1);
			            ynchat.sendMessageWithFiles(iReceiverId, message, files, upload_files, isPhoto);
		            }
		
		        },
		        error: function(x, t, m) {
		            if(t === "timeout") {
		                ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
		            } else {
		            }
		        }
	        });	
		}
		else {
	        var action = 'sendMessageWithFiles';
	        var data = {
	        	iUserId: ynchat.getUserSettings('iUserId')
	            , iReceiverId: iReceiverId
	            , sText: message
	            , files: JSON.stringify(upload_files)
	        };
	
	        var sObject = ynchat.generateSendingData(action, data);
	        ynchat.connectionSend(sObject);
            if (isPhoto == true) {
				
                ynchat.photos = [];
            }
            else {
				
                ynchat.files = [];
            }
            var sBoxId = "ynchat-box-user-" + iReceiverId;
            var element = '#' + sBoxId + ' .ynchat-box-history';
            var inner = jsynchat(element).closest('.inner');
            var loadding = jsynchat(inner).find('.ynchat-loading')[0];
            jsynchat(loadding).hide();            
       }
    }
	
	//send message with files response
    , sendMessageWithFilesRes: function(oData) {
        if (oData.iSenderId != ynchat.getUserSettings('iUserId')) {
            var ele = '#ynchat-box-user-' + oData.iSenderId + ' .footerbar .chatting';
            var eleParent = '#ynchat-box-user-' + oData.iSenderId + ' .ynchat-box-history';
            var oldHeight = 0;
            //if(ynchat.getUserSettings('iIsGoOnline')){
                // draw chat box
                var bShouldAddMessage = ynchat.drawChatBoxByUserId(oData.iSenderId, true, oData, true, false);
                oldHeight = jsynchat(eleParent)[0].scrollHeight;
                if(bShouldAddMessage){
                    oData.sText = ynchat.prepareDisplay(oData.sText);
                    ynchat.displayIntoBoxHistoryAsReceiver(oData);
                } else {
                    ynchat.__getOneMessage(oData.iSenderId, true, oData);
                }
            //} else {
            //    ynchat.displayIntoBoxHistoryAsReceiver(oData);
            //}
            ynchat.playSoundNotify(oData.iSenderId);
            ele = '#ynchat-box-user-' + oData.iSenderId + ' .footerbar .chatting';
            eleParent = '#ynchat-box-user-' + oData.iSenderId + ' .ynchat-box-history';
            if(jsynchat(ele).is(":focus")){
                //input and text area has focus
                ynchat.scrollToBottom(eleParent, 100, 1000);
            }
            else {
                if (jsynchat('#ynchat-box-user-' + oData.iSenderId).hasClass('active')) {
                    if (jsynchat(eleParent)[0].scrollHeight > jsynchat(eleParent).height()) {
                        if (jsynchat(eleParent).scrollTop() + jsynchat(eleParent).innerHeight() < oldHeight) {
                            if (jsynchat('#ynchat-box-user-' + oData.iSenderId + ' .newMessageNotice').length == 0){
                                var newMess = jsynchat('<div />', {
                                    'class': 'newMessageNotice',
                                    text: 'Scroll down to see new messages.',
                                    click : function() {
                                        ynchat.scrollToBottom(eleParent, 100, 1000);
                                        jsynchat(this).remove();
                                        ynchat.removeNewMessageInFriendsList(oData.iSenderId);
                                    },
                                    css : {
                                        'z-index': '2009',
                                        'background': 'white',
                                        'position': 'fixed',
                                        'padding': '5px',
                                        'text-align': 'center',
                                        'width': '235px',
                                        'color': 'gray',
                                        'cursor': 'pointer'
                                    }
                                }).prependTo(jsynchat('#ynchat-box-user-' + oData.iSenderId + ' .ynchat-box-history'));
                            }
                            ynchat.updateNewMessageInFriendsList(oData.iSenderId);
                        }
                        else {
                            ynchat.scrollToBottom(eleParent, 100, 1000);
                        }
                    }
                }
                else {
                    ynchat.updateNewMessage(oData.iSenderId);
                }
            }
        }
        else {
			var bShouldAddMessage = ynchat.drawChatBoxByUserId(oData.iReceiverId, true, oData, false, false);
            oData.sText = ynchat.prepareDisplay(oData.sText);
            ynchat.displayIntoBoxHistoryAsSender(oData, false, false, false);
            var eleParent = '#ynchat-box-user-' + oData.iReceiverId + ' .ynchat-box-history';
            ynchat.scrollToBottom(eleParent, 100, 1000);
        }
    }
	
	//do not use now
    , clickPhotoInBox: function(ele, userid)
    {
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }

        var parent = jsynchat(ele).parent();
        if(jsynchat('#uploadphoto_' + userid).length == 0){
            // draw upload photo form
            jsynchat(parent).find('.panel').html(ynchat.getFormUploadPhoto(userid));

            // bind event
            var options = {
                target:   '#ynchat-box-user-' + userid + ' .footerbar .photo .panel .output',   // target element(s) to be updated with server response
                dataType:  'json',
                beforeSubmit:  function(){ynchat.uploadPhotoBeforeSubmit(userid);},  // pre-submit callback
                uploadProgress: function(event, position, total, percentComplete){ynchat.uploadPhotoOnProgress(event, position, total, percentComplete, userid);},
                success:       function(data, status, xhr){ynchat.uploadPhotoAfterSuccess(data, status, xhr, userid);},  // post-submit callback
                resetForm: true        // reset the form after successful submit
            };

            jsynchat('#uploadphoto_' + userid).submit(function() {
                jsynchat(this).ajaxSubmit(options);
                // return false to prevent standard browser submit and page navigation
                return false;
            });
        }

        jsynchat(parent).find('.panel').toggleClass('active');
    }
    , uploadPhotoBeforeSubmit: function(userid)
    {
        //check whether browser fully supports all File API
        var imageInput = '#ynchat-box-user-' + userid + ' .footerbar .photo .panel #imageInput';
        var output = '#ynchat-box-user-' + userid + ' .footerbar .photo .panel .output';
        var submit_btn = '#ynchat-box-user-' + userid + ' .footerbar .photo .panel #submit-btn';
        var loading_img = '#ynchat-box-user-' + userid + ' .footerbar .photo .panel #loading-img';
        var progressbox = jsynchat('#ynchat-box-user-' + userid + ' .footerbar .photo .panel #progressbox');
        var progressbar     = jsynchat('#ynchat-box-user-' + userid + ' .footerbar .photo .panel #progressbar');
        var statustxt     = jsynchat('#ynchat-box-user-' + userid + ' .footerbar .photo .panel #statustxt');
        var completed       = '0%';
       if (window.File && window.FileReader && window.FileList && window.Blob)
        {

            if( !jsynchat(imageInput).val()) //check empty input filed
            {
                jsynchat(output).html("Are you kidding me?");
                return false
            }

            var fsize = jsynchat(imageInput)[0].files[0].size; //get file size
            var ftype = jsynchat(imageInput)[0].files[0].type; // get file type

            //allow only valid image file types
            switch(ftype)
            {
                case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
                    break;
                default:
                    jsynchat(output).html("<b>"+ftype+"</b> Unsupported file type!");
                    return false
            }

            //Allowed file size is less than 1 MB (1048576)
            if(fsize > ynchat.getConfig('iImageSizeLimit'))
            {
                jsynchat(output).html("<b>" + ynhelper.bytesToSize(fsize) + "</b> " + ynchat.getLang('too_big_image_file') + " <br />" + ynchat.getLang('please_try_another_image'));
                return false
            }

            //Progress bar
            progressbox.show(); //show progressbar
            progressbar.width(completed); //initial value 0% of progressbar
            statustxt.html(completed); //set status text
            statustxt.css('color','#000'); //initial color of status text


            jsynchat(submit_btn).hide(); //hide submit button
            jsynchat(loading_img).show(); //hide submit button
            jsynchat(output).html("");
        }
        else
        {
            //Output error to older unsupported browsers that doesn't support HTML5 File API
            jsynchat(output).html("Please upgrade your browser, because your current browser lacks some new features we need!");
            return false;
        }
    }
    , uploadPhotoOnProgress: function(event, position, total, percentComplete, userid)
    {
        //Progress bar
        var progressbar     = jsynchat('#ynchat-box-user-' + userid + ' .footerbar .photo .panel #progressbar');
        var statustxt     = jsynchat('#ynchat-box-user-' + userid + ' .footerbar .photo .panel #statustxt');
        progressbar.width(percentComplete + '%') //update progressbar percent complete
        statustxt.html(percentComplete + '%'); //update status text
        if(percentComplete>50)
        {
            statustxt.css('color','#fff'); //change status text to white after 50%
        }
    }
    , uploadPhotoAfterSuccess: function(data, status, xhr, userid)
    {
        var submit_btn = '#ynchat-box-user-' + userid + ' .footerbar .photo .panel #submit-btn';
        var loading_img = '#ynchat-box-user-' + userid + ' .footerbar .photo .panel #loading-img';
        var progressbox = jsynchat('#ynchat-box-user-' + userid + ' .footerbar .photo .panel #progressbox');

        jsynchat(submit_btn).show(); //hide submit button
        jsynchat(loading_img).hide(); //hide submit button
        progressbox.hide();

        // show image as message
        if(undefined != data.aMessage && null != data.aMessage){
            ynchat.sendMessageAsText(userid, '', data.aMessage.iMessageId, 0);
        }

    }
    , getFormAttachVideo: function(userid)
    {
        var sForm = '';
        sForm += '<div>';
            sForm += '<input type="text" class="input-video" value="" data-userid="' + userid + '"/>';
            sForm += '<div class="extra_info"></div>';
        sForm += '</div>';
        sForm += '<div>';
            sForm += '<button type="button" class="button-video btn btn-primary btn-sm">' + ynchat.getLang('add') +'</button>';
        sForm += '</div>';

        return sForm;
    }
    , clickVideoInBox: function(ele, userid)
    {
        var parent = jsynchat(ele).parent();
        var $panel = jsynchat(parent).find('.panel');
        if($panel.find('.input-video').length == 0){
            // draw upload photo form
            $panel.html(ynchat.getFormAttachVideo(userid));

            // bind event
            $panel.find('.button-video').click(function(){
                ynchat.attachVideo($panel, userid);
            });
        }

        $panel.toggleClass('active');
    }
    , attachVideo: function($ele, userid){
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/attachVideo',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "attachVideo"
                , iUserId: userid
                , sUrl: ynhelper.base64_encode($ele.find('.input-video').val())
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                    ynchat.sendMessageAsText(oOutput.aMessage.iReceiverId, '', oOutput.aMessage.iMessageId, 0);
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , getFormAttachLink: function(userid)
    {
        var sForm = '';
        sForm += '<div>';
            sForm += '<input type="text" class="input-link" value="" data-userid="' + userid + '"/>';
            sForm += '<div class="extra_info"></div>';
        sForm += '</div>';
        sForm += '<div>';
            sForm += '<button type="button" class="button-link btn btn-primary btn-sm" >' + ynchat.getLang('add') +'</button>';
        sForm += '</div>';

        return sForm;
    }
    , clickLinkInBox: function(ele, userid)
    {
        var parent = jsynchat(ele).parent();
        var $panel = jsynchat(parent).find('.panel');
        if($panel.find('.input-link').length == 0){
            // draw form
            $panel.html(ynchat.getFormAttachLink(userid));

            // bind event
            $panel.find('.button-link').click(function(){
                ynchat.attachLink($panel, userid);
            });
        }

        $panel.toggleClass('active');
    }
    , attachLink: function($ele, userid){
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/attachLink',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "attachLink"
                , iUserId: userid
                , sUrl: ynhelper.base64_encode($ele.find('.input-link').val())
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                    ynchat.sendMessageAsText(oOutput.aMessage.iReceiverId, '', oOutput.aMessage.iMessageId, 0);
                }

                /* end */
            },
            error: function(x, t, m) {
                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                }
            }
        });
    }
    , clickEmoticonStickerInBox: function(ele, userid)
    {
        if(null != ynchat.oConnection && 'disconnect' == ynchat.oConnection.getStatus()){
            return ynchat.handleInvalidConnection();
        }

        var parent = jsynchat(ele).parent();
        var $panel = jsynchat(parent).find('.panel');
        if($panel.find('.emoticonsticker-container').length == 0){
            // draw form
            $panel.html(ynchat.getFormEmoticonSticker(userid));

            // select tab
            $panel.find('.emoticonsticker-header span').click(function(){
                var tab_active = jsynchat(this).attr('rel');

                $panel.find('.emoticonsticker-header > span').removeClass('active');
                $panel.find('.emoticonsticker-body > div').removeClass('active');

                jsynchat(this).addClass('active');
                $panel.find('.'+tab_active).addClass('active');
            });

            // bind event
            $panel.find('.emoticon-item').click(function(){
		 		var ele = jsynchat(this);
		    	var text = ele.attr('icon');

		    	var textarea = jsynchat('#ynchat-box-user-' + userid + ' .ynchat-box-actions').find('textarea');
		    	textarea.trigger('focus');
		        var caret = textarea.caret().start;
		        var val = textarea.val();

                val = val.substr(0, caret) + text + val.substr(caret);
		        textarea.val(val);
		        textarea.trigger('changed');

                // $panel.removeClass('active');
            });

            $panel.find('.sticker-item').click(function(){
                var stickerid = jsynchat(this).data('stickerid');
                // draw sticker
                var sBoxId = "ynchat-box-user-" + userid;
                var iTimeStamp = 0;
                if(jsynchat('#' + sBoxId + ' .ynchat-box-history div:last-child').length > 0){
                    iTimeStamp = jsynchat('#' + sBoxId + ' .ynchat-box-history div:last-child').data('reactid');
                }
                // send via socket
                ynchat.sendMessageAsText(userid, '', 0, stickerid);
                $panel.removeClass('active');
            });
        }

         if( $panel.hasClass('active') == false){
            // remove popup
            jsynchat('.panel.active').removeClass('active');
            
            $panel.addClass('active');
         } else {
            $panel.removeClass('active');
         }
    }
    , getFormEmoticonSticker: function(userid)
    {
        var sForm = '';
        var idx = 0;
        var aEmoticon = ynchat.getConfig('aEmoticon');
        var aSticker = ynchat.getConfig('aSticker');
        var sPicUrl = ynchat.getConfig('sPicUrl');
        sForm += '<div class="emoticonsticker-container" data-userid="' + userid + '">';
            sForm += '<div class="emoticonsticker-header">';
            sForm += '<span rel="emoticon-list" class="active">Emoticons</span>';
            sForm += '<span rel="sticker-list">Sticker</span>';
            sForm += '</div>';
            sForm += '<div class="emoticonsticker-body">';
                sForm += '<div class="emoticon-list active">';
                    for(idx = 0; idx < aEmoticon.length; idx ++){
                        sForm += '<span class="emoticon-item" icon="' + aEmoticon[idx].sText + '" title="' + aEmoticon[idx].sTitle + '"><img  data-text="' + aEmoticon[idx].sText + '" src="' + sPicUrl + 'ynchat_emoticon/' + (ynchat.isMobile() ? ('png/' + aEmoticon[idx].sImage.replace('gif', 'png')) : aEmoticon[idx].sImage) + '"></span>';
                    }
                sForm += '</div>';
                sForm += '<div class="sticker-list">';
                    for(idx = 0; idx < aSticker.length; idx ++){
                        sForm += '<span class="sticker-item" data-stickerid="' + aSticker[idx].iStickerId + '" title="' + aSticker[idx].sTitle + '"><img src="' + sPicUrl + 'ynchat_sticker/' + (ynchat.isMobile() ? ('png/' + aSticker[idx].sImage.replace('gif', 'png')) : aSticker[idx].sImage) + '"></span>';
                    }
                sForm += '</div>';
            sForm += '</div>';
        sForm += '</div>';

        return sForm;
    }
    , checkBan: function(text, type)
    {
        var idx = 0;
        var aBanWord = ynchat.getConfig('aBanWord');
        switch (type){
            case 'word':
                for(idx = 0; idx < aBanWord.length; idx ++){
                    text = text.replace(aBanWord[idx].org_find_value, aBanWord[idx].org_replacement);
                }
                break;
        }

        return text;
    }
    , parseEmoticon: function(text)
    {

        var idx = 0;
        var aEmoticon = ynchat.getConfig('aEmoticon');
        var sPicEmoticonUrl = ynchat.getConfig('sPicUrl') + 'ynchat_emoticon/';
        var key = '';

        for(idx = 0; idx < aEmoticon.length; idx ++){
            key = aEmoticon[idx].sText;
            text = text.replace(key, '<img src="' + sPicEmoticonUrl + aEmoticon[idx].sImage + '" alt="' + aEmoticon[idx].sTitle + '" title="' + aEmoticon[idx].sTitle + '" class="v_middle" />');
            text = text.replace('&lt;','<');
            text = text.replace('&gt;','>');
            text = text.replace('&quot;','"');
        }
        return text;
    }
    , prepareDisplay: function(sText) {
        sText = sText.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"');
        sText = sText.replace( /<script\b[^>]*>([\s\S]*?)<\/script>/gmi, '$1' );
        return sText;
    }
    , parseSticker: function(text, stickerid)
    {
        var idx = 0;
        var aSticker = ynchat.getConfig('aSticker');
        var sPicStickernUrl = ynchat.getConfig('sPicUrl') + 'ynchat_sticker/';
        for(idx = 0; idx < aSticker.length; idx ++){
            if(stickerid == aSticker[idx].iStickerId){
                text = '<img src="' + sPicStickernUrl + aSticker[idx].sImage + '" alt="' + aSticker[idx].sTitle + '" title="' + aSticker[idx].sTitle + '" />';
                break;
            }
        }

        return text;
    }
    , friendListHeaderBindEvent: function()
    {
        jsynchat('#ynchat-main-content #ynchat-friend-list .headerbar').click(function(){
            ynchat.friendListHeaderClick(this);
        });
        jsynchat('#ynchat-main-content #ynchat-friend-list .headerbar .setting > a').click(function(e){
            if( jsynchat(this).next().hasClass('ynopen') == false){
                jsynchat('.ynopen').removeClass('ynopen');
                jsynchat(this).next().addClass('ynopen');
            } else {
                jsynchat(this).next().removeClass('ynopen');
            }

            e.stopPropagation();
            return false;
        });
        jsynchat('#ynchat-main-content #ynchat-friend-list .headerbar .setting .menu li').click(function(e){
            var action = jsynchat(this).data('action');
            switch (action){
                case 'advanced_settings':
                    ynchat.getAdvancedSetting();
                    break;
                case 'close_all_tabs':
                    ynchat.friendListHeaderMenuClickCloseAllTabs(this);
                    break;
                case 'go_online_offline':
                    ynchat.friendListHeaderMenuClickGoOnlineOffline(this);
                    break;
                case 'play_sound':
                    ynchat.friendListHeaderMenuClickPlaySound(this);
                    break;
            }

            jsynchat('#ynchat-main-content #ynchat-friend-list .headerbar .setting a').trigger('click');
            e.stopPropagation();
        });
    }
    , getAdvancedSetting: function()
    {
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/getAdvancedSetting',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "getAdvancedSetting"
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                    ynchat.friendListHeaderMenuClickAdvancedSettings(oOutput.aBlockList, oOutput.aAllowList);
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , friendListHeaderMenuClickAdvancedSettings: function(aBlockList, aAllowList)
    {
        var sForm = '';
        sForm += '<div class="advanced-settings">';
            sForm += '<div class="padding-bottom">';
                sForm += '<div class="ynchat-input-checkbox">';
                    sForm += '<input type="radio" id="turnoff" name="list_turn_on_off_chat" value="turnoff">' + ynchat.getLang('turn_on_chat_except');
                sForm += '</div>';
                sForm += '<div class="list-turn-off-chat">';
                    sForm += '<input type="text" id="list-turn-off-chat" class="form-control"/>';
                sForm += '</div>';
            sForm += '</div>';
            sForm += '<div class="padding-bottom">';
                sForm += '<div class="ynchat-input-checkbox">';
                    sForm += '<input type="radio" id="turnon" name="list_turn_on_off_chat" value="turnon">' + ynchat.getLang('turn_on_chat_for_only_some_friends');
                sForm += '</div>';
                sForm += '<div class="list-turn-on-chat">';
                    sForm += '<input type="text" id="list-turn-on-chat" class="form-control"/>';
                sForm += '</div>';
            sForm += '</div>';
            sForm += '<div>';
                sForm += '<button class="btn btn-primary btn-xs">' + ynchat.getLang('save') + '</button>';
            sForm += '</div>';
        sForm += '</div>';

        jsynchat('.ynopen').removeClass('ynopen');
        ynchat.openPopupModal('Advanced Settings', sForm, '500px', 'auto');

        // bind event
        var options =  {
            theme: "facebook"
            , method: "POST"
            , noResultsText: ynchat.getLang('nothing_friend_s_found')
            , searchingText: ynchat.getLang('searching')
            , placeholder: ynchat.getLang('enter_name')
            , preventDuplicates: true
            , hintText: ''
            , resultsFormatter: ynchat.resultsFormatterTurnOnOffChatWithFriend // format when displaying on list box
            , tokenFormatter: ynchat.tokenFormatterTest // format when displaying on input
        };
        jsynchat("#list-turn-off-chat").tokenInput(ynchat.getConfig('sApiUrl')+ 'ynchat/searchFriend', options);
        jsynchat("#list-turn-on-chat").tokenInput(ynchat.getConfig('sApiUrl')+ 'ynchat/searchFriend', options);

        jsynchat('.advanced-settings input[type=radio][name=list_turn_on_off_chat]').change(function() {
            var elelTurnoff = '.advanced-settings .list-turn-off-chat';
            var eleTurnon = '.advanced-settings .list-turn-on-chat';
            switch (this.value){
                case 'turnoff':
                    jsynchat(elelTurnoff).show();
                    jsynchat(eleTurnon).hide();
                    break;
                case 'turnon':
                    jsynchat(eleTurnon).show();
                    jsynchat(elelTurnoff).hide();
                    break;
            }
        });

        jsynchat('.advanced-settings button').click(function(){
            ynchat.friendListHeaderMenuSaveAdvancedSettings();
            return false;
        });

        // add settings
        var elelTurnoff = '.advanced-settings .list-turn-off-chat';
        var eleTurnon = '.advanced-settings .list-turn-on-chat';
        switch (ynchat.getUserSettings('sTurnOnOff')){
            case 'onall':
            case 'offall':
                jsynchat('.advanced-settings #turnoff').prop('checked', true);
                jsynchat(elelTurnoff).show();
                jsynchat(eleTurnon).hide();
                break;
            case 'onsome':
                jsynchat('.advanced-settings #turnon').prop('checked', true);
                jsynchat(eleTurnon).show();
                jsynchat(elelTurnoff).hide();
                break;
            case 'offsome':
                jsynchat('.advanced-settings #turnoff').prop('checked', true);
                jsynchat(elelTurnoff).show();
                jsynchat(eleTurnon).hide();
                break;
        }

        var idx = 0;
        for(idx = 0; idx < aBlockList.length; idx ++){
            jsynchat("#list-turn-off-chat").tokenInput("add", aBlockList[idx]);
        }
        for(idx = 0; idx < aAllowList.length; idx ++){
            jsynchat("#list-turn-on-chat").tokenInput("add", aAllowList[idx]);
        }
    }
    , friendListHeaderMenuSaveAdvancedSettings: function()
    {
        var sBlockList = '';
        var sAllowList = '';
        var idx = 0;
        var aBlockList = jsynchat("#list-turn-off-chat").tokenInput("get");
        if(aBlockList.length > 0){
            sBlockList += aBlockList[0].user_id;
            for(idx = 1; idx < aBlockList.length; idx ++){
                sBlockList += ',' + aBlockList[idx].user_id;
            }
        }
        var aAllowList = jsynchat("#list-turn-on-chat").tokenInput("get");
        if(aAllowList.length > 0){
            sAllowList += aAllowList[0].user_id;
            for(idx = 1; idx < aAllowList.length; idx ++){
                sAllowList += ',' + aAllowList[idx].user_id;
            }
        }
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/saveAdvancedSetting',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "saveAdvancedSetting"
                , sTurn : jsynchat(".advanced-settings input[type='radio']:checked").val()
                , sBlockList : sBlockList
                , sAllowList : sAllowList
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                    ynchat.closePopupModal(jsynchat('.ynchat-popup-close'));
                    // jsynchat('.advanced-settings').remove();
                    
                    ynchat.setUserSettings('sTurnOnOff', oOutput.sTurnonoff)
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , resultsFormatterTurnOnOffChatWithFriend: function(item)
    {
        var sHtml = '';
        sHtml += '<div class="avatar"><img src="' + (item.avatar) + '" /></div>';
        sHtml += '<div class="name">' + item.full_name + '</div>';
        return "<li>" + sHtml + "</li>";
    }
    , tokenFormatterTest: function(item)
    {
        var sHtml = '';
        sHtml += item.full_name;
        return "<li><p>" + sHtml + "</p></li>";
    }
    , friendListHeaderMenuClickCloseAllTabs: function(ele)
    {
        ynchat.updateUserBoxSetting(0, 'removeall');
        jsynchat('#ynchat-chatboxtabs').html('');
		jsynchat('#ynchat-hidetabs').html('');
    }
    , friendListHeaderMenuClickGoOnlineOffline: function(ele)
    {
        var status = ynchat.getUserSettings('iIsGoOnline') == 1 ? 0 : 1;
        ynchat.setUserSettings('iIsGoOnline', status);
        if(ynchat.getUserSettings('iIsGoOnline')){
            jsynchat(ele).html(ynchat.getLang('go_offline'));
        } else {
            jsynchat(ele).html(ynchat.getLang('go_online'));
        }
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/updateStatusGoOnline',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "updateStatusGoOnline"
                , iStatus: status
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , friendListHeaderMenuClickPlaySound: function(ele)
    {
        var status = ynchat.getUserSettings('iIsNotifySound') == 1 ? 0 : 1;
        ynchat.setUserSettings('iIsNotifySound', status);
        if(ynchat.getUserSettings('iIsNotifySound')){
            jsynchat(ele).find('span').html(ynchat.getLang('yes'));
        } else {
            jsynchat(ele).find('span').html(ynchat.getLang('no'));
        }
        jsynchat.ajax({
            url: ynchat.getConfig('sApiUrl')+ 'ynchat/updateStatusPlaySound',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "updateStatusPlaySound"
                , iStatus: status
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){
                var oOutput = jsynchat.parseJSON(sOutput);

                /* process */
                if(oOutput.error_code > 0){
                    alert(oOutput.error_message);
                } else {
                }

                /* end */
                /*ynchat.hideLoadingPanel();*/
            },
            error: function(x, t, m) {
                /*ynchat.hideLoadingPanel();*/

                if(t === "timeout") {
                    ynchat.showError(ynchat.getLang('connection_timed_out_please_try_again'));
                } else {
                    /*alert(t);*/
                }
            }
        });
    }
    , friendListHeaderClick: function(ele)
    {
        jsynchat(ele).parent().toggleClass('ynchat-hide');
        jsynchat(ele).toggleClass('active');
    }
    , updateAgent: function(){
        sUrl = ynchat.getConfig('sApiUrl');
        /*if(sUrl.indexOf('index.php/')){
           sUrl = sUrl.replace('index.php/','PF.Base/');
        }*/
        jsynchat.ajax({
            url:  ynchat.getConfig('sApiUrl') + 'ynchat/updateAgent',
            type: "POST",
            timeout: ynchat.getConfig('iTimeOut'),
            data: {
                action : "updateAgent"
                , sUserIdHash: ynchat.getConfig('sUserIdHash')
            },
            success:function(sOutput){},
            error: function(x, t, m) {}
        });        
    }
    //implement drag and drop
    , dragenterFiles: function(e, ele) {
        e.preventDefault();
        jsynchat(ele).addClass('ynchat-file-pending'); 
    }
    , dragoverFiles: function(e) {
        e.preventDefault();
    }
    , dropFiles: function(e, ele) {
        jsynchat(ele).removeClass('ynchat-file-pending');
        e.preventDefault();
        var files = e.dataTransfer.files;
        var box_actions = jsynchat(ele).next('.ynchat-box-actions');
        var chat_attachment = jsynchat(box_actions).next('.chat-attachment');
        var files_container = jsynchat(chat_attachment).find('.ynchat-content')[0];
        var chatinput = jsynchat(box_actions).find('.chatting')[0];
        
        if (files.length > 0) {
            for (var i = 0; i < files.length; i++ ) {
                ynchat.addFile(files[i]);
                var file_div = jsynchat('<div />', {
                    'class': 'file_div'
                });
                
                var file_name = jsynchat('<span />', {
                    'class': 'file_name',
                    text: files[i].name
                });
                
                var remove_btn = jsynchat('<span />', {
                    'class': 'remove_file',
                    'text': '',
                    'click': function(){
                        var parent = jsynchat(this).parent();
                        var index = jsynchat(parent).index();
                        jsynchat(parent).remove();
                        ynchat.removeFile(index);
                    }
                });                
                file_div.append(remove_btn);
                file_div.append(file_name);
                jsynchat(files_container).append(file_div);
                jsynchat(chatinput).focus();
            }
        }
    }

	, checkNewMessage: function(ele) {
		if (jsynchat(ele).scrollTop() + jsynchat(ele).innerHeight() >= ele.scrollHeight) {
			var newMessageNotice = jsynchat(ele).find('.newMessageNotice');
			if (newMessageNotice.length > 0) {
				jsynchat(newMessageNotice[0]).remove();
				var parent = jsynchat(ele).closest('.chatboxuser');
				ynchat.removeNewMessageInFriendsList(jsynchat(parent).attr('data-userid'));
			}
		}
	}
	
	, updateNewMessage: function(userId) {
		var chatbox = jsynchat('#ynchat-box-user-' + userId);
		var parent = chatbox.parent();
		if (jsynchat(parent).attr('id') == 'ynchat-hidetabs') {
			var hideItem = jsynchat('#hideuser_' + userId);
			var countNewMessage = jsynchat('#hideuser_' + userId + ' .count-new-message');
			if (countNewMessage.length == 0) {
				countNewMessage = jsynchat('<span />', {
					'class': 'count-new-message',
					text: 1,
					css: {
						'padding': '3px',
						'background': 'red',
						'color': 'white'
					}
				}).prependTo(hideItem);
			}
			else {
				var text = jsynchat(countNewMessage[0]).text();
				if (isNaN(text)) {
					return;
				}
				var count_mess = parseInt(text)+1;
				if (count_mess > 9) count_mess = '9+';
				jsynchat(countNewMessage[0]).text(count_mess);
			}
			ynchat.updateHideListNewMessage();
		}
		else {
			if (!chatbox.hasClass('active')) {
				var countNewMessage = chatbox.find('.count-new-message');
				if (countNewMessage.length == 0) {
					var title =  jsynchat(chatbox.find('.title')[0]);
					countNewMessage = jsynchat('<span />', {
						'class': 'count-new-message',
						text: 1,
						css: {
							'padding': '3px',
							'background': 'red',
							'color': 'white'
						}
					}).prependTo(title);
				}
				else {
					var text = jsynchat(countNewMessage[0]).text();
					var count_mess = parseInt(text)+1;
					jsynchat(countNewMessage[0]).text(count_mess);
				}
			}
		}
		ynchat.updateNewMessageInFriendsList(userId);
	}
	
	,updateHideListNewMessage: function() {
		var count = 0;
		jsynchat('#ynchat-hidelist .count-new-message').each(function() {
			if (jsynchat(this).text() == '9+') {
				count = 10;
				return;
			}
			count += parseInt(jsynchat(this).text());
		})
		var countNewMessage = jsynchat('#ynchat-hidelist #count-new-message');
		if (count > 0) {
			if (count > 9) count = '9+';
			if (countNewMessage.length == 0) {
				countNewMessage = jsynchat('<div />', {
					id: 'count-new-message',
					text: count,
					css: {
						'padding': '3px',
						'background': 'red',
						'color': 'white',
						'position': 'absolute',
						'bottom': '18px',
						'left': '30px'
					}
				}).appendTo(jsynchat('#ynchat-hidelist'));
			}
			else {
				countNewMessage.text(count);
			}
		}
		else {
			if (countNewMessage.length == 0) {}
			else {
				countNewMessage.remove();
			}
		}
	}
	
	, updateNewMessageInFriendsList: function(userId) {
		var friendItem = '#ynchat-friend-'+userId;
		var countNewMessage = jsynchat(friendItem + ' .count-new-message');
		if (countNewMessage.length == 0) {
			countNewMessage = jsynchat('<span />', {
				'class': 'count-new-message',
				text: 1
			}).appendTo(friendItem);
		}
		else {
			countNewMessage = jsynchat(countNewMessage[0]);
			var count = countNewMessage.text();
			if (isNaN(count)) {
					return;
				}
			var count = parseInt(count)+1;
			if (count > 9) count = '9+';
			countNewMessage.text(count);
		}
	}
	
	, removeNewMessageInFriendsList: function(userId) {

		var friendItem = '#ynchat-friend-'+userId;
		var countNewMessage = jsynchat(friendItem + ' .count-new-message');
		if (countNewMessage.length > 0) {
            ynchat.scrollToBottom('#ynchat-box-user-'+userId + ' .ynchat-box-history', 100, 1000);
            jsynchat('.newMessageNotice').remove();
			jsynchat(countNewMessage[0]).remove();
		}
	}
	
    , openPopupModal: function(title, content, width, height) {
        if (!width) { width = '500px'; }
        if (!height) { height = '400px'; }

        var sPopup = '';
        sPopup += '<div class="ynchat-popup-overlay"></div>';
        sPopup += '<div class="ynchat-popup-drop">';
        sPopup += '<div class="ynchat-popup" style="width: ' + width + '; height: ' + height + '">';
            sPopup += '<div class="ynchat-popup-header"><h2>' + title + '</h2>';
            sPopup += '<div class="ynchat-popup-close"></div></div>';
            sPopup += '<div class="ynchat-popup-body">' + content + '</div>';
        sPopup += '</div>';
        sPopup += '</div>';

        jsynchat('#ynchat-iframe-container').addClass('ynchat-popup-open');
        jsynchat('#ynchat-iframe-container').append(sPopup);

        // fix 100% layout photo and video
        jsynchat('#ynchat-iframe-container').find('img').css('max-height', (window.innerHeight-60)*0.8 );
        jsynchat('#ynchat-iframe-container').find('iframe').css('max-height', (window.innerHeight-60)*0.8 );
        jsynchat('#ynchat-iframe-container').find('video').css('max-height', (window.innerHeight-60)*0.8 );
        jsynchat('#ynchat-iframe-container').find('embed').css('max-height', (window.innerHeight-60)*0.8 );

        jsynchat('.ynchat-popup-overlay').click(function(){
            ynchat.closePopupModal(jsynchat(this));
        });

        jsynchat('.ynchat-popup-close').click(function(){
            ynchat.closePopupModal(jsynchat(this));
        });
    }
    , closePopupModal: function(e) {
        e.closest('.ynchat-popup-drop').remove();
        jsynchat('#ynchat-iframe-container').find('.ynchat-popup-overlay:eq(0)').remove();
        if(jsynchat('.ynchat-popup-drop').length == 0)
        {
            jsynchat('#ynchat-iframe-container').removeClass('ynchat-popup-open');
        }
    }
    , showVideoInPopup: function(ele) {
        var iframe = jsynchat(ele).data('iframe');
        var width = parseInt( jsynchat(ele).data('width')) + 26 + 'px';
        var height = parseInt( jsynchat(ele).data('height')) + 75 + 'px';
        iframe = ynhelper.base64_decode(iframe);

        ynchat.openPopupModal(ynchat.getLang('video'), iframe, width, 'auto');
    }    
    , showPhotoInPopup: function(ele) {
        var url = jsynchat(ele).data('url');
        var sHtml = '';
        sHtml += '<div>';
            sHtml += '<img onclick="return false;" src="' + url + '" />';
        sHtml += '</div>';
        ynchat.openPopupModal(ynchat.getLang('photo'), sHtml, 'auto', 'auto');
    }
    , checkAtBottomContainer: function(ele) {
        var elem = jsynchat(ele);
        if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()) 
        {
            return true;
        }        
        return false;
    }
    , scrollToBottom: function(ele, timeout, timeanimate) {
        setTimeout(function()
        {
            if(ynchat.checkAtBottomContainer(ele) == false){
                jsynchat(ele).animate({
                    scrollTop: jsynchat(ele)[0].scrollHeight
                }, timeanimate);    
            }
            if(ynchat.isMobile() 
                && jsynchat('body').hasClass('show-ynchat-mobileview')
                && jsynchat('#ynchat-iframe-container').hasClass('ynchat-box-chatting')){
                jsynchat('body').animate({
                    scrollTop: jsynchat('body')[0].scrollHeight
                }, timeanimate);    
            }
        }, timeout);
    }    
};
 
jsynchat( document ).ready(function() {
    ynchat.setConfig('sApiUrl', '<?php echo $sApiUrl; ?>');
    ynchat.setConfig('iIsMobile', '<?php echo Phpfox::isMobile() ? 1 : 0; ?>');
    ynchat.initLangAndConfig();

    if (window.navigator && window.navigator.onLine){
        window.addEventListener("offline", function(e) {
          ynchat.disableIframe();
        }, false);

        window.addEventListener("online", function(e) {
            if(ynchat.oConnection != null){
                ynchat.oConnection.reconnect();
            }
        }, false);        
    }

});

