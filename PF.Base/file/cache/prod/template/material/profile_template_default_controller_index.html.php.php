<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php 

?>
<script>
  var bCheckinInit = false;
  $Behavior.prepareInit = function()
  {
    if($Core.Feed !== undefined) {
        $Core.Feed.sGoogleKey = '<?php echo Phpfox::getParam('core.google_api_key'); ?>';
        $Core.Feed.googleReady('<?php echo Phpfox::getParam('core.google_api_key'); ?>');
      }
  }
</script>

