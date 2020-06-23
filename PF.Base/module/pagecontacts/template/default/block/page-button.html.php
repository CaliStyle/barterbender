<?php
/**
 * @copyright		YouNet Company
 * @author  		MinhNTK
 * @package  		Module_PageContact
 * @version 		3.01
 */
defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script>
	function showContactPopup()
	{
		$Core.ajaxBox('pagecontacts.popup');
	}
</script>

<style>
	.yn_pagecontact_button{
		display: block;
		text-align: center;
		padding:10px;
	}

	.yn_pagecontact_button input{
		width: 100%;
		margin-bottom: 15px;
		
	}
</style>


{/literal}
<div class="yn_pagecontact_button">
	{if !$bIsSetting}
		<button type="button" class="button btn btn-success" onclick="tb_show('{phrase var='pagecontacts.contact_us'}', $.ajaxBox('pagecontacts.popup', 'height=300&amp;width=550&amp;iPageId={$iPageId}')); return false;">{phrase var='pagecontacts.contact_us'}</button>
	{else}
		<button type="button" class="button btn btn-success" onclick="window.location.href ='{$sLink}'; return false;">{phrase var='pagecontacts.contact_us'}</button>
	{/if}
	<div class="clear"></div>
</div>