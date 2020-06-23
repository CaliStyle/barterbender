<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="main_break">
	{$sCreateJs}
	<form method="post" class="ynfr_add_edit_form" action="{url link='current'}" id="ynfr_edit_campaign_form"  enctype="multipart/form-data">
		<div id="js_custom_privacy_input_holder">
		{if $bIsEdit && empty($sModule)}
			{module name='privacy.build' privacy_item_id=$aForms.campaign_id privacy_module_id='fundraising'}
		{/if}
		</div>
		<input type="hidden" name="val[selected_categories]" id="js_selected_categories" value="{value type='input' id='selected_categories'}" />
		<input type="hidden" name="val[is_approved]" value="{value type='input' id='is_approved'}" />

		{if !empty($sModule)}
			<input type="hidden" name="module" value="{$sModule|htmlspecialchars}" />
		{/if}
		{if !empty($iItem)}
			<input type="hidden" name="item" value="{$iItem|htmlspecialchars}" />
		{/if}
		{if $bIsEdit}
			<input type="hidden" name="id" value="{$aForms.campaign_id}" />
		{/if}
		{plugin call='fundraising.template_controller_add_hidden_form'}
		{module name='fundraising.campaign.form-main-info'}
		{if $bIsEdit}
		</form>
			{module name='fundraising.campaign.form-gallery' iCampaignId=$aForms.campaign_id}
			{module name='fundraising.campaign.form-contact-information' iCampaignId=$aForms.campaign_id}
			{module name='fundraising.campaign.form-email-conditions' iCampaignId=$aForms.campaign_id}
			{module name='fundraising.campaign.form-invite-friend' iCampaignId=$aForms.campaign_id}
		{else}
	</form>
		{/if}
</div>

{if PHPFOX_IS_AJAX_PAGE}
{literal}
<script type="text/javascript">
    $Core.loadInit(true);
</script>
{/literal}
{/if}

<!--P_Check-->
{if $bIsEdit && $sTab != ''}
{literal}
<script type="text/javascript">
	var bIsFirstRun = false;
    $Behavior.pageSectionMenuRequest = function() {
        if (!bIsFirstRun) {
            $Core.pageSectionMenuShow('#js_fundraising_block_{/literal}{$sTab}{literal}');
            if ($('#page_section_menu_form').length > 0) {
                $('#page_section_menu_form').val('js_fundraising_block_detail');
            }
            bIsFirstRun = true;
        }
    }

    function ClickAll(all) {
        if(all.val() == oTranslations['fundraising.select_all'])
            all.val(oTranslations['fundraising.un_select_all']);
        else
            all.val(oTranslations['fundraising.select_all']);

		$(".label_flow .checkbox").click();
        $(".friend_search_holder").click();
    }
</script>
{/literal}
{/if}
{literal}
<script type="text/javascript">
(function()
{
	$Behavior.fundraisingProgressBarSettings = function()
	{
		if ($Core.exists('#js_fundraising_block_gallery_holder'))
		{
			oProgressBar = {
				holder: '#js_fundraising_block_gallery_holder',
				progress_id: '#js_progress_bar',
				uploader: '#js_progress_uploader',
				add_more: false,
				max_upload: {/literal}{$iMaxUpload}{literal},
				total: 1,
				frame_id: 'js_upload_frame',
				file_id: 'image[]'
			};
			$Core.progressBarInit();
		}
	}
	$Behavior.setMinPredefined = function()
	{
		iMaxPredefined = {/literal}{$iMaxPredefined}{literal};
		iMinPredefined = {/literal}{$iMinPredefined}{literal};
	}

    $Behavior.initFundraisingFormValidation = function()
    {
    	function checkCondition()
    	{
			if(/undefined/i.test(typeof jQuery.validator))
			{
				window.setTimeout(checkCondition, 400);
			}
			else
			{
				initializeValidator();
			}
		}
		window.setTimeout(checkCondition, 400);

		function initializeValidator()
		{
			$('.ynfr_add_edit_form').each(function(index)
			{
				ynfundraising.initializeValidator($(this));
			});
		}
	};

	$Behavior.initFundraisingForm = (function(){
		$('#fundraising_goal').keydown(function (e) {
                  if (e.altKey || e.ctrlKey) {
			    e.preventDefault();
			}
			else if (e.shiftKey && !(e.keyCode >= 35 && e.keyCode <= 40)){
				  e.preventDefault();
			} else {
			    var n = e.keyCode;
			    if (!((n == 8)
			    || (n == 46)
			    || (n >= 35 && n <= 40)
			    || (n >= 48 && n <= 57)
			    || (n >= 96 && n <= 105))
			    ) {
				  e.preventDefault();
			    }
			}
		});
	});
    $Behavior.setDescEditor = (function(){
		Editor.setId("description");

        $("a[rel='js_fundraising_block_main']").bind("click", function(){
			Editor.setId("description");
        });

        $("a[rel='js_fundraising_block_contact_information']").bind("click", function(){
	       	 Editor.setId("contact_about_me");
	         Editor.getEditors();
        });

        $("a[rel='js_fundraising_block_email_conditions']").bind("click", function(){
            Editor.setId("email_message");
        });

        {/literal}{if isset($sTab)}{literal}
			  if('{/literal}{$sTab}{literal}' == 'contact_information')
			  {
			  		Editor.setId("contact_about_me");
					Editor.getEditors();
			  }
			  else
			  if('{/literal}{$sTab}{literal}' == 'email_conditions')
			  {
					Editor.setId("email_message");
			  }
			  else
			  {
			  		Editor.setId("description");
			  		Editor.getEditors();
			  }
		  {/literal}{/if}{literal}
     });
})();
</script>
<style type="text/css">
	div.row_focus {
		background: none repeat scroll 0 0 #FEFBD9;
	 }
</style>
{/literal}
<script type="text/javascript">
var loadMap = false;
{literal}
var googleApiKey={/literal}"{$googleApiKey}"{literal};
 $Behavior.ynfrInitializeGoogleMapLocation = function() {
    if (loadMap === false) {
        loadMap = true;
        if ($('#ynfr_edit_campaign_form').length === 0) { return false }
        $('#js_country_child_id_value').change(function(){
            debug("Cleaning  city, postal_code and address");
            $('#city').val('');
            $('#postal_code').val('');
            $('#address').val('');
        });
        $('#country_iso, #js_country_child_id_value').change(ynfundraising_map.inputToMap);
        $('#location_venue, #address, #postal_code, #city').blur(ynfundraising_map.inputToMap);
        ynfundraising_map.loadScript();
    }
  };

{/literal}
</script>