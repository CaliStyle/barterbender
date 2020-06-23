<div class="yncaffiliate_commission_tracking">
    {if $iPage <=1 }
	<div class="yncaffiliate_search_form form-inline">
        <form action="{url link='affiliate.commission-tracking'}" method="GET">
            <div class="yncaffiliate_search_form_inner clearfix">
                <div class="form-group padding flexone">
                    <label>{_p var='client_name'}</label>
                    <div class="form-inline">
                        <input type="text" name="search[client_name]" value="{value type='input' id='client_name'}">
                    </div>
                </div>
                <div class="form-group padding">
                    <label>{_p var='purchased_date'}</label>
                    <div class="form-inline clearfix sm_padding_parent">
                        <div class="form-group yncaffiliate_datetime_picker_parent sm_padding">
                            {select_date prefix='start_time_' id='_from' start_year='-10' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true }
                        </div>
                        <div class="form-group yncaffiliate_datetime_picker_parent sm_padding">
                            {select_date prefix='end_time_' id='_end_time' start_year='-10' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true }
                        </div>
                    </div>
                </div>
                <div class="form-group padding flexone">
                    <label>{_p var='payment_type'}</label>
                    <div class="form-inline">
                        <select name="search[payment_type]" class="form-control">
                            <option value="">{_p var='any'}</option>
                            {if count($aRules)}
                                {foreach from=$aRules item=aRule key=iKey}
                                <option value="{$aRule.rule_id}" {value type='select' id='payment_type' default=$aRule.rule_id}>{_p var=$aRule.rule_title}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>
                <div class="form-group padding flexone">
                    <label>{_p var='status'}</label>
                    <div class="form-inline">
                        <select class="form-control" name="search[status]">
                            <option value="">{_p var='any'}</option>
                            <option value="delaying" {value type='select' id='status' default = 'delaying'}>{_p var='Delaying'}</option>
                            <option value="waiting" {value type='select' id='status' default = 'waiting'}>{_p var='Waiting'}</option>
                            <option value="approved" {value type='select' id='status' default = 'approved'}>{_p var='Approved'}</option>
                            <option value="denied" {value type='select' id='status' default = 'denied'}>{_p var='Denied'}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-left">
                <button type="submit" class="btn btn-primary fw-bold">{_p var='search'}</button>
            </div>
        </form>
	</div>

	<ul class="clearfix">
		<li class="col-md-4 yncaffiliate_item">
			<div class="yncaffiliate_item_inner">
				<div class="col-left">{$iTotalApproved}</div>
				<div class="col-right approved">
					<div>
						<i class="fa fa-check-circle" aria-hidden="true"></i>
						<strong>{_p('Approved')}</strong>
					</div>
					<p>{_p var='total_approved_commissions'}</p>
				</div>
			</div>
		</li>
		<li class="col-md-4 yncaffiliate_item">
			<div class="yncaffiliate_item_inner">
				<div class="col-left">{$iTotalDelaying}</div>
				<div class="col-right delaying">
					<div>
						<i class="fa fa-history" aria-hidden="true"></i>
						<strong>{_p('Delaying')}</strong>
					</div>
					<p>{_p var='total_delaying_commissions'}</p>
				</div>
			</div>
		</li>
		<li class="col-md-4 yncaffiliate_item">
			<div class="yncaffiliate_item_inner">
				<div class="col-left">{$iTotalWaiting}</div>
				<div class="col-right waiting">
					<div>
						<i class="fa fa-hourglass-end" aria-hidden="true"></i>
						<strong>{_p var='waiting'}</strong>
					</div>
					<p>{_p var='total_waiting_commissions'}</p>
				</div>
			</div>
		</li>
	</ul>
    {/if}
    {if count($aTrackings)}
        {if !PHPFOX_IS_AJAX}
        <div id="tableCommissionTracking" class="table-responsive">
            <table class="table table-bordered yncaffiliate_table">
                <thead>
                    <tr>
                        <th>{_p var='purchased_date'}</th>
                        <th>{_p var='client_name'}</th>
                        <th>{_p var='payment_type'}</th>
                        <th>{_p var='total_amount'}</th>
                        <th>{_p var='commission_rate'}</th>
                        <th>{_p var='commission_amount'}</th>
                        <th>{_p var='commission_points'}</th>
                        <th>{_p var='client_relation'}</th>
                        <th>{_p var='reason'}</th>
                        <th>{_p var='status'}</th>
                    </tr>
                </thead>
                {else}
                    <table id="page2" style="display: none" class="table table-bordered yncaffiliate_table">
                {/if}
                <tbody>
                    {foreach from=$aTrackings item=aItem key=iKey}
                        <tr>
                            <td>{$aItem.time_stamp|date:'core.global_update_time'}</td>
                            <td><a href="{url link=$aItem.client_username}" title="{$aItem.client_name|clean}">{$aItem.client_name|clean}</a></td>
                            <td>{_p var=$aItem.rule_title}</td>
                            <td>{$aItem.purchase_symbol}{$aItem.purchase_amount|number_format:2}</td>
                            <td>{$aItem.commission_rate|number_format:2}%</td>
                            <td>{$aItem.purchase_symbol}{$aItem.commission_amount|number_format:2}</td>
                            <td>{$aItem.commission_points|number_format:2}</td>
                            <td>{$aItem.relation}</td>
                            <td>{$aItem.reason|clean}</td>
                            <td>{_p var=$aItem.status}</td>
                        </tr>
                    {/foreach} 
                </tbody>
            </table>
        {if !PHPFOX_IS_AJAX}
        </div>
        {/if}
        {pager}
    {else}
        {if !PHPFOX_IS_AJAX}
        <div class="p_4">
            {_p var='no_commissions_found'}
        </div>
        {/if}
    {/if}
</div>

{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.loadMoreLinkTracking = function () {
        if ($('#page2').length > 0 && $('#page2 tbody').length > 0 && $('#tableCommissionTracking tbody').length > 0)
        {
            $('#tableCommissionTracking tbody').append($('#page2 tbody').html());
            $('#page2').remove();
        }
    }
</script>
{/literal}