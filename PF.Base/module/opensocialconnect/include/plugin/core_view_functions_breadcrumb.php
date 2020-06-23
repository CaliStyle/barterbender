<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::isModule('opensocialconnect') && !Phpfox::isUser()) {
    // Providers
    Phpfox::getService('opensocialconnect.providers')->viewLoginHeader();
    echo '<script type="text/javascript">
      $Behavior.ynSocialConnectAddProviders = function() {
        if ($(\'.opensocialconnect_holder_header\').length < 2) {
          var guestActions = $(\'[data-component="guest-actions"]\');
          if (guestActions.length) {
            if ($(\'#opensocialconnect_holder_header\').length) {
              guestActions.prepend($(\'#opensocialconnect_holder_header\'));
            }
          }
        }
        $Behavior.ynSocialConnectAddProviders = function(){};
      };
    </script>';
};
