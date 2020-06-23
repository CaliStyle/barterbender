<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>

<div class="bidincrement_form_msg">
</div>
<form id="bidincrement_form" method="post" action="#" onkeypress="return event.keyCode != 13;" onsubmit="return false;">
	<div class="price_and_buy">
		<div class="title">
			<h3>{phrase var='ecommerce.price_and_buy_it_now'}</h3>
		</div>
		
		<div class="form-inline">
			{phrase var='ecommerce.fixed_price_buy_it_now_price'} >= &nbsp;
			<input type="text" name="val[limit_percent_buy_it_now_price]" value="{value type='input' id='limit_percent_buy_it_now_price' default=100}" class="form-control limit_percent_buy_it_now_price" /> 
			&nbsp;% * {phrase var='ecommerce.reserve_price'}
		</div>
		
		<div class="table form-group">
			<div class="table_left">
				<label for="category_id">{phrase var='ecommerce.options_for_displaying_buy_it_now_button'}:</label>
			</div>

			<div class="form-inline">
				<label for="one_time_only">
					<input type="radio" id="one_time_only" name="val[type_display]" value="0" class="checkbox" style="vertical-align:middle;" {if (isset($aForms.type_display) && $aForms.type_display == 0)} checked="checked" {/if} /> 
					{phrase var='ecommerce.when_anyone_bid_buy_it_now_option_disappears_and_bidding_continues_until_the_listing_ends'}
				</label>

				<label for="reaching_limit">
					<input type="radio" id="reaching_limit" name="val[type_display]" value="1" class="checkbox" style="vertical-align:middle;" {if (isset($aForms.type_display) && $aForms.type_display == 1)} checked="checked" {/if} /> 
					{phrase var='ecommerce.buy_it_now_option_is_available_until_bids_reach'} 
				</label>
				&nbsp;
				<br>
				<input type="text" name="val[percent_reaching_limit]" value="{value type='input' id='percent_reaching_limit' default=100}" class="form-control reaching_limit_input" />&nbsp;% {phrase var='ecommerce.of_the_buy_it_now_price'}
				
				<br>
				<label for="always">
					<input type="radio" id="always" name="val[type_display]" value="2" class="checkbox" style="vertical-align:middle;" {if isset($aForms.type_display) } {if $aForms.type_display == 2} checked="checked" {/if} {else} checked="checked" {/if} /> 
					{phrase var='ecommerce.always'}
				</label>
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="form-inline">{phrase var='ecommerce.offers'} ({phrase var='ecommerce.offer_price'} >= {phrase var='ecommerce.reserve_price'} * &nbsp;<input type="text" name="val[limit_percent_offer_price]" value="{value type='input' id='limit_percent_offer_price' default=100}" class="form-control limit_percent_offer_price" />&nbsp; % ) </div>
	</div>
	<div class="bid_increment">
		<div class="title">
			<h3>{phrase var='ecommerce.bid_increment'}</h3>
		</div>
		<div>{required} {phrase var='ecommerce.if_seller_doesnt_define_will_use_default_values_from_admins_settings'}</div>
		<div>{phrase var='bid_increment_desc'}</div><br/>
		<div class="table form-group">
			<div class="table_left">
				<label for="category_id">{phrase var='ecommerce.category'}:</label>
			</div>
			<div class="table_right">
				{$sCategories}
			</div>
			<div class="clear"></div>
		</div>
		<div class="bidincrement_content" style="display: none;">
			
		</div>
		<div class="bidincrement_content_loading" style="display: none;">
			{img theme="ajax/small.gif"}
		</div>
	</div>
	
	<div class="transfer">
		<div class="title">
			<h3>{phrase var='ecommerce.transfer_winning_bidder'}</h3>
		</div>
		<div class="table form-inline">
			<div class="table_left">
				<label for="time_complete_transaction">{phrase var='ecommerce.time_for_winner_complete_transaction'}:</label>
			</div>
			<div class="table_right">
				<input type="text" name="val[time_complete_transaction]" value="{value type='input' id='time_complete_transaction' default=7}" id="time_complete_transaction" class="form-control time_complete_tansaction" />
				{phrase var='day_s'}
			</div>
			<div class="clear"></div>
		</div>
		<div class="table form-inline">
			<div class="table_left">
				<label for="number_of_transfers">{phrase var='ecommerce.number_of_transfers_for_each_auction'}:</label>
			</div>
			<div class="table_right">
				<input type="text" name="val[number_of_transfers]" value="{value type='input' id='number_of_transfers' default=2}" id="number_of_transfers" class="form-control number_of_transfers" />
				{phrase var='time_s'}
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="table_clear">
		<button class="bidincrement_submit btn btn-sn btn-primary" type="submit" onclick="submitFormBidIncrement();">{phrase var='ecommerce.submit'}</button>
		<div class="bidincrement_loading" style="display: none;">
			{img theme="ajax/small.gif"}
		</div>
	</div>
</form>

<div class="bidincrement_table_template" style="display: none;">
	<div class="bidincrement_items">
		<div class="bidincrement_row">
            <div class="bidincrement_from"><input class="field_from" type="text" name="val[from][]" value="0.00" readonly /></div>
			<div class="bidincrement_to"><input class="field_to" type="text" name="val[to][]" value="" /></div>
			<div class="bidincrement_increment"><input class="field_increment" type="text" name="val[increment][]" value="" /></div>
			<div class="bidincrement_delete"><a href="javascript:;" onclick="removeBidIncrement(this);" >{img theme="misc/delete.png"}</a></div>
		</div>
		<div class="clear"></div>
	</div>
</div>

{literal}
<script>
$Behavior.onAULoadCategory = function(){ 
   $(".js_mp_category_list").attr("id","category_id");
   $(".js_mp_category_list").attr("onchange","changeCategoryBidIncrement();");
}
function addNewBidIncrement()
{
    var bCanAddMore = true;
    $('.ynaucton_table_body').find('.field_to').each(function(i){
        if ($(this).val().trim() == '')
        {
            bCanAddMore = false;
            $('.bidincrement_form_msg').html('<div class="error_message">' + oTranslations['ecommerce.please_field_new_row_before_add_more'] + '</div>').show();
        }
    });
    $('.ynaucton_table_body').find('.field_increment').each(function(i){
        if ($(this).val().trim() == '')
        {
            bCanAddMore = false;
            $('.bidincrement_form_msg').html('<div class="error_message">' + oTranslations['ecommerce.please_field_new_row_before_add_more'] + '</div>').show();
        }
    });
    
    var oLastTo = $('.ynaucton_table_body').find('.field_to').last();
    var oLastFrom = $('.ynaucton_table_body').find('.field_from').last();
    if (oLastTo.length != 0 && oLastFrom.length != 0 && parseFloat(oLastTo.val()) < parseFloat(oLastFrom.val()))
    {
        bCanAddMore = false;
        $('.bidincrement_form_msg').html('<div class="error_message">' + oTranslations['ecommerce.to_field_must_be_greater_than_from_field_in_each_rows'] + '</div>').show();
    }
    
    if (bCanAddMore)
    {
        $('.ynaucton_table_body').append($('.bidincrement_table_template').html());
        
        if (oLastTo.length != 0)
        {
            $('.ynaucton_table_body').find('.field_from').last().val(parseFloat(oLastTo.val()) + 0.01);
        }
        
        $('.bidincrement_form_msg').html('').hide();
        $('.ynaucton_table_body').find('.field_to').prop("readonly", true);
        $('.ynaucton_table_body').find('.field_to').last().prop("readonly", false);
    }
}

function removeBidIncrement(e)
{
    var oDeleteRow = $(e).parent().parent().parent();
    
    var oPreviousTo = oDeleteRow.prev().find('.field_to').first();
    var oNextFrom = oDeleteRow.next().find('.field_from').first();
    
    if (oPreviousTo.length == 0 && oNextFrom.length == 0)
    {
        
    }
    else if (oPreviousTo.length != 0 && oNextFrom.length == 0)
    {
        
    }
    else if (oPreviousTo.length == 0 && oNextFrom.length != 0)
    {
        oNextFrom.val(0.00);
    }
    else if (oPreviousTo.length != 0 && oNextFrom.length != 0)
    {
        oNextFrom.val(parseFloat(oPreviousTo.val()) + 0.01);
    }
    
	$(e).parent().parent().parent().remove();
    
    $('.ynaucton_table_body').find('.field_to').prop("readonly", true);
    $('.ynaucton_table_body').find('.field_to').last().prop("readonly", false);
}

function submitFormBidIncrement()
{
	$('.bidincrement_submit').hide();
	$('.bidincrement_loading').show();
	$('#bidincrement_form').ajaxCall('auction.updateBidIncrement');
}

function changeCategoryBidIncrement()
{
	var iCategoryId = $('#category_id').val();
	if (iCategoryId)
	{
		$('.bidincrement_content_loading').show();
		$('.bidincrement_content').hide();
		$.ajaxCall('auction.changeCategoryBidIncrement', 'categoryId=' + iCategoryId);
	}
	else
	{
		$('.bidincrement_content_loading').hide();
		$('.bidincrement_content').html('').hide();
	}
	$('.bidincrement_form_msg').html('');
}
</script>

<style>
	.ynaucton_table_header div{font-weight: bold;}
	.bidincrement_row{margin: 10px;}
	.bidincrement_add_new{margin: 10px;}
	.bidincrement_row div{float: left;}
	.bidincrement_from{width: 22%; text-align: center;}
	.bidincrement_to{width: 22%; text-align: center;}
	.bidincrement_increment{width: 22%; text-align: center;}
	.bidincrement_content_loading{text-align: center; margin: 20px;}
</style>
{/literal}