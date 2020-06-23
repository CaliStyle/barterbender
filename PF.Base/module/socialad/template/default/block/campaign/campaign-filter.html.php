<form id="js_ynsa_campaign_list_form" 
	data-ajax-action="socialad.changeCampaignListFilter"  
	data-result-div-id="js_ynsa_campaign_list" 
	data-custom-event="ondatachanged">
{if $bIsAdminManage}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='search_filter'}
        </div>
    </div>
    <div class="panel-body">
{/if}
        <div class="form-group ynsaFixFloatDiv table">
            <label for="campaign_status" class="table_left">
                {_p var='status'}:
            </label>
            <div class="table_right">
                <select class="form-control ynsaSelectMethod" name="val[campaign_status]" id="campaign_status">
                    <option value="0" >{_p var='all'}</option>
                    {foreach from=$aCampaignStatus item=aStatus}
                        <option {if $aStatus.id == $iCampaignDefaultStatus} selected="selected" {/if} value="{$aStatus.id}" >{$aStatus.phrase}</option>
                    {/foreach}
                </select>
            </div>
        </div>
{if $bIsAdminManage}
    </div>
</div>
{/if}
</form>

<script type="text/javascript">
$Behavior.ynsaInitCampaignListForm = function() {l} 
	$("#js_ynsa_campaign_list_form").ajaxForm();
{r}
</script>
