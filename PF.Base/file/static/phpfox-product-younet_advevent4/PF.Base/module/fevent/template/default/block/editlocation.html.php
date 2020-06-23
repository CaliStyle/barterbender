<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>

<div class="form-inline">
	
	<div class="form-group">
		<input id="el_location" class="form-control" type="text" value="{_p var="fevent.location"}..." onfocus="if(this.value=='{_p var="fevent.location"}...'){l}this.value=''{r}" onblur="if($.trim(this.value)==''){l}this.value='{_p var="fevent.location"}...'{r}" />
	</div>
	
	<div class="form-group">
		<input id="el_city" type="text" class="form-control" value="{_p var="fevent.city"}..." onfocus="if(this.value=='{_p var="fevent.city"}...'){l}this.value=''{r}" onblur="if($.trim(this.value)==''){l}this.value='{_p var="fevent.city"}...'{r}" />
	</div>
</div>

<br/>

<div id="gmap" style="width:700px; height:300px;">


	GMap holder
</div>

{literal}

<script type="text/javascript">
	ynfeIndexPage.initLocationBlock();	
</script>

<style type="text/css">
#gmap{
	width: 100% !important;
}
</style>

{/literal}