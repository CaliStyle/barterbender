if (typeof YNCONTEST_CONTROLLER_PLAYER == 'undefined')
{
    YNCONTEST_CONTROLLER_PLAYER = {
        arHTML5Player: {},
        iNumber: 0,
        
        initialize: function(){
            // Init all players.
            this.arHTML5Player = $('.younet_html5_player.init');
			this.mobileHTMLPlayer = $('.younet_html5_player.mobile');
            var sType = 'big';
            // Check if HTML5 player exist.
            if (this.arHTML5Player.length == 0)
            {
				if(this.mobileHTMLPlayer.length == 0){
					this.arHTML5Player = $('.younet_html5_player_profile.init');
					sType = 'small';
					 if (this.arHTML5Player.length == 0)
					{
						return false;
					}
					
				}else{	
					this.arHTML5Player = $('.younet_html5_player.mobile');
					sType = 'mobile';
					 if (this.arHTML5Player.length == 0)
					{
						return false;
					}
				}
               
            }
			
            ++this.iNumber;
           
            console.log('Number of Init: ' + this.iNumber);
            // Build extension for HTML5 player.
            this.buildCol();
            this.buildLoop();
            this.buildNext();
            this.buildPrev();
			this.buildClear();
            this.buildShuffle();
            if (sType == 'big')
            {
                this.initBig();
				
            }else if(sType == 'mobile'){
				if(this.iNumber > 1){	
					
				}else{
					this.initMobilePlayer();
					this.autoplayMobile();
				}
			}
            else
            {
                this.initSmall();
            }
			
            this.buildSelectSong();
            if ($('.html5_ajax').length > 0)
            {
                this.buildScrollBarFeedPage();
            }
            else
            {
                this.buildScrollBarMainPage();
            }
            this.arHTML5Player.each(function(i){
                $(this).removeClass('init');
            });
        },
    
        //Create a new flash audio - large player
        initBig: function(){
            var t = this;
            this.arHTML5Player.each(function(i){
                var oPlayer = $(this);
                var path = getParam('sJsHome');
                // Call init for each media element player.
                oPlayer.find('video,audio').mediaelementplayer({
                    // If success, when the song is over, call to play next song.
                    success: function (mediaElement, domObject) {
                        mediaElement.addEventListener('ended', function(e){
                            t.mejsPlayNext(e.target, oPlayer);
                        }, false);
                    },
                    pluginPath: path + "/module/contest/static/jscript/",
                    flashName:  "flashmediaelement.swf",
                    // List features.
					//for destop
                    features: ['playpause','progress','duration','loop','col','volume'],
                    // List key actions.
                    keyActions: [],
                    startVolume: 0.8,
                    pauseOtherPlayers: true
                });
            });
        },
		initMobilePlayer: function(){
			var t = this;
            this.arHTML5Player.each(function(i){
                var oPlayer = $(this);
                var path = getParam('sJsHome');
                // Call init for each media element player.
                oPlayer.find('video,audio').mediaelementplayer({
                    // If success, when the song is over, call to play next song.
                    success: function (mediaElement, domObject) {
                        mediaElement.addEventListener('ended', function(e){
                            t.mejsPlayNext(e.target, oPlayer);
                        }, false);
                    },
                    pluginPath: path + "/module/contest/static/jscript/",
                    flashName:  "flashmediaelement.swf",
                    
				   features: ['current','progress','duration','clear','playpause'],
                    // List key actions.
                    keyActions: [],
                    startVolume: 0.8,
                    pauseOtherPlayers: true
                });
            });
		},
		
        initSmall: function(){
            var t = this;
            this.arHTML5Player.each(function(i){
                var oPlayer = $(this);
                var sPath = getParam('sJsHome');
                oPlayer.find('video,audio').mediaelementplayer({
                    success: function(mediaElement, domObject){
                        mediaElement.addEventListener('ended', function(e){
                            t.mejsPlayNext(e.target, oPlayer);
                        }, false);
                    },
                    pluginPath: sPath + "/module/contest/static/jscript/",
                    flashName:  "flashmediaelement.swf",
                    features: ['playpause','progress'],
                    keyActions: [],
                    startVolume: 0.8,
                    pauseOtherPlayers: true
                });
            });
        },
		autoplayMobile: function(){
			this.arHTML5Player.each(function(i){
				var oPlayer = $(this);
				oPlayer.find('audio').each(function(){	
					this.play();
				});
			});
		},
        // Select a song
        buildSelectSong: function(){
            // Get all players.
			
            this.arHTML5Player.each(function(i){
			
                var oPlayer = $(this);
				
                oPlayer.find('.mejs-list li span.song-title').click(function(){
					
                    var oItem = $(this);
                    // Title.
                    oItem.parent().addClass('current').siblings().removeClass('current');
                    var strType = 1;
                    // For HTML5 player.
                    var oHTML5PlayerType = oPlayer.find('.html5_player_type');
                    if (oHTML5PlayerType.length > 0)
                    {
                        switch (oHTML5PlayerType.val())
                        {
                            case 'playlist':
                                strType = 0;
                                break;
                            case 'album':
                                strType = 1;
                                break;
                            default:
                                strType = 1;
                        }
                    }
                    var strSongTitle = oItem.text();
                    var strAudioSrc = oItem.parent().children(".link").text();	
					
                    var iSongId = oItem.parent().children(".song_id").text();
                    var oTitleHead = oPlayer.find('.song-title-head');
                    oPlayer.find('audio').each(function(){	
						if((navigator.userAgent.match(/iPhone/i)) || 
						 (navigator.userAgent.match(/iPod/i))) {
						   if (document.cookie.indexOf("iphone_redirect=false") == -1) 
							 this.pause(); 
						}else
							this.player.pause(); 
                        // Fix bug can not change song on Firefox and IE.
                        
                        oTitleHead.html(strSongTitle);
						$(this).attr('src', strAudioSrc);
						if((navigator.userAgent.match(/iPhone/i)) || 
						 (navigator.userAgent.match(/iPod/i))) {
						   if (document.cookie.indexOf("iphone_redirect=false") == -1) 
							 this.setSrc(strAudioSrc);
						}else
							this.player.setSrc(strAudioSrc);
                        
                        if (oPlayer.hasClass('younet_html5_player_profile') || oPlayer.hasClass('html5_ajax'))
                        {
                            $.ajaxCall('contest.changSongHTML5Small', 'musicid=' + iSongId + '&typ=' + strType, 'POST');
                        }
                        else
                        {
                            $.ajaxCall('contest.changSongHTML5', 'musicid=' + iSongId + '&typ=' + strType, 'POST');
                        }
                        // Change the move text.
						if((navigator.userAgent.match(/iPhone/i)) || 
						 (navigator.userAgent.match(/iPod/i))) {
						   if (document.cookie.indexOf("iphone_redirect=false") == -1) 
							  this.play();
						}else
							 this.player.play();
                       
                    });
                });
            });
        },
        
        //Get an random number
        getRandomInt: function(iMin, iMax) {
            return Math.floor(Math.random() * (iMax - iMin + 1)) + iMin;
        },

        getNextSong: function(oCurrentItem, oHTML5Player){
            var t = this;
            //get next li to get song
            var oNextItem;
            var oControlList = oHTML5Player.find('.song-list');
            var iCount = oControlList.children('li').size();
            if (oHTML5Player.find('.mejs-shuffle-on').length > 0)
            {
                oNextItem = oHTML5Player.find(".mejs-list li").eq(t.getRandomInt(0, iCount - 1));
            }
            else if(oHTML5Player.find('.mejs-loop-on').length > 0)
            {
                // 1 song only.
                if (iCount == 1)
                {
                    oNextItem = $(oCurrentItem);
                }
                else
                {
                    // Multi-song: get the next song.
                    oNextItem = $(oCurrentItem).parent().next();
                    // If the next song does not exist, get the first one.
                    if (oNextItem.length == 0)
                    {
                        // If it is the last song.
                        oNextItem = oHTML5Player.find('.mejs-list li:first');
                    }
                }
            }
            else
            {
                oNextItem = $(oCurrentItem).parent().next();
            }
			//console.log(oNextItem);
            return oNextItem;
        },

        buildScrollBarMainPage: function(){
            var t = this;
            t.arHTML5Player.each(function(i){
                var oPlayer = $(this);
                var arConfig = {
                    height: '300px',
                    width: '493px',
                    disableFadeOut: true,
                    size: '5px'
                };
            /*  oPlayer.find('.scroll-pane').slimScroll(arConfig);
            
                var iScroll = oPlayer.find('.scroll-pane').scrollTop();
            
                var oControlList = oPlayer.find('.song-list');
            
                oPlayer.find(".slimScrollBar").css("top", 0);

                oControlList.slimScroll({
                    "scroll": (iScroll + "px")
                });

                oControlList.trigger(jQuery.Event("updatescroller"));*/
            });
        },
    
        buildScrollBarFeedPage: function(){
            var t = this;
        
            t.arHTML5Player.each(function(i){
                var oPlayer = $(this);
            
                var oSongList = oPlayer.find('.song-list').find('li');
            
                // The height of 1 song.
                var iHeightItem = 0;
            
                if (oSongList.length > 0)
                {
                    iHeightItem = $(oSongList[0]).height();
                }
            
                // The height of song list.
                var iHeight = 10;
            
                // Limit the number of songs display in song list.
                var iLimitNumberOfSongs = 5;
            
                if (oSongList.length <= iLimitNumberOfSongs)
                    iHeight += oSongList.length * iHeightItem;
                else
                    iHeight += iLimitNumberOfSongs * iHeightItem;
            
                // Set config.
                var arConfig = {
                    height: iHeight + 'px',
                    width: '465px',
                    disableFadeOut: true,
                    size: '5px'
                };
                oPlayer.find('.scroll-pane').slimScroll(arConfig);
                var iScroll = oPlayer.find('.scroll-pane').scrollTop();
                var oControlList = oPlayer.find('.song-list');
                oPlayer.find(".slimScrollBar").css("top", 0);
                oControlList.slimScroll({
                    "scroll": (iScroll + "px")
                });
                oControlList.trigger(jQuery.Event("updatescroller"));
            });
        },
    
    

        //get next song's link 
        nextSong: function(oCurrentItem, oHTML5Player){
            var oNextItem;
            oNextItem = this.getNextSong(oCurrentItem, oHTML5Player);//get next song
            var oControlList = oHTML5Player.find(".song-list");
            /*Scroll */
            oControlList.scrollTop(0);
            oControlList.scrollTo(oCurrentItem.closest("li"));
            oControlList.scrollTo(oHTML5Player.find(".current"));
            var strAudioSrc = oNextItem.children(".link").text();
            var oTitleHead = oHTML5Player.find('.song-title-head');
            if (oTitleHead.length > 0)
            {
                oTitleHead.html(oNextItem.children(".song-title").text());
            }
            oNextItem.addClass('current').siblings().removeClass('current');
            // Call ajax.
            var strType = 1;
            // For HTML5 player.
            var oHTML5PlayerType = oHTML5Player.find('.html5_player_type');
            if (oHTML5PlayerType.length > 0)
            {
                switch (oHTML5PlayerType.val())
                {
                    case 'playlist':
                        strType = 0;
                        break;
                    case 'album':
                        strType = 1;
                        break;
                    default:
                        strType = 1;
                }
            }
            var iSongId = parseInt(oNextItem.find(".song_id").text());
            oHTML5Player.find('audio:first').each(function(){
                if (oHTML5Player.hasClass('younet_html5_player_profile') || oHTML5Player.hasClass('html5_ajax'))
                {
                    $.ajaxCall('contest.changSongHTML5Small', 'musicid=' + iSongId + '&typ=' + strType, 'POST');
                }
                else
                {
                    $.ajaxCall('contest.changSongHTML5', 'musicid=' + iSongId + '&typ=' + strType, 'POST');
                }
            });
            return oNextItem;
        },

        //play new playlist
        newPlay: function(oCurrentItem, oHTML5Player){
            var oNextItem = null;
            oNextItem = this.getNextSong(oCurrentItem, oHTML5Player);//get next song
            var strAudioSrc = oNextItem.next().text();
            $(oCurrentItem).next().addClass('current').siblings().removeClass('current');
            return strAudioSrc;
        },

        //Choose next song
        mejsPlayNext: function(oCurrentPlayer, oHTML5Player){
            var oCurrentItem = '';
            var strAudioSrcItem = '';
            if (oHTML5Player.find('.mejs-list li.current').length > 0)
            { 
                // get the .current song	
                oCurrentItem = oHTML5Player.find('.mejs-list li.current:first span.link'); // :first is added if we have few .current classes
                strAudioSrcItem = this.nextSong(oCurrentItem, oHTML5Player);
				strAudioSrc = strAudioSrcItem.children(".link").text();
            }
            else
            {
                var oControlList = oHTML5Player.find('.song-list');
                var iCount = oControlList.children('li').size();
                // If the first song does not exist (That means the list is empty) or play one song.
                if (iCount == 0 || iCount == 1)
                {
                    // Repeat if user click on shuffle or loop, otherwise pause.
                    if (oHTML5Player.find('.mejs-shuffle-on').length > 0 || oHTML5Player.find('.mejs-loop-on').length > 0)
                    {
                        oCurrentPlayer.play();
                    }
                    else
                    {
                        oCurrentPlayer.pause();
                    }
                    // End this function.
                    return;
                }
                // if there is no .current class
                oCurrentItem = oHTML5Player.find('.mejs-list li:first .link'); // get :first if we don't have .current class
                // Set the audio source.
                strAudioSrcItem = this.newPlay(oCurrentItem, oHTML5Player);
				strAudioSrc = strAudioSrcItem.children(".link").text();
            }
            // If this is the last song.
            if( $(oCurrentItem.parent()).is(':last-child') ) 
            {
                var oControlList = oHTML5Player.find(".song-list");
                // if it is last - and user click on loop, repeat the list.
                if (oHTML5Player.find('.mejs-loop-on').size() > 0)
                {
                    var iCount = oControlList.children('li').size();
                    var strSongTitle = '';
                    var oTitleHead = oHTML5Player.find('.song-title-head');
                    if (iCount == 1)
                    {
                        strAudioSrc = oCurrentItem.text();
                        if (oTitleHead.size() > 0)
                        {
                            strSongTitle = oCurrentItem.parent().children(".song-title").text();
                        }
                    }
                    else
                    {
                        oCurrentItem = oHTML5Player.find('.mejs-list li:first span.link');
                    
                        oHTML5Player.find('.mejs-list .current').removeClass("current");
                        oHTML5Player.find('.mejs-list li:first').addClass("current");
                    
                        strAudioSrc = oCurrentItem.text();
                    
                        if (oTitleHead.length > 0)
                        {
                            strSongTitle = oCurrentItem.parent().children(".song-title").text()
                        }
                    }
                    if (oTitleHead.length > 0)
                    {
                        oTitleHead.innerHTML = strSongTitle;
                    }
					oCurrentPlayer.pause();
                    oCurrentPlayer.setSrc(strAudioSrc);
                    oCurrentPlayer.play();
                    oControlList.scrollTop(0);
                }
                else if(oHTML5Player.find('.mejs-shuffle-on').size() > 0)
                {
                    oCurrentItem = this.nextSong(oCurrentItem, oHTML5Player);
                    strAudioSrc = oCurrentItem.text();
                    oCurrentPlayer.setSrc(strAudioSrc);
                    oCurrentPlayer.play();
                    oControlList.scrollTo(oCurrentItem);
                }
                else
                {
                    oHTML5Player.find(oCurrentItem).removeClass('current');
                }
            }
            else
            {
				oCurrentPlayer.pause();
                oCurrentPlayer.setSrc(strAudioSrc);
                oCurrentPlayer.play();
            }
			console.log(strAudioSrc);
        },

        //Build Next Button
        buildNext: function() {
            var t = this;
        
            // next button
            MediaElementPlayer.prototype.buildnext =function(oPlayer, oControls, layers, media) {
                // create the next button
                var strNextButton = '<div class="mejs-button mejs-next-button ' + ((oPlayer.options.next) ? 'mejs-next-on' : 'mejs-next-off') + '">' +
                '<button type="button"></button>' +
                '</div>';
            
                var next = $(strNextButton)
                // append it to the toolbar
                .appendTo(oControls)
                //Click on next button
                .click(function(evt){
                    var oNextSong;
                    var strAudioSrc = '';
                    var strSongTitle = '';
                    var oControlPlayer = oControls.parent().parent().parent().parent();
                    var oHTML5Player = oControlPlayer.parent();
                    var oControlList = oHTML5Player.find('.song-list');
                    var oCurrentItem = oControlList.find('li.current');
                    var oTitleHead = oControlPlayer.find('.song-title-head');
                    if (oControlPlayer.find('.mejs-shuffle-on').size() > 0) 
                    {
                        //check shuffle mode
                        var ul = oControlList;
                        var li = ul.children('li').size();
                        var nextItemGet;
                        if (li <= 1) 
                        {
                            nextItemGet = 0;
                        } 
                        else 
                        {
                            while((nextItemGet = t.getRandomInt(0, li - 1)) == oCurrentItem.index()) 
                            {
                            }
                        }
                        oNextSong = oHTML5Player.find(".mejs-list li").eq(nextItemGet);
                        oCurrentItem.removeClass('current');
                        strAudioSrc = oNextSong.children(".link").text();
                        if (oTitleHead.size() > 0)
                        {
                            strSongTitle = oNextSong.children(".song-title").text();
                        }
                        oNextSong.addClass('current');
                    }
                    else if (oCurrentItem.is(':last-child'))
                    {
                        var oSongLink = oHTML5Player.find('.mejs-list li:first span.link');
                        strAudioSrc = oSongLink.text();
                        if (oTitleHead.size() > 0)
                        {
                            strSongTitle = oSongLink.parent().children(".song-title").text(); 
                        }
                        oCurrentItem.removeClass('current');
                        // Get the first song.
                        oNextSong = oHTML5Player.find('.mejs-list li:first');
                        oNextSong.addClass('current');
                    }
                    else 
                    {
                        oNextSong = oCurrentItem.next();
                        if (!oNextSong.size()) 
                        {
                            return true;
                        }
                        oCurrentItem.removeClass('current');
                        strAudioSrc = oNextSong.children(".link").text();
                        if (oTitleHead.size() > 0)
                        {
                            strSongTitle = oNextSong.children(".song-title").text();
                        }
                        oNextSong.addClass('current');
                    }
                    // Call ajax.
                    var strType = 1;
                    // For HTML5 player.
                    var oHTML5PlayerType = oHTML5Player.find('.html5_player_type');
                    if (oHTML5PlayerType.length > 0)
                    {
                        switch (oHTML5PlayerType.val())
                        {
                            case 'playlist':
                                strType = 0;
                                break;

                            case 'album':
                                strType = 1;
                                break;

                            default:
                                strType = 1;
                        }
                    }
                    var iSongId = parseInt(oNextSong.find(".song_id").text());
                    oHTML5Player.find('audio:first').each(function(){
                        if (oHTML5Player.hasClass('younet_html5_player_profile') || oHTML5Player.hasClass('html5_ajax'))
                        {
                            $.ajaxCall('contest.changSongHTML5Small', 'musicid=' + iSongId + '&typ=' + strType, 'POST');
                        }
                        else
                        {
                            $.ajaxCall('contest.changSongHTML5', 'musicid=' + iSongId + '&typ=' + strType, 'POST');
                        }
                    });
                    oPlayer.pause();
                    oPlayer.setSrc(strAudioSrc);
                    if (oTitleHead.size() > 0)
                    {
                        oTitleHead.html(oNextSong.children(".song-title").text());
                    }
                    oPlayer.play();
                    oControlList.scrollTo(oNextSong);
                });
            // add a click toggle event
            }
        },

        //Build Previous button
        buildPrev: function() {
            var t = this;
            // prev button
            MediaElementPlayer.prototype.buildprev = function(oPlayer, oControls, layers, media) {
                var strHTMLPrev = '<div class="mejs-button mejs-prev-button ' + ((oPlayer.options.prev) ? 'mejs-prev-on' : 'mejs-prev-off') + '">' +
                '<button type="button"></button>' +
                '</div>';
                // create the prev button
                var prev = $(strHTMLPrev)
                // append it to the toolbar
                .appendTo(oControls)
                // add a click toggle event
                .click(function(evt){
                    var oPrevSong = '';
                    var strAudioSrc = '';
                    var strSongTitle = '';
                    var oControlPlayer = oControls.parent().parent().parent().parent();
                    var oHTML5Player = oControlPlayer.parent();
                    var oTitleHead = oControlPlayer.find('.song-title-head');
                    var oControlList = oHTML5Player.find('.song-list');
                    var oCurrentItem = oControlList.find('li.current');
                    if (oControlPlayer.find('.mejs-shuffle-on').size() > 0)
                    { 
                        //check shuffle mode
                        var ul = oControlList;
                        var li= ul.children('li').size();
                        var iNextItem;

                        if (li <= 1)
                        {
                            iNextItem = 0;
                        }
                        else
                        {
                            while ((iNextItem = t.getRandomInt(0,li - 1)) == oCurrentItem.index())
                            {
                            }
                        }
                        oPrevSong = oHTML5Player.find(".mejs-list li").eq(iNextItem);
                        oCurrentItem.removeClass('current');
                        strAudioSrc = oPrevSong.children(".link").text();
                        if (oTitleHead.size() > 0)
                        {
                            strSongTitle = oPrevSong.children(".song-title").text();
                        }
                        oPrevSong.addClass('current');
                    } 
                    else 
                    {
                        oPrevSong = oCurrentItem.prev();
                        if (!oPrevSong.size())
                        {
                            return true;
                        }
                        oCurrentItem.removeClass('current');
                        strAudioSrc = oPrevSong.children(".link").text();
                        if (oTitleHead.size() > 0)
                        {
                            strSongTitle = oPrevSong.children(".song-title").text()
                        }
                        oPrevSong.addClass('current');
                    }

                    // Call ajax.
                    var strType = 1;

                    // For HTML5 player.
                    var oHTML5PlayerType = oHTML5Player.find('.html5_player_type');

                    if (oHTML5PlayerType.length > 0)
                    {
                        switch (oHTML5PlayerType.val())
                        {
                            case 'playlist':
                                strType = 0;
                                break;

                            case 'album':
                                strType = 1;
                                break;

                            default:
                                strType = 1;
                        }
                    }
                    var iSongId = parseInt(oPrevSong.find(".song_id").text());
                    oHTML5Player.find('audio:first').each(function(){
                        if (oHTML5Player.hasClass('younet_html5_player_profile') || oHTML5Player.hasClass('html5_ajax'))
                        {
                            $.ajaxCall('contest.changSongHTML5Small', 'musicid=' + iSongId + '&typ=' + strType, 'POST');
                        }
                        else
                        {
                            $.ajaxCall('contest.changSongHTML5', 'musicid=' + iSongId + '&typ=' + strType, 'POST');
                        }
                    });
                    oPlayer.pause();
                    oPlayer.setSrc(strAudioSrc);
                    if (oTitleHead.size() > 0)
                    {
                        oTitleHead.html(oPrevSong.children(".song-title").text())
                    }
                    oPlayer.play();
                    oControlList.scrollTo(oPrevSong);
                });  
            }
        },

        //Build repeat button
        buildLoop: function(){
            // prev button
            MediaElementPlayer.prototype.buildloop = function(oPlayer, oControls, layers, media) {
                var strHTMLLoop = '<div class="mejs-button mejs-loop-button ' + ((oPlayer.options.loop) ? 'mejs-loop-on' : 'mejs-loop-off') + '">' +
                '<button type="button"></button>' +
                '</div>';
                // create the prev button
                var oLoop = $(strHTMLLoop)
                // append it to the toolbar
                .appendTo(oControls)
                // add a click toggle event
                .click(function(){
                    oPlayer.options.loop = !oPlayer.options.loop;
                    if (oPlayer.options.loop)
                    {
                        oLoop.removeClass('mejs-loop-off').addClass('mejs-loop-on');
                        oPlayer.options.shuffle = false;
                        oControls.find('.mejs-shuffle-button').removeClass('mejs-shuffle-on').addClass('mejs-shuffle-off');
                    }
                    else
                    {
                        oLoop.removeClass('mejs-loop-on').addClass('mejs-loop-off');
                    }
                });     
            }
        },

        //Build Shuffle button
        buildShuffle: function() {
            // prev button
            MediaElementPlayer.prototype.buildshuffle = function(oPlayer, oControls, layers, media) {
                var strShuffle = '<div class="mejs-button mejs-shuffle-button ' + ((oPlayer.options.shuffle) ? 'mejs-shuffle-on' : 'mejs-shuffle-off') + '">' +
                '<button type="button"></button>' +
                '</div>';
                // create the prev button
                var oShuffle = $(strShuffle)
                // append it to the toolbar
                .appendTo(oControls)
                // add a click toggle event
                .click(function(){
                    oPlayer.options.shuffle = !oPlayer.options.shuffle;
                    if (oPlayer.options.shuffle)
                    {
                        oShuffle.removeClass('mejs-shuffle-off').addClass('mejs-shuffle-on');
                        oPlayer.options.loop = false;
                        oControls.find('.mejs-loop-button').removeClass('mejs-loop-on').addClass('mejs-loop-off');
                    }
                    else
                    {
                        oShuffle.removeClass('mejs-shuffle-on').addClass('mejs-shuffle-off');
                    }
                });
            }
        },

        buildCol: function() {
            // prev button
            MediaElementPlayer.prototype.buildcol = function(player, controls, layers, media) {
                var strHTMLCol = '<div class="col-vol">' +
                '<span></span>' +
                '</div>';
                // create the prev button
                $(strHTMLCol)
                // append it to the toolbar
                .appendTo(controls)
            // add a click toggle event            
            }
        },
		buildClear: function(){
			 MediaElementPlayer.prototype.buildclear = function(player, controls, layers, media) {
                var strHTMLClear = '<div class="clear">' +
                '</div>';
                // create the prev button
                $(strHTMLClear)
                // append it to the toolbar
                .appendTo(controls)
            // add a click toggle event            
            }
		}
    }
}

$Behavior.initHtml5Player = function() {

    if($('#init_once_music').val() == 0){
        YNCONTEST_CONTROLLER_PLAYER.initialize();
        $('#init_once_music').val(1);
    }
}