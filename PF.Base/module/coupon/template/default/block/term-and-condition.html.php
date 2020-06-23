<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
 ?>

{literal}
<style type="text/css">
	.table1{
		line-height: 20px;
		margin-bottom: 4px;
	}
	.table_left1{
		float: left;
		text-align: center;
	}
	.table_right1{
		margin-left: 100px;
	}
</style>
<script type="text/javascript">
	function updateGetCodeButton()
	{
		checked = $('#coupon_term_condition').is(":checked");
		if(checked)
		{
			$('#btnGetCode').show();
		}
		else
		{
			$('#btnGetCode').hide();
		}
	}
</script>
{/literal}

<form id="core_js_coupon" method="post">
	<input type="hidden" name='coupon_id' value="{$iCouponId}"/>
	<div  style="width:570px; color:#333333;max-height:300px; margin-bottom: 5px;" class="item_view_content">
	  <p id="purpose" style="overflow:auto; max-height:300px;padding-left:5px; line-height: 2em; font-size:12px;">
	  	{$sTermCondition|parse}
	  </p>
	</div>
	<div>
		<input id="coupon_term_condition" type="checkbox" style="margin-right: 5px;" onclick="updateGetCodeButton();"/>{phrase var="i_have_read_and_agreed_with_the_terms_and_conditions"}
	</div>
	<div style="margin-top:5px;">
		<button type="button" class="btn btn-primary btn-sm" id="btnGetCode" style="display:none;">{phrase var='get_code'}</button>
	</div>
</form>
{literal}
<script type="text/javascript">
	$Behavior.onLoadResumePopUp = function()
	{
		$('#btnGetCode').click(function(){
			checked = $('#coupon_term_condition').is(":checked");
			if(!checked)
			{
				alert(oTranslations['coupon.you_must_agree_with_the_terms_and_conditions_before_getting_code']);
				return false;
			}
			else
			{
				$(this).addClass('disabled').attr('disabled','disabled');
				$("#core_js_coupon").ajaxCall('coupon.getCode');
			}
		});
	};
	$Core.loadInit();
</script>
{/literal}
