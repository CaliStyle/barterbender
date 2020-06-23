<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: price.html.php 3533 2011-11-21 14:07:21Z Raymond_Benc $
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<li class="customrow active pref_{$sKeyVar}">
	<div class="yn_jh_manidiv">
		<a href="#" title="{if $is_active == "1"}{phrase var='advancedmarketplace.switch_off'}{else}{phrase var='advancedmarketplace.switch_on'}{/if}" class="bullet btn1 {if $is_active == "1"}on{else}off{/if} onoffswitch" ref="{$sKeyVar}">&nbsp;</a>
		<a href="#" title="{phrase var='advancedmarketplace.move_up'}" class="btn1 up">&nbsp;</a>
		<a href="#" title="{phrase var='advancedmarketplace.move_down'}" class="btn1 down">&nbsp;</a>
		<a href="#" title="{phrase var='advancedmarketplace.edit'}" class="btn1 edit yn_jh_cusgroup_edit" ref="{$sKeyVar}">&nbsp;</a>
		<a href="#" title="{phrase var='advancedmarketplace.save'}" class="btn1 save yn_jh_cusgroup_save" ref="{$sKeyVar}">&nbsp;</a>
		<a href="#" title="{phrase var='advancedmarketplace.delete'}" class="btn1 delete yn_jh_cusgroup_delete" ref="{$sKeyVar}">&nbsp;</a>
		<img class="ajxloader" src="{param var='core.path_actual'}PF.Site/Apps/p-advmarketplace/assets/image/default/ajxloader.gif" />
	</div>
	<div>
		<input {*disabled="disabled" *}type="text" class="value ref_{$sKeyVar}" name="customfieldgroup[{$sKeyVar}]" value="{$sText}" ref="{$sKeyVar}"/>
	</div>
</li>