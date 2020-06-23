<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-body">
        <form method="post" enctype="multipart/form-data" action="{url link='admincp.advancedfooter.addsocial'}" onsubmit="$Core.onSubmitForm(this, true);">
            {if $bIsEdit}
                {if $bIsEdit && $aForms.parent_id}
                    <div><input type="hidden" name="sub" value="{$iEditId}" /></div>
                {else}
                    <div><input type="hidden" name="edit" value="{$iEditId}" /></div>
                {/if}
            {/if}
            <div class="table form-group">
                <div class="table_left">
                    {_p var='Social Icon'}:
                </div>
                <div class="table_right">
                    <select name="val[icon]" class="form-control">
                        {foreach from=$aSocial key=aKey item=aItem}
                            <option value="{$aKey}" {if $bIsEdit and $aKey == $aForms.icon}selected{/if}>{$aItem.phrase}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {_p var='Link'}:
                </div>
                <div class="table_right">
                    <input class="form-control" name="val[link]" value="{if !empty($aForms.link)}{$aForms.link}{/if}" type="text" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
            </div>
        </form>
    </div>
</div>