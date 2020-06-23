<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: ajax.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
defined('PHPFOX') or exit('NO DICE!');

?>

{literal}
<style>
	.yndonate_table{
		width: 100%;
		box-sizing: border-box;
	}
	.yndonate_table .extra_info{
		padding: 0px;
		margin: 10px 0;
	}
	.table.yndonate_table .table_right{
		padding: 0px;
		border: none;
	}
	.yndonate_table #purpose{
		margin: 10px 0;
	}

	#js_friend_search_content .label_flow .row2{
		padding: 5px;
		margin: 0px;
		clear: both;
		overflow: hidden;
	}
	#js_friend_search_content .label_flow{
		padding: 0px;
	}
	#js_friend_search_content .label_flow .yndonate_img{
		font-size: 0;
		float: left;
		margin-right: 10px;
	}
	#js_friend_search_content .label_flow .yndonate_img .img-wrapper,#js_friend_search_content .label_flow .yndonate_img .no_image_user{
		width: 48px;
		height: 48px;
	}
	#js_friend_search_content .label_flow .yndonate_img .img-wrapper img{
		width: 100%;
		height: 100%;
	}
	#js_friend_search_content .label_flow button{
		float: right;
		position: relative;
		text-transform: uppercase;
		top: -10px;
	}
	#js_friend_search_content .label_flow .js_donation{
		font-weight: bold;
		display: block;
	}
	#js_friend_search_content .label_flow .yndonate_price{
	    position: absolute;
	    left: 215px;
	    top: 20px;
	    text-transform: uppercase;
	    font-weight: bold;
	    font-size: 13px;
	}
	#donorlist_info{
		padding: 0 10px;
		box-sizing: border-box;
	}
	input[type="checkbox"]:focus,
	input[type="radio"]:focus{
		width: auto;
	}
	#donation_popup2 h3{
		margin-bottom: 10px;
	}
	#purpose_2{
	    overflow: auto;
	    padding-left:20px;
	    max-height: 150px;
	    margin-top: 10px;
	    margin-bottom: 10px;
	}
	span[id^=js_user_name_link_profile]{
		max-width: 130px;
		overflow: hidden;
		display: inline-block;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
</style>
{/literal}


<div id="alertBox"></div>

<div id="dMessage" class="message" style="display:none;"></div>

<div class="table form-group yndonate_table">
	<div id="donation_popup1">

		<div style="display: flex" >
			<div class="mr-1" style="padding-top: 8px;">
			{phrase var='donation.donate'} 
			</div>

			<div style="display: flex;flex-flow: wrap;flex:1;">
			<input type="text" class="form-control" name="quanlity" id="quanlity" value="0" style="width:100px;display:inline;">
            &nbsp;
			<select id="yn_donation_select_currency" class="form-control" name='yn_donation_select_currency' style="min-width: 80px;max-width: 100px;display:inline;">
				{foreach from=$aCurrentCurrencies key=key item=sCurrency}
				<option value="{$sCurrency}">
					{$sCurrency}
				</option>
				{/foreach}
			</select>

			<span style="padding-left: 20px;" id="btnBlock">
				<a href="#" onclick="checkQuanlity(); return false;"><img src="{param var='core.url_module'}donation/static/image/donate_button_small.gif" style="vertical-align: middle;" /></a>
			</span>
			</div>
		</div>

	<div>
		<input type="hidden" value="{$iPageId}" name="iPageId" id="iPageId" />
		<input type="hidden" value="{$iUserId}" name="iUserId" id="iUserId" />
		<input type="hidden" value="{$sUrl}" name="sUrl" id="sUrl" />
		<input type="hidden" value="http://localhost/phpfox301donation/pages/3/payment_done" name="return">
		<input type="hidden" value="{phrase var='donation.please_input_number_delimiter_by'}" id="error" />
		<input type="hidden" value="{phrase var='donation.must_agree_to_the_terms_and_conditions_to_continue'}" id="error_confirm" />
		<input type="hidden" value="{phrase var='donation.must_fill_your_name'}" id="error_guest_name" />
	</div>

	<div class="extra_info">
		{phrase var='donation.notice_multi_currency_conversion'}
	</div>

		<h3>{phrase var='donation.purpose_of_donation'}</h3>
		{if $sContent}
			<div id="purpose_2">
				<p>{$sContent|parse}</p>
			</div>
		{else}
		</br>
		{/if}

	<div>
		<h3>{phrase var='donation.donation_lists'}</h3>

		{module name='donation.donorlist' friend_share=true input='to'}
	</div>
</div>

<div id="donation_popup2" style="display:none;">
	<div class="table_right">
		{if Phpfox::isUser()}
		<h3>{phrase var='donation.donation_privacy'}
			<a href="#" id = 'donation_privacy_label'>
				<i class="fa fa-caret-down fa-lg"></i>
			</a>
		</h3>
		
		<div id="donation_privacy" >
			<input type="hidden" id="bIsGuest" name="bIsGuest"  value="0">
			<div style="clear: both; display: flex;">
				<span style="float:left;"><input style="margin-top: 1px;" type="checkbox" name="do_not_show_name" id="do_not_show_name" /></span>
				<span style="float:left;margin-top:-1px;margin-left:4px;line-height: 18px">{phrase var='donation.do_not_show_name_on_donor_list'}</span>
			</div>
			
			<div style="clear: both;display: flex;">
				<span style="float:left;"><input style="margin-top: 1px;" type="checkbox" name="do_not_show_money" id="do_not_show_money" /></span>
				<span style="float:left;margin-top:-1px;margin-left:4px;line-height: 18px">{phrase var='donation.do_not_show_donation_amount_on_donor_list'}</span>
			</div>
			
			<div style="clear: both;display: flex;">
				<span style="float:left;"><input style="margin-top: 1px;" type="checkbox" name="do_not_show_feed" id="do_not_show_feed" /></span>
				<span style="float:left;margin-top:-1px;margin-left:4px;line-height: 18px">{phrase var='donation.do_not_show_feed'}</span>
			</div>
			<div style="clear: both;"></div>
		</div>
		{else}
		<p  style="font-weight:bold;padding-left:5px; line-height: 2em; font-size:12px; background-color: #ccc">{phrase var='donation.donation_guest_information'}</p>
		<div id="donation_privacy_guest" >
			<div class="form-group">
				<div class="table_left mb-1">
					<span style="margin-left:4px;">{phrase var='donation.your_name'} *</span>
				</div>
				
				<div class='table_right'>
					<span><input type="text" class="form-control" name="guest_name" id="guest_name" /></span>
					<!--
					<span style="margin-left:4px;" name='guest_name_hint' id='guest_name_hint'> {phrase var='donation.guest_name_hint'} </span>
					-->
				</div>
			</div>
			
			<input type="hidden" id="bIsGuest" name="bIsGuest" value="1">
			
			<div style="clear: both;display: flex;">
				<span style="float:left;"><input style="margin-top:1px;" type="checkbox" name="do_not_show_name" id="do_not_show_name" /></span>
				<span style="float:left;margin-left:4px;line-height: 18px">{phrase var='donation.do_not_show_name_on_donor_list'}</span>
			</div>
			
			<div style="clear: both;display: flex;">
				<span style="float:left;"><input style ="margin-top: 1px" type="checkbox" name="do_not_show_money" id="do_not_show_money" /></span>
				<span style="float:left;margin-left:4px;line-height: 18px;">{phrase var='donation.do_not_show_donation_amount_on_donor_list'}</span>
			</div>
			<div style="clear: both;"></div>
			
		</div>
		
		{/if}
		
	</div>
	
	</br>
	
	
	
	<div class="table_right">
		<h3>{phrase var='donation.terms_and_conditions'}</h3>
		{if $sTermOfService}
		<div id="purpose_2" style="color:black">
			<p>{$sTermOfService|parse}</p>
		</div>
		{else}
		</br>
		{/if}
		<div style="clear: both;display: flex;">
			<span style="float:left;">
				<input type="checkbox" name="agree" id="agree" style="margin-top: 1px;"/>
			</span>
			<span style="float:left;margin-top: -1px;margin-left:4px;line-height: 18px;">{phrase var='donation.read_and_agree_to_the_terms_and_conditions'}</span>
		</div>
		<div style="clear: both;"></div>
	</div>
	<div class="table_right mt-1" style="clear: both;">
		<button id="js_confirm_donation" type="button" class="button btn btn-primary btn-sm" onclick="$(this).addClass('disabled').attr('disabled','disabled');checkConfirm();return false;">{phrase var='donation.confirm'}</button>
	</div>
	
</div>
</div>

{literal}
	<script type="text/javascript">
		
	    function checkQuanlity(){
	        var quanlity = $('#quanlity').val();     
	        var iPageId = $('#iPageId').val();
	        var iUserId = $('#iUserId').val();
	        var sUrl = $('#sUrl').val();
			var isEnable = $('input[name=agree]').is(':checked');

	        if(((quanlity - 0) == quanlity && quanlity.length > 0 && parseFloat(quanlity)>0))
			{
			{/literal}

			{literal}
				$('#donation_popup1').css('display','none');
				$('#donation_popup2').css('display','');
			{/literal}
			{literal}
	        }
			else
			{
	            var ele = $('#alertBox').find('div');
	            if (ele.html()==null){               
	                var sError = $('#error').val();
	                $('#alertBox').append("<div class='error_message'>"+sError+"</div>");
	                $('#quanlity').select().focus();
	                setTimeout(function(){
	                    $('#alertBox').find('div').slideUp(200, function(){
	                        $('#alertBox').find('div').remove();
	                    });
	                }, 2000);
	            }
	        }
	    }

        $Behavior.initDropDownBox= function() {
            $("#guest_name_hint").hide();
            $("#donation_privacy").hide();
            $("#donation_privacy_label").click(function () {
                if ($("#donation_privacy").is(':hidden')) {
                    $("#donation_privacy").show();
                }
                else {
                    $("#donation_privacy").hide();
                }

                return false;
            });
        }
		/*
		$("input[name=guest_name]").focus( function() {
	            
			$("#guest_name_hint").show(200);
		});
		
		$("input[name=guest_name]").blur( function() {
			$("#guest_name_hint").hide(200);
		});
		*/
	    $("input[name='agree']").change( function() {
	  		var isEnable = $('input[name=agree]').is(':checked');
	  		if(isEnable == true)
	  		{
	  			
	  		}
		});
		function checkConfirm()
		{
			 var quanlity = $('#quanlity').val();     
	        var iPageId = $('#iPageId').val();
	        var iUserId = $('#iUserId').val();
	        var sUrl = $('#sUrl').val();
			var isEnable = $('input[name=agree]').is(':checked');
			
			
			if(isEnable)
			{
				if($('input[name=bIsGuest]').val() == 1)
				{
					//var guestnameRegex= /^([A-Za-z0-9\s]){6,50}$/;
					var guestnameRegex= /^(.){1,100}$/;
					if(!guestnameRegex.test($("input[name=guest_name]").val())) {
						var ele = $('#alertBox').find('div');
						if (ele.html()==null){               
							var sError = $('#error_guest_name').val();
							$('#alertBox').append("<div class='error_message'>"+sError+"</div>");
							$('#quanlity').select().focus();
							setTimeout(function(){
								$('#alertBox').find('div').slideUp(200, function(){
									$('#alertBox').find('div').remove();
								});
							}, 2000);
						}
						$('#js_confirm_donation').removeClass('disabled').removeAttr('disabled');
						return false
					}
				}

				var bNotShowMoney =  $('input[name=do_not_show_money]').is(':checked') ? 1 : 0;
				var bNotShowName =  $('input[name=do_not_show_name]').is(':checked') ? 1  : 0;
				var bNotShowFeed =  $('input[name=do_not_show_feed]').is(':checked') ? 1 : 0;
				var sCurrency = $('#yn_donation_select_currency').val();
				var sGuestName = ($('input[name=guest_name]').val()) ? $('input[name=guest_name]').val() : "" ;

				$.ajaxCall('donation.addToDonationLists','iPageId='+iPageId+'&iUserId='+iUserId+'&quanlity='+quanlity
						+ '&sUrl='+sUrl+'&bNotShowMoney=' + bNotShowMoney +'&bNotShowName=' 
						+ bNotShowName +'&bNotShowFeed=' + bNotShowFeed +'&sGuestName=' + sGuestName + 
						'&sCurrency=' + sCurrency);
	            $('#btnBlock').html('');
			}
			else
			{
				var ele = $('#alertBox').find('div');
	            if (ele.html()==null){               
	                var sError = $('#error_confirm').val();
	                $('#alertBox').append("<div class='error_message'>"+sError+"</div>");
	                $('#quanlity').select().focus();
	                setTimeout(function(){
	                    $('#alertBox').find('div').slideUp(200, function(){
	                        $('#alertBox').find('div').remove();
	                    });
	                }, 2000);
	            }
				$('#js_confirm_donation').removeClass('disabled').removeAttr('disabled');
			}
		}
	</script>

	<style>
		#purpose:first-letter{ 
			font-size:110%;
			padding-left: 7px;
			text-transform: uppercase;
		}

		.js_donation:FIRST-LETTER{
			text-transform: uppercase;
		}
		#btnBlock img{
			max-height: 40px;
		}
		@media screen and (max-width: 480px){
			#btnBlock{
				display: block;
				padding-left: 0 !important;
				margin-top: 10px;
				width: 100%;
			}
			#btnBlock img{
				width: 100px;
			}
			#yn_donation_select_currency{
				width: 40px !important;
			}

			.yndonate_price{
				display: block;
				clear: both;
				position: static !important;
				margin: 20px 0;
				margin-left: 0 !important;
			}

			.label_flow button{
				float: none !important;
				display: block;
				clear: both;
			}
		}

	</style>
{/literal}