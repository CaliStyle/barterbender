<?php
;
if (Phpfox::isModule('socialbridge'))
{
    $sText = _p('socialbridge.manage_social_accounts');
    $sLink = Phpfox::getLib('url')->makeUrl('socialbridge.setting');
    $sAttach = "<li role='presentation' id='socialbridge_setting'><a href='".$sLink."'><i class='fa fa-share-alt'></i>".$sText."</a></li>";
?>

<script type="text/javascript">
	$Ready(function()
	{
        if($("#socialbridge_setting").length) return false;
		$('#header_menu .feed_form_menu .nav_header ul li:nth-child(3)').after("<?php echo $sAttach; ?>");
		$('#section-header #user_sticky_bar .dropdown-menu-right li:nth-child(3)').after("<?php echo $sAttach; ?>");
	});
</script>
<?php
}
;
?>