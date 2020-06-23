<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aItems)}
<select id="video_categories" name="val[category][0]" class="form-control">
    <option value="">{_p var='select'}:</option>
    {foreach from=$aItems item=aCategory}
        <option value="{$aCategory.category_id}" {if isset($aCategory.active)}selected="selected"{/if}>
            {$aCategory.name|convert}
        </option>
        {foreach from=$aCategory.sub item=aSubCategory}
            <option value="{$aSubCategory.category_id}" {if isset($aSubCategory.active)}selected="selected"{/if}>
                - {$aSubCategory.name|convert}
            </option>
            {foreach from=$aSubCategory.sub item=aSubSubCategory}
                <option value="{$aSubSubCategory.category_id}"
                        {if isset($aSubSubCategory.active)}selected="selected"{/if}>
                    -- {$aSubSubCategory.name|convert}
                </option>
                {foreach from=$aSubSubCategory.sub item=aSubSubSubCategory}
                    <option value="{$aSubSubSubCategory.category_id}"
                            {if isset($aSubSubSubCategory.active)}selected="selected"{/if}>
                        --- {$aSubSubSubCategory.name|convert}
                    </option>
                {/foreach}
            {/foreach}
        {/foreach}
    {/foreach}
</select>
{else}
<div class="p_4">
    {_p var='no_categories_added'}
</div>
{/if}