<?php 
	try {
		if (Phpfox::isUser() && !Phpfox::isAdminPanel() && Phpfox::isModule('notification'))
		{
			$iDelayTime = (int) Phpfox::getParam('fanot.display_notification_seconds') * 1000;
			$iRefreshTime = (int) Phpfox::getParam('fanot.notification_refresh_time') * 1000;
			$str = '';
			$str .= '<div id="fanot_box" class="fanotui"></div>';
			?>
			<script language="javascript">
				$Behavior.ynfanotInitVar = function(){
					if ($('#fanot_box').length){}else
					{
						$('body').prepend('<?php echo $str; ?>');
					}
					if (typeof($Core) != "undefined") {
						$Core.fanot.fanotDelay = <?php echo abs($iDelayTime); ?>;
						$Core.fanot.fanotUpdateDelay = <?php echo abs($iRefreshTime); ?>;
					}
				};
			</script>
			<?php
		}

		if(Phpfox::isModule('fanot'))
		{
			$bgcolor = (Phpfox::getParam('fanot.notification_bgcolor')!='') ? Phpfox::getParam('fanot.notification_bgcolor') : '#CAD1DE';
			?>
			<style type="text/css">
				.fanot_content, .fanotui .fanot_item:hover {
					background-color: <?php echo $bgcolor; ?>!important;
				}
				.fanotui .fanot_selected {background:<?php echo $bgcolor; ?>!important;}
			</style>
			<?php
		}
	}
	catch (Exception $e) {

	}
?>
