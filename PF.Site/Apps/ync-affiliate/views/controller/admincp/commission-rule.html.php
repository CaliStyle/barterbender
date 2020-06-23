<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:37
 */

defined('PHPFOX') or exit('NO DICE!');
?>


<!------------------------------------------>
{if !empty($sError)}
    <br>
    <div class="error_message">{$sError}</div>
{else}
<form action="" method="post">
    <div class="panel panel-default">
        <!-- Filter Search Form Layout -->
        <div class="panel-heading">
            <div class="panel-title">
                {_p('Commission Rules')}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="group_user">{_p('User group')}:</label>
                <select name="val[group_user]" id="group_user" class="form-control">
                    {foreach from=$aUserGroup key=iKey item=aItem}
                    <option value="{$aItem.user_group_id}" {if $iUserGroupId == $aItem.user_group_id}selected{/if}>{softPhrase var=$aItem.title}</option>
                    {/foreach}
                </select>
                <!-- Submit Buttons -->
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" id="yn_filter_affiliate_submit" name="val[submit]" value="{_p('Submit')}" class="btn btn-primary"/>
        </div>
    </div>
</form>
<!------------------------------------------>
{if count($aItems) > 0}
<div class="table-responsive ">
    <table class="table table-admin">
        <thead>
            <tr>
                <th class="w20"></th>
                <th class="t_center">{_p('Payment Types')}</th>
                <th class="t_center">{_p('Level 1')}</th>
                <th class="t_center">{_p('Level 2')}</th>
                <th class="t_center">{_p('Level 3')}</th>
                <th class="t_center">{_p('Level 4')}</th>
                <th class="t_center">{_p('Level 5')}</th>
                <th class="t_center">{_p('Active')}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aItems key=iKey item=aItem}
            <tr>
                <td class="t_center">
                    <a href="#" class="js_drop_down_link" title="Manage"></a>
                    <div class="link_menu">
                        <ul>
                            <li><a class="popup" href="{url link='admincp.yncaffiliate.edit-commission-rule' rulemap=$aItem.rulemap_id rule=$aItem.rule_id group=$aItem.user_group_id}">{_p var='Edit'}</a></li>
                            <li><a href="{url link='admincp.yncaffiliate.edit-commission-rule' reset=$aItem.rulemap_id group=$aItem.user_group_id}" class="sJsConfirm">{_p var='Clear Settings'}</a></li>
                        </ul>
                    </div>
                </td>
                <td align="left">
                    {_p var=$aItem.rule_title}
                </td>
                <td align="center">
                    {if isset($aItem.level_1)}{_p var=$aItem.level_1}{/if}
                </td>
                <td align="center">
                    {if isset($aItem.level_2)}{_p var=$aItem.level_2}{/if}
                </td>
                <td align="center">
                    {if isset($aItem.level_3)}{_p var=$aItem.level_3}{/if}
                </td>
                <td align="center">
                    {if isset($aItem.level_4)}{_p var=$aItem.level_4}{/if}
                </td>
                <td align="center">
                    {if isset($aItem.level_5)}{_p var=$aItem.level_5}{/if}
                </td>
                <td class="t_center">
                    <div class="js_item_is_active"{if $aItem.is_active == 0} style="display:none;"{/if}>
                    <a href="#?call=yncaffiliate.updateRuleStatus&amp;id={$aItem.rulemap_id}&amp;active=0" class="js_item_active_link" title="{_p var='Unable'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aItem.is_active == 1} style="display:none;"{/if}>
                    <a href="#?call=yncaffiliate.updateRuleStatus&amp;id={$aItem.rulemap_id}&amp;active=1" class="js_item_active_link" title="{_p var='Enable'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{else}
<br>
<p>{_p('No Commission Rules Found')}</p>
{/if}
{/if}
{literal}
<script type="text/javascript">
    $Ready(function(){
        if($('.apps_menu').length == 0) return false;
        $('.apps_menu > ul').find('li:eq(3) a').addClass('active');
    });
</script>
{/literal}

