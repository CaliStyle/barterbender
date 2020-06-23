<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 * 
 */?>
 
<div id="js_add_new_category"></div>
{foreach from=$aItems item=aItem}
	{template file='resume.block.category-form'}
{foreachelse}
<div class="p_4">
	{_p var='no_categories_added'}
</div>
{/foreach}