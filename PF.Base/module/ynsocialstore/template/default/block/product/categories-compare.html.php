<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/10/16
 * Time: 15:02
 */
?>

<div class="ynstore_compare_product_choose">
    <div class="ynstore_compare_product_choose-content">
        <select class="form-control" id="ynstore_detail_compare_product_category">
            {foreach from=$aCategories key=Id item=aCategory}
            <option
                id="ynstore_detail_compare_product_select_{$aCategory.category_id}"
            "
            {if $iCategoryId == $aCategory.category_id}
            selected="selected"
            {/if}
            value="{$aCategory.category_id}" {if $aCategory.total < 2}disabled{/if} data-link="{$aCategory.compare_link}">{$aCategory.title} ({$aCategory.total})</option>
            {/foreach}
        </select>
    </div>
</div>
{if !$bIsHaveCurrent}
<script type="text/javascript">
    $Behavior.onChangeCategoryComparePage = function() {l}
        window.location.href = $('#ynstore_detail_compare_product_category option:selected').data('link');
    {r}
</script>
{/if}