<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 * @package  		Module FoxFavorite
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<h1>MENU</h1>
<div class="sub_section_menu">
	<ul>
	{foreach from=$aModules item=aModule}
		<li><a href="{$aModule.url}" class="ajax_link">{$aModule.title|convert|clean}</a></li>
	{/foreach}
	</ul>
</div>