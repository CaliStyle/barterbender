<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>


<form method="post" action="{url link='current'}"  class="ynfr_add_edit_form"  id="ynfr_edit_campaign_sponsor_levels_form" onsubmit="" enctype="multipart/form-data">
    <div id="js_fundraising_block_sponsor_levels"class="js_fundraising_block page_section_menu_holder" style="display:none;">
        <div class="extra_info" >{phrase var='sponsor_form_notice'} </div>
        <div id="ynfr_sponsor_holder">
            <div id="ynfr_sholder" class="ynfr_sample_holder" style="display: none">
                <div class="table form-group">
                    <div class="table_left">
                        <table>
                            <tr>
                                <td>
                                    {phrase var='amount'}:
                                </td>
                                <td>
                                    <input type="text" class="ynfr required number ynfr_sponsor_level_amount form-control" name="val[sponsor_level][][amount]" value="" id="amount" size="30" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {phrase var='description'}:
                                </td>
                                <td>
                                    <input type="text" class="form-control ynfr required=" val[sponsor_level][][level_name]" value="" id="level_name" size="30" />
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="table_right">
                        <a href="#" onclick="ynfr_removeLevels(this);">{phrase var='remove_upper'}</a>
                    </div>
                </div>
            </div>
            {if !$bIsEdit}
            <div id="ynfr_sholder">
                <div class="table form-group">
                    <div class="table_left">
                        <table>
                            <tr>
                                <td>
                                    {phrase var='amount'}:
                                </td>
                                <td>
                                    <input type="text" class="ynfr form-control required number ynfr_sponsor_level_amount" name="val[sponsor_level][1][amount]" value="" id="amount" size="30" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {phrase var='description'}:
                                </td>
                                <td>
                                    <input type="text" class="ynfr form-control required" name="val[sponsor_level][1][level_name]" value="" id="level_name" size="30" />
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="table_right">
                        <a href="#" onclick="removeLevels(this);">{phrase var='remove_upper'}</a>
                    </div>
                </div>
            </div>
            {else}
            {foreach from=$aForms.sponsor_level key=iKey item=aSponsor}
            {if isset($aSponsor.amount) && !empty($aSponsor.amount) && isset($aSponsor.level_name) && !empty($aSponsor.level_name) }
            <div id="ynfr_sholder">
                <div class="table form-group">
                    <div class="table_left">
                        <table>
                            <tr>
                                <td>
                                    {phrase var='amount'}:
                                </td>
                                <td>
                                    <input type="text" class="ynfr form-control required number ynfr_sponsor_level_amount" name="val[sponsor_level][{$iKey}][amount]" value="{$aSponsor.amount}" id="amount" size="30" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {phrase var='description'}:
                                </td>
                                <td>
                                    <input type="text" class="ynfr form-control required" name="val[sponsor_level][{$iKey}][level_name]" value="{$aSponsor.level_name}" id="title" size="30" />
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="table_right">
                        <a href="#" onclick="ynfr_removeLevels(this);">{phrase var='remove_upper'}</a>

                    </div>
                </div>
            </div>
            {/if}
            {/foreach}
            {/if}
        </div>
        <div class="table_clear">
            <button id="add_level" type="button" class="btn btn-sm btn-primary" value="{phrase var='add_level'}" onclick="ynfr_addMoreLevels();">{phrase var='add_level'}</button>
        </div>

        <div class="table_clear">
            <button type="submit" name="val[submit_sponsor_levels]" value="{phrase var='save'}" class="btn btn-sm btn-primary" onclick="$('.ynfr_sample_holder').remove();">{phrase var='save'}</button>
            {if $bIsEdit && $aForms.is_draft == 1}
                <button type="submit" name="val[publish_sponsor_levels]" value="{phrase var='publish'}" class="btn btn-sm btn-primary">{phrase var='publish'}</button>
            {/if}
        </div>
    </div>
 </form>

