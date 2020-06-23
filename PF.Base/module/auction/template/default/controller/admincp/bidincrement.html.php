<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="bidincrement_form_msg" style="display: none;">
</div>
<form id="bidincrement_form" method="post" action="#" onkeypress="return event.keyCode != 13;" onsubmit="return false;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='bidincrement_increment'}
            </div>
        </div>
        <div class="panel-body">
            <label for="">{required} {phrase var='category'}</label>
            <select class="form-control" name="val[category_id]" id="category_id" onchange="changeCategoryBidIncrement();">
                <option value="">{phrase var='select'}:</option>
                {foreach from=$aCategories item=aCategory}
                	{php}
							$aCategory = $this->_aVars['aCategory'];
					{/php}
                    <option value="{$aCategory.category_id}" {value type='select' id='category_id' default=$aCategory.category_id}><td><?php echo Phpfox::isPhrase($aCategory['title']) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title']);?></td></option>
                {/foreach}
            </select>
        </div>
        <div class="bidincrement_content" style="display: none;">

        </div>
        <div class="bidincrement_content_loading" style="display: none;">
            {img theme="ajax/small.gif"}
        </div>
    </div>
</form>

<div class="bidincrement_table_template" style="display: none;">
	<div class="bidincrement_items">
		<div class="bidincrement_row">
            <div class="bidincrement_from"><input class="field_from form-control" type="text" name="val[from][]" value="0.00" readonly /></div>
			<div class="bidincrement_to"><input class="field_to form-control" type="text" name="val[to][]" value="" /></div>
			<div class="bidincrement_increment"><input class="field_increment form-control" type="text" name="val[increment][]" value="" /></div>
			<div class="bidincrement_delete"><a href="javascript:;" onclick="removeBidIncrement(this);" ><img src="{$corepath}module/auction/static/image/delete.png" class="v_middle"/></a></div>
		</div>
		<div class="clear"></div>
	</div>
</div>

{literal}
<script>
function addNewBidIncrement()
{
    var bCanAddMore = true;
    $('.ynaucton_table_body').find('.field_to').each(function(i){
        if ($(this).val().trim() == '')
        {
            bCanAddMore = false;
            $('.bidincrement_form_msg').html('<div class="error_message">' + oTranslations['auction.please_field_new_row_before_add_more'] + '</div>').show();
        }
    });
    $('.ynaucton_table_body').find('.field_increment').each(function(i){
        if ($(this).val().trim() == '')
        {
            bCanAddMore = false;
            $('.bidincrement_form_msg').html('<div class="error_message">' + oTranslations['auction.please_field_new_row_before_add_more'] + '</div>').show();
        }
    });
    
    var oLastTo = $('.ynaucton_table_body').find('.field_to').last();
    var oLastFrom = $('.ynaucton_table_body').find('.field_from').last();
    if (oLastTo.length != 0 && oLastFrom.length != 0 && parseFloat(oLastTo.val()) < parseFloat(oLastFrom.val()))
    {
        bCanAddMore = false;
        $('.bidincrement_form_msg').html('<div class="error_message">' + oTranslations['auction.to_field_must_be_greater_than_from_field_in_each_rows'] + '</div>').show();
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
	$('#bidincrement_form').ajaxCall('auction.updateBidIncrement', 'is_admincp=1');
}

function changeCategoryBidIncrement()
{
	var iCategoryId = $('#category_id').val();
	if (iCategoryId)
	{
		$('.bidincrement_content_loading').show();
		$('.bidincrement_content').hide();
		$.ajaxCall('auction.changeCategoryBidIncrement', 'categoryId=' + iCategoryId + '&is_admincp=1');
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
	.bidincrement_row div{float: left; margin-right: 10px}
    .bidincrement_row div input{padding-left: 5px;}
	.bidincrement_from{width: 22%; text-align: center;}
	.bidincrement_to{width: 22%; text-align: center;}
	.bidincrement_increment{width: 22%; text-align: center;}
	.bidincrement_content_loading{text-align: center; margin: 20px;}
</style>
{/literal}

