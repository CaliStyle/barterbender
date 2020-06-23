<div id='yndirectory_manage_packages'>	
	{if isset($invoice_id) && (int)$invoice_id > 0}
		<div>
			<h3>{phrase var='payment_methods'}</h3>
			{module name='api.gateway.form'}			
		</div>
	{else}
	<form method="post" action="{url link='directory.manage-packages.id_'.$iBusinessid}" id="js_manage_packages" onsubmit="" enctype="multipart/form-data">

			<input type="hidden" name="val[business_id]" value="{$iBusinessid}" >
			<div class="help-block">
				{if isset($aPackageBusiness)}
					{phrase var='your_business_is_currently_using_package_name_quote' package_name=$aPackageBusiness.name}
				{else}
					{phrase var='please_select_a_package_to_start_your_business'}
				{/if}
			</div>
			<table class="yndirectory-table-full">
				<tr>
					<th ></th>
					<th>{phrase var='id'}</th>
					<th>{phrase var='package_name'}</th>
					<th>{phrase var='price'}</th>
					<th>{phrase var='valid_period'}</th>
				</tr>
				{foreach from=$aPackages item=aPackage}
				<tr>
						<td style="width: 50px;">
							
    	                	<div class="radio ync-radio-custom"><label><input type="radio" name="val[package_id]" value="{$aPackage.package_id}"
    	                	{if isset($aBusiness) && ($aBusiness.package_id == $aPackage.package_id)}
	                			checked="checked"
    	                	{/if}
    	                /><i class="ico ico-circle-o mr-1"></i></label></div>
						</td>
						<td>{$aPackage.package_id}</td>
						<td>{$aPackage.name}</td>
						<td>{$aPackage.fee}</td>
						<td>
							{if $aPackage.expire_type!=0}
								{$aPackage.expire_number}&nbsp;
							{/if}
							{if $aPackage.expire_type==1}
								{phrase var='day_s'}
							{elseif $aPackage.expire_type==2}
								{phrase var='week_s'}
							{elseif $aPackage.expire_type==3}
								{phrase var='month_s'}
							{else}
								{phrase var='never_expired'}
							{/if}
						</td>
				</tr>
				{/foreach}
			</table>
			<div class="yndirectory-button" style="margin-bottom: 10px;">
				<button type="submit" name="val[apply_package]" class="btn btn-sm btn-primary" id="apply_package" value="{phrase var='buy_new_package'}">{phrase var='buy_new_package'}</button>
			</div>

		<label>{phrase var='renewal_notification'}
		: {phrase var='before'}</label>
			<select class="form-control" name="val[renewal_notification]">
				<option value="1" {if isset($aBusiness.renewal_type) && $aBusiness.renewal_type==1}selected{/if}>{phrase var='1_day'}</option>
				<option value="2" {if isset($aBusiness.renewal_type) && $aBusiness.renewal_type==2}selected{/if}>{phrase var='1_week'}</option>
				<option value="3" {if isset($aBusiness.renewal_type) && $aBusiness.renewal_type==3}selected{/if}>{phrase var='1_month'}</option>
			</select>

			<div class="yndirectory-button main_break">
				<button type="submit" name="val[update_setting]" class="btn btn-sm btn-primary" id="update_setting" value="{phrase var='ok'}">{phrase var='ok'}</button>
			</div>
		</form>

	{/if}

</div>
