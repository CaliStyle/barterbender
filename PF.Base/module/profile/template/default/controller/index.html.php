<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<script>
  var bCheckinInit = false;
  $Behavior.prepareInit = function()
  {l}
    if($Core.Feed !== undefined) {l}
        $Core.Feed.sGoogleKey = '{param var="core.google_api_key"}';
        $Core.Feed.googleReady('{param var="core.google_api_key"}');
      {r}
  {r}
</script>
