<?php

if (!Phpfox::isAdminPanel() && Phpfox::isModule('socialad')) {
    $url = $sBaseUrl . 'PF.Base/module/socialad/static/css/default/default/display.css';
    $sData .= '<link href="' . $url . '" rel="stylesheet">';
    $sData .= '<script>
                $Behavior.ynsaInitDisplayAdHiddenJs = function() {
                    $(\'.ynsaDisplayAdHideButton\').on(\'click\', function(e) {
                        var adId = $(this).data(\'ad-id\');
                        var prefix = \'#ynsaAdDisplay_\';
                        var divId = prefix + adId;
                
                        $(divId).hide();
                        $.ajaxCall(\'socialad.hideAd\', \'ad_id=\' + adId);
                        e.preventDefault();
                        return false;
                    });
                
                    $(\'.ynsaDisplayBannerAdHideButton\').on(\'click\', function(e) {
                        var adId = $(this).data(\'ad-id\');
                        var prefix = \'#ynsaAdDisplay_\';
                        var divId = prefix + adId;
                
                        $(divId).hide();
                        //$(this).parent().hide();
                        $.ajaxCall(\'socialad.hideAd\', \'ad_id=\' + adId);
                        e.preventDefault();
                        return false;
                    });
                
                    $(\'.ynsaHiddenPermanently .button\').on(\'click\', function(e) {
                        var adId = $(this).data(\'ad-id\');
                        var prefix = \'#ynsaAdDisplay_\';
                        var divId = prefix + adId;
                        var action = $(this).data(\'action\');
                
                        if(action == \'yes\') {
                            $.ajaxCall(\'socialad.hideAdPermanent\', \'ad_id=\' + adId);
                            $(divId).hide();
                        }
                
                        e.preventDefault();
                        return false;
                    });
                };
                
                ynsaShowHidePermanentBox = function(adId) {
                    var html = \'\';
                    var prefix = \'#ynsaAdDisplay_\';
                    var divId = prefix + adId;
                
                    $(divId).find(\'.ynsaDisplayAdBlock\').hide();
                    $(divId).find(\'.ynsaHiddenPermanently\').show();
                    $(divId).show();
                };
                
                ynsaClickNoButtonBox = function(adId) {
                    var html = \'\';
                    var prefix = \'#ynsaAdDisplay_\';
                    var divId = prefix + adId;
                
                    $(divId).find(\'.ynsaDisplayAdBlock\').show();
                    $(divId).find(\'.ynsaHiddenPermanently\').hide();
                };
                
                ynsaShowHidePermanentBoxBanner = function(phrase, adId) {
                    var decodedPhrase = $("<div/>").html(phrase).text();
                    if(confirm(decodedPhrase)) {
                            $.ajaxCall(\'socialad.hideAdPermanent\', \'ad_id=\' + adId);
                    }
                };
            </script>';
    $sData .= '<script>
            var first_socialAd = false;
            $Behavior.ynsaUpadateView = function(ignoreBanner) {
            if(first_socialAd == false) {
                    setTimeout(function() {
                        var tempAr = [];
                        $(\'.ynsaAdId\').each(function() {
                            if(undefined != ignoreBanner && null != ignoreBanner && true == ignoreBanner && 2 == jQuery(this).data(\'adstype\')){
                            } else {
                                tempAr.push($(this).val());
                            }
                        });
                        var param = tempAr.join(\'-\');
                        $.ajaxCall(\'socialad.updateAdView\', \'ad_ids=\' + param);
        
                    }, 3000);
                    first_socialAd = true;
                }
            }
</script>';
}