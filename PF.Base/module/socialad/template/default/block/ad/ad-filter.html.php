{if $bIsAdminManage && !$bHideForm}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='search_filter'}
        </div>
    </div>
{/if}
<form id="js_ynsa_ad_list_form"
	data-ajax-action="socialad.changeAdListFilter"
	data-result-div-id="js_ynsa_ad_list"
	data-custom-event="ondatachanged"
	data-is-prevent-submit="true"
   {if $bHideForm}style="display:none;"{/if}
>
    {if $bIsAdminManage}
    <div class="panel-body">
    {/if}
        <div class="ynsaFixFloatDiv form-group table">
            <label for="name" class="table_left">
                {_p var='name'}:
            </label>
            <div class="table_right">
                <input class="form-control" id="name" type="text" name="val[keyword]" />
            </div>
        </div>
        <div class="ynsaFixFloatDiv form-group table">
            <label for="ad_status" class="table_left">
                {_p var='status'}:
            </label>
            <div class="table_right">
                <select class="form-control ynsaSelectMethod" id="ad_status" name="val[ad_status]" id="">
                    <option value="0" >{_p var='all'}</option>
                    {foreach from=$aAdStatuses item=aStatus}
                        <option {if $aStatus.id == $iFilterDefaultStatusId} selected="selected" {/if} value="{$aStatus.id}" >{$aStatus.phrase}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="ynsaFixFloatDiv form-group table">
            <label for="ad_type" class="table_left">
                {_p var='type'}:
            </label>
            <div class="table_right">
                <select class="form-control ynsaSelectMethod" name="val[ad_type]" id="ad_type">
                    <option value="0" >{_p var='all'}</option>
                    {foreach from=$aAdTypes item=aType}
                        <option value="{$aType.id}" >{$aType.phrase}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {if $bIsAdminManage}
    </div>
    {/if}
{if $iFilterCampaignId}
    <input type="hidden" name="val[ad_campaign_id]" value="{$iFilterCampaignId}"/>
{/if}

</form>
{if $bIsAdminManage && !$bHideForm}
</div>
{/if}
<script type="text/javascript">
$Behavior.ynsaInitAdListForm = function() {l}
    $Behavior.loadYnsocialAdSetupParam();
	$("#js_ynsa_ad_list_form").ajaxForm();

    $(".ynsaSelectMethod").change(function(){l}
        var status = $("[name='val[ad_status]'] option:selected").val()
        var type = $("[name='val[ad_type]'] option:selected").val()

        setCookie('ynad-filter-status',status,1)
        setCookie('ynad-filter-type',type,1)
    {r});
    $("[name='val[keyword]']").change(function(){l}
        var keyword = $("[name='val[keyword]']").val()
        setCookie('ynad-filter-keyword',keyword,1)
    {r});
    var status2 = getCookie('ynad-filter-status');
    var type2 = getCookie('ynad-filter-type');
    var keyword2 = getCookie('ynad-filter-keyword');
    if(keyword2 != ""){l}
        $("[name='val[keyword]']").val(keyword2)
    {r}
    if(status2 != ""){l}
        $("[name='val[ad_status]'] option[value='" + status2 +"']").prop('selected',true)
    {r}
    if(type2 != ""){l}
        $("[name='val[ad_type]'] option[value='" + type2 +"']").prop('selected',true)
    {r}
    $("#js_ynsa_ad_list_form").trigger('change');
    if($("#page_socialad_ad_index").length == 0){l}
        deleteCookie('ynad-filter-status');
        deleteCookie('ynad-filter-type');
        deleteCookie('ynad-filter-keyword');
    {r}
    if($("#page_socialad_admincp_ad_index").length == 0){l}
        deleteCookie('ynad-filter-status');
        deleteCookie('ynad-filter-type');
        deleteCookie('ynad-filter-keyword');
    {r}
{r}
</script>
