<?php

if (Phpfox::isUser() && !Phpfox::isAdminPanel() && Phpfox::isModule('notification'))
{
    $iDelayTime = (int) Phpfox::getParam('fanot.display_notification_seconds') * 1000;
    $iRefreshTime = (int) Phpfox::getParam('fanot.notification_refresh_time') * 1000;
    $str = '';
	$str .= '<div id="fanot_box" class="fanotui"></div>';
	echo $str;
	?>
    <script language="javascript">
        $Behavior.ynfanotInitVar = function(){
            if (typeof($Core) != "undefined") {
                $Core.fanot.fanotDelay = <?php echo abs($iDelayTime); ?>;
                $Core.fanot.fanotUpdateDelay = <?php echo abs($iRefreshTime); ?>;
            }
        };
    </script>
	<?php
}
?>