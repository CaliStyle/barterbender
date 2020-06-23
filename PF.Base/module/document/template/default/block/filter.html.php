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
 * @package         YouNet_Document
 */
?>
<form method="post" action="{if empty($sCategoryUrl)}{url link=$sParentLink}{else}{url link=''$sParentLink'.category'$sCategoryUrl''}{/if}">
    {phrase var='keywords'}:
    <div class="p_4">
        {filter key='keyword'}
    </div>    
    
    <div class="p_top_4">
        {phrase var='sort'}:
        <div class="p_4">
            {filter key='sort'} {phrase var='in_sorting_order'} {filter key='sort_by'}
        </div>    
    </div>    
    
    <div class="p_top_8">
        <input name="search[submit]" value="{phrase var='submit'}" class="button btn btn-primary btn-sm" type="submit" />
        <input name="search[reset]" value="{phrase var='reset'}" class="button btn btn-default btn-sm" type="submit" />
    </div>    
</form>