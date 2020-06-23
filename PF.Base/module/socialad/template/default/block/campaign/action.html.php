{if $aCampaign.status_phrase != 'Deleted'}
<div class="ynsaActionList">
    <a href="#" class="js_ynsa_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
    <div class="link_menu">
        <ul class="dropdown-menu">
            {if $aCampaign.can_edit_campaign}
            <li>
                <a href="#" onclick="tb_show('{_p var='edit_campaign'}', $.ajaxBox('socialad.actionCampaign', 'height=400&width=390&action=show_edit_box&campaign_id={$aCampaign.campaign_id}')); return false;">
                    {_p var="edit_name"}
                </a>
            </li>
            {/if}

            {if $aCampaign.can_delete_campaign}
            <li>
                <a href="#"  onclick="$Core.jsConfirm({l}message: '{_p var='are_you_sure_delete_campaign'}'{r}, function () {l}$.ajaxCall('socialad.actionCampaign', 'action=delete&campaign_id={$aCampaign.campaign_id}');{r}, function () {l}{r});return false;">
                    {_p var="delete"}
                </a>
            </li>
            {/if}

        </ul>
    </div>
</div>
{/if}

{literal}
<script type="text/javascript">
    $Behavior.ynsaInitDropDownMenu = function() {
        ynsocialad.helper.initDropdownMenu();
    }
</script>
{/literal}