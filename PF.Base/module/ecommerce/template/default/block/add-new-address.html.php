<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_ecommerce_block_add_new_address" class="js_ecommerce_block page_section_menu_holder">
	<form method="post" id="ynecommerce_add_new_address" onsubmit="return onSubmitNewAddress()" enctype="multipart/form-data">
		<div>				
			  {if isset($iAddressId)}
			  <input type="hidden" name="address_id" value="{$iAddressId}" id="address_id" />
			  {/if}
			 <div class="table form-group">
				<div class="table_left">
					{phrase var='contact_name'}:
				</div>
				<div class="table_right">
					<input class="form-control" type="text" name="contact_name" value="{value type='input' id='contact_name'}" id="contact_name" size="40"/>
				</div>
			 </div>

			<div class="table form-group">
				<div class="table_left">
					{phrase var='country_region'}:
				</div>
				<div class="table_right">
						{select_location}						
						<div style="padding: 5px 0px 0px;" id="js_country_child_id">
						</div>
				</div>
			 </div>

			 <div class="table form-group">
				<div class="table_left">
					{phrase var='street_address'}:
				</div>
				<div class="table_right">
					<input class="form-control" type="text" name="address_street" value="{value type='input' id='address_street'}" id="address_street" size="40"  placeholder="{phrase var='street_name'}"/>
					<input class="form-control" style="margin-top:3px;" type="text" name="address_street_2" value="{value type='input' id='address_street_2'}" id="address_street_2" size="40" placeholder="{phrase var='apartment_suite_unit_etc_optional'}"/>
				</div>
			 </div>

			 <div class="table form-group">
				<div class="table_left">
					{phrase var='city'}:
				</div>
				<div class="table_right">
					<input class="form-control" type="text" name="address_city" value="{value type='input' id='address_city'}" id="address_city" size="40"/>
				</div>
			 </div>


			 <div class="table form-group">
				<div class="table_left">
					{phrase var='zip_postal_code'}:
				</div>
				<div class="table_right">
					<input class="form-control" type="text" name="address_postal_code" value="{value type='input' id='address_postal_code'}" id="address_postal_code" size="40"/>
				</div>
			 </div>

			  <div class="table form-group">
				<div class="table_left">
					{phrase var='tel'}:
				</div>
				<div class="table_right">
					<input class="form-control" type="text" name="address_country_code" value="{value type='input' id='address_country_code'}" id="address_country_code" size="3"/>
					<input class="form-control" type="text" name="address_city_code" value="{value type='input' id='address_city_code'}" id="address_city_code" size="3"/>
					<input class="form-control" type="text" name="address_phone_number" value="{value type='input' id='address_phone_number'}" id="address_phone_number" size="10"/>
				</div>
			 </div>

			 <div class="table form-group">
				<div class="table_left">
					{phrase var='mobile'}:
				</div>
				<div class="table_right">
					<input class="form-control" type="text" name="address_mobile_number" value="{value type='input' id='address_mobile_number'}" id="address_mobile_number" size="40"/>
				</div>
			 </div>
			<div class="p_top_8">
				<button type="submit" name="save_address" id="save_address" class="btn btn-sm btn-primary" >{phrase var='save'}</button>
				<button type="button" onclick="return js_box_remove(this);" name="cancel_address"  id="cancel_address" class="btn btn-sm btn-default" >{phrase var='cancel'}</button>
			</div>		
		</div>	
	</form>
</div>
{literal}
<script type="text/javascript">

	$("#country_iso option[value={/literal}{if isset($aForms.country_iso)}{$aForms.country_iso}{/if}{literal}]").attr("selected", "selected");
	{/literal}{if isset($aForms.country_child_id)}{literal}
		sExtra = '&country_child_filter=true';
		$.ajaxCall('core.getChildren', 'country_iso={/literal}{$aForms.country_iso}{literal}&country_child_id={/literal}{$aForms.country_child_id}{literal}'+ sExtra, 'GET');
	{/literal}{/if}{literal} 

	$('#country_iso').bind('change',function()
	{	
		var sChildValue = $('#js_country_child_id_value').val();
		var sExtra = '';
		$('#js_country_child_id').html('');
		$('#country_iso').after('<span id="js_cache_country_iso">' + $.ajaxProcess('no_message') + '</span>');
		if ($('#js_country_child_is_search').length > 0)
		{
			sExtra += '&country_child_filter=true';
		}		
		$.ajaxCall('core.getChildren', 'country_iso=' + this.value + '&country_child_id=' + sChildValue + sExtra, 'GET');

	});
	
	function onSubmitNewAddress() {
        $('.error').remove();
        if(
            checkRequired($('#ynecommerce_add_new_address #contact_name')) &&
            checkRequired($('#ynecommerce_add_new_address #country_iso')) &&
            checkRequired($('#ynecommerce_add_new_address #address_street')) &&
            checkRequired($('#ynecommerce_add_new_address #address_city')) &&
            checkRequired($('#ynecommerce_add_new_address #address_postal_code')) &&
            checkRequired($('#ynecommerce_add_new_address #address_phone_number')) &&
            checkRequired($('#ynecommerce_add_new_address #address_mobile_number'))
        ){
            $.ajaxCall('ecommerce.saveAddress', $('#ynecommerce_add_new_address').serialize(), 'post');
            return false;
        }
        return false;
    }

	function checkRequired(element){
		if(element.val() == '' || parseInt(element.val()) == -1){
		    element.parent().parent().after("<span class='error'>"+oTranslations['auction.this_field_is_required']+"</span>");
			return false;
		}
		else{		
			return true;
		}
	}
</script>
<style type="text/css">
	.error{
		color:red;
		font-weight: italic;
	}
</style>
{/literal}