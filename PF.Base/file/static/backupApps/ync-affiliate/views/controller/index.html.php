{if !empty($sError)}
    <div class="error_message">{$sError}</div>
{else}
    <div class="yncaffiliate_commission_rules">
        <div class="form-group">
            <label class="fw-400 fw-bold">{_p('User group')}</label>
            <select name="val[group_user]" id="ynaf_group_user" class="form-control">
                {foreach from=$aUserGroup key=iKey item=aItem}
                    <option value="{$aItem.user_group_id}" {if $iUserGroupId == $aItem.user_group_id}selected{/if}>{softPhrase var=$aItem.title}</option>
                {/foreach}
            </select>
        </div>
        <div id="ynaff_loading" style="display: none;padding-top:10px;font-size:20px;" class="t_center"><i class="fa fa-spin fa-circle-o-notch"></i></div>
        <div class="table-responsive" id="ynaf_commisison_rule">
            <table class="table table-bordered yncaffiliate_table">
                <thead>
                <tr>
                    <th>{_p('Payment Types')}</th>
                    <th>{_p('Level 1')}</th>
                    <th>{_p('Level 2')}</th>
                    <th>{_p('Level 3')}</th>
                    <th>{_p('Level 4')}</th>
                    <th>{_p('Level 5')}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aItems key=iKey item=aItem}
                    <tr>
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
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    {literal}
    <script type="text/javascript">
        $Behavior.onChangeUserGroup = function(){
            $('#ynaf_group_user').on('change',function(){
                var data = $(this).val();
                $('#ynaf_commisison_rule').hide();
                $('#ynaff_loading').show();
                $.ajaxCall('yncaffiliate.loadCommissionRule','group_id='+ data);
            });
        }
    </script>
    {/literal}
{/if}
