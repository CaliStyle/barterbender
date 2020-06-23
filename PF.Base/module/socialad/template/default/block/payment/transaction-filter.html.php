{if $bIsAdminManage}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='search_filter'}
        </div>
    </div>
{/if}
<form id="js_ynsa_transaction_list_form"
	data-ajax-action="socialad.changePaymentListFilter"  
	data-result-div-id="js_ynsa_payment_list" 
	data-custom-event="ondatachanged">
    <div class="panel-body">
        <div class="form-group ynsaFixFloatDiv table">
            <label class="table_left" for="transaction_method_id">
                {_p var='payment_method'}:
            </label>
            <div class="table_right">
                <select class="form-control ynsaMultipleChosen ynsaSelectMethod" name="val[transaction_method_id]" id="transaction_method_id">
                    <option value="0" >{_p var='all_methods'}</option>
                    {foreach from=$aTransactionMethod item=aMethod}
                        <option value="{$aMethod.id}" >{$aMethod.phrase}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="table form-group ynsaFixFloatDiv">
            <label class="table_left" for="transaction_status_id">
                {_p var='status'}:
            </label>
            <div class="table_right">
                <select class="form-control ynsaMultipleChosen ynsaSelectMethod" name="val[transaction_status_id]" id="transaction_status_id">
                    <option value="0" >{_p var='all'}</option>
                    {foreach from=$aTransactionStatus item=aStatus}
                        <option value="{$aStatus.id}" >{$aStatus.phrase}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</form>
{if $bIsAdminManage}
</div>
{/if}
<script type="text/javascript">
$Behavior.ynsaInitTransactionListForm = function() {l} 
	$("#js_ynsa_transaction_list_form").ajaxForm();
{r}
</script>
