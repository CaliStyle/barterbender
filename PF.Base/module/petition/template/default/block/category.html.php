<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="sub_section_menu">
	<ul class="action">
	{foreach from=$aCategories item=aCategory}
		<li class="{if $iCategoryPetitionView == $aCategory.category_id} active{/if}"><a href="{$aCategory.url}" class="ajax_link">{$aCategory.name|convert|clean}</a></li>
	{/foreach}
	</ul>
</div>