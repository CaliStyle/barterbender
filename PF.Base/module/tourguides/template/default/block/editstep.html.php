<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<style type="text/css">
.admin_yntour_edit_step
{
    padding:0px;
}
.admin_yntour_edit_step .description
{
   margin-bottom:4px; 
   font-weight: bold;
}
.extra_info {
    font-size: 11px;
    margin: 5px;
    font-weight: bold;
}
.extra_info {
    clear: both;
    color: black;
}
.extra_info, .extra_info_link {
    padding: 4px 0;
    overflow: hidden;
}
.yntour_bgcolor {
    float: left;
    height: 12px;
    margin-left: 4px;
    width: 12px;
}
.yntour_span_color {
    float: left;
    width: 115px;
    line-height: 14px;
}
.yntour_color_selected,div.yntour_bgcolor:hover
{
     border: 1px inset solid #4169E1 !important; 
      cursor: pointer !important;
    -webkit-box-shadow: 0 0 2px 2px #4169E1  !important;
    -moz-box-shadow: 0 0 2px 2px #4169E1  !important;
    box-shadow: 0 0 2px 2px #4169E1  !important;
}
.js_tour_lang_area,
.js_tour_lang_area:focus{
    width: 98%;
    border: none;
}

input#yntour_delay{
    overflow: hidden;
    box-sizing: border-box;
    padding: 10px;
    border: none;
}

</style>
<script type="text/javascript">
    $Behavior.onLoadEditTourForm = function() {
        $('input#yn_chk_multi_lang_yes').off('click').on('click', function () {
            $('#yntour_lang_select_value').show();
            $('#yn_current_lang').hide();
            _onchangeYnTourLang($('#yntour_lang_select_value'));
        });

        $('input#yn_chk_multi_lang_no').off('click').on('click', function () {
            $('#yntour_lang_select_value').hide();
            $('#yn_current_lang').show();
            _onchangeYnTourLang($('#js_current_lang'));
        });
        $('.admin_yntour_edit_step').closest('.js_box_content').css('background-color', '#e5e5e5');
    }
</script>
{/literal}
<div class="admin_yntour_edit_step">
<form action="{url link='admincp.tourguides.add'}" id="yntour_edit_form" name="yntour_edit_form" method="post" onsubmit="return false;">
<div class="description">{_p var='description'}: </div>
    <input type="hidden" value="{$sCurrentLangId}" id="js_current_lang">
<div style="position: relative; height: 160px; overflow:hidden" class="form-group">
    {foreach from=$aLanguages key=iKey item=aLanguage}    
        {if $iKey == $sCurrentLangId}
            <textarea  id="js_tour_lang_area_{$iKey}" class="js_tour_lang_area active form-control" name="tour_description[{$iKey}]" rows="8" cols="49"><?php echo $this->_aVars["aStep"]["description"][$this->_aVars["iKey"]]; ?></textarea>
        {else}
            <textarea  id="js_tour_lang_area_{$iKey}" class="js_tour_lang_area form-control" style="display: none" name="tour_description[{$iKey}]" rows="8" cols="49"><?php echo $this->_aVars["aStep"]["description"][$this->_aVars["iKey"]]; ?></textarea>
        {/if}
    {/foreach}
</div>
{if count($aLanguages) > 1}
<div class="extra_info">
    <div class="yntour_span_color" style=" margin-top: 4px;">{_p var='multiple_languages'}:</div>
    <div class="tags">
        <input id="yn_chk_multi_lang_no" type="radio" value="{$sCurrentLangId}" name="is_multi_lang" {if !empty($aStep.single_lang)}checked="checked"{/if}/>{phrase var='core.no'}
        &nbsp;&nbsp;<input id="yn_chk_multi_lang_yes" type="radio" value="" name="is_multi_lang" {if empty($aStep.single_lang)}checked="checked"{/if}/>{phrase var='core.yes'}
    </div>
</div>
{/if}
<div class="extra_info">
    <div class="yntour_span_color" style="line-height: 20px;">{_p var='language'}:</div>
     <div class="tags form-inline" >
        {if count($aLanguages) == 1}
            <span id="yn_current_lang" style="line-height: 20px">{$sCurrentLangTitle}</span>
        {else}
            {if !empty($aStep.single_lang)}
            <span id="yn_current_lang" style="line-height: 20px">{$sCurrentLangTitle}</span>
            <select class="form-control" id="yntour_lang_select_value" style="padding:0px; display: none;" onchange="_onchangeYnTourLang(this);">
            {else}
            <span id="yn_current_lang" style="line-height: 20px; display: none">{$sCurrentLangTitle}</span>
            <select class="form-control" id="yntour_lang_select_value" style="padding:0px;" onchange="_onchangeYnTourLang(this);">
            {/if}        
            {foreach from=$aLanguages key=iKey item=aLanguage}
                {if $iKey == $sCurrentLangId}
                <option value="{$iKey}" selected="selected">{$aLanguage}</option>
                {else}
                <option value="{$iKey}">{$aLanguage}</option>
                {/if}
            {/foreach}
            </select>
        {/if}
        </div>
</div>
<div class="extra_info">
    <div class="yntour_span_color">{_p var='background_color'}:</div>
    <div id="yntour_bg_color" class="tags">
        <div>
            <div style="background-color:black" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="black")}yntour_color_selected{/if}" rel="black" onclick="_changeColor(this);"></div>
            <div style="background-color:white" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="white")}yntour_color_selected{/if}" rel="white" onclick="_changeColor(this);"></div>
            <div style="background-color:yellow" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="yellow")}yntour_color_selected{/if}" rel="yellow" onclick="_changeColor(this);"></div>
            <div style="background-color:silver" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="silver")}yntour_color_selected{/if}" rel="silver" onclick="_changeColor(this);"></div>
            <div style="background-color:red" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="red")}yntour_color_selected{/if}" rel="red" onclick="_changeColor(this);"></div>
            <div style="background-color:purple" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="purple")}yntour_color_selected{/if}" rel="purple" onclick="_changeColor(this);"></div>
            <div style="background-color:olive" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="olive")}yntour_color_selected{/if}" rel="olive" onclick="_changeColor(this);"></div>
            <div style="background-color:navy" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="navy")}yntour_color_selected{/if}" rel="navy" onclick="_changeColor(this);"></div>
            <div style="background-color:maroon" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="maroon")}yntour_color_selected{/if}" rel="maroon" onclick="_changeColor(this);"></div>
            <div style="background-color:lime" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="lime")}yntour_color_selected{/if}" rel="lime" onclick="_changeColor(this);"></div>
            <div style="background-color:green" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="green")}yntour_color_selected{/if}" rel="green" onclick="_changeColor(this);"></div>
            <div style="background-color:gray" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="gray")}yntour_color_selected{/if}" rel="gray" onclick="_changeColor(this);"></div>
            <div style="background-color:fuchsia" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="fuchsia")}yntour_color_selected{/if}" rel="fuchsia" onclick="_changeColor(this);"></div>
            <div style="background-color:blue" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="blue")}yntour_color_selected{/if}" rel="blue" onclick="_changeColor(this);"></div>
            <div style="background-color:aqua" class="yntour_bgcolor {if (isset($aStep.bgcolor) && $aStep.bgcolor=="aqua")}yntour_color_selected{/if}" rel="aqua" onclick="_changeColor(this);"></div>
        </div>
    </div>
</div>
<div class="extra_info">
    <div class="yntour_span_color">{_p var='font_color'}:</div>
    <div id="yntour_font_color" class="tags">
        <div>
           <div style="background-color:black" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="black")}yntour_color_selected{/if}" rel="black" onclick="_changeColor(this);"></div>
            <div style="background-color:white" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="white")}yntour_color_selected{/if}" rel="white" onclick="_changeColor(this);"></div>
            <div style="background-color:yellow" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="yellow")}yntour_color_selected{/if}" rel="yellow" onclick="_changeColor(this);"></div>
            <div style="background-color:silver" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="silver")}yntour_color_selected{/if}" rel="silver" onclick="_changeColor(this);"></div>
            <div style="background-color:red" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="red")}yntour_color_selected{/if}" rel="red" onclick="_changeColor(this);"></div>
            <div style="background-color:purple" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="purple")}yntour_color_selected{/if}" rel="purple" onclick="_changeColor(this);"></div>
            <div style="background-color:olive" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="olive")}yntour_color_selected{/if}" rel="olive" onclick="_changeColor(this);"></div>
            <div style="background-color:navy" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="navy")}yntour_color_selected{/if}" rel="navy" onclick="_changeColor(this);"></div>
            <div style="background-color:maroon" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="maroon")}yntour_color_selected{/if}" rel="maroon" onclick="_changeColor(this);"></div>
            <div style="background-color:lime" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="lime")}yntour_color_selected{/if}" rel="lime" onclick="_changeColor(this);"></div>
            <div style="background-color:green" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="green")}yntour_color_selected{/if}" rel="green" onclick="_changeColor(this);"></div>
            <div style="background-color:gray" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="gray")}yntour_color_selected{/if}" rel="gray" onclick="_changeColor(this);"></div>
            <div style="background-color:fuchsia" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="fuchsia")}yntour_color_selected{/if}" rel="fuchsia" onclick="_changeColor(this);"></div>
            <div style="background-color:blue" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="blue")}yntour_color_selected{/if}" rel="blue" onclick="_changeColor(this);"></div>
            <div style="background-color:aqua" class="yntour_bgcolor {if (isset($aStep.fcolor) && $aStep.fcolor=="aqua")}yntour_color_selected{/if}" rel="aqua" onclick="_changeColor(this);"></div>
        </div>
    </div>
</div>
<div class="extra_info">
    <div class="yntour_span_color">{_p var='position_display'}:</div>
     <div class="tags form-inline" >
        <select class="form-control" id="yntour_select_value" style="padding:0px;" onchange="_onchangeYnTour(this);">
            <option value="TL" {if (isset($aStep.position) && $aStep.position=="TL")}selected="selected"{/if}>{_p var='top_left'}</option>
            <option value="TR" {if (isset($aStep.position) && $aStep.position=="TR")}selected="selected"{/if}>{_p var='top_right'}</option>
            <option value="BL" {if (isset($aStep.position) && $aStep.position=="BL")}selected="selected"{/if}>{_p var='bottom_left'}</option>
            <option value="BR" {if (isset($aStep.position) && $aStep.position=="BR")}selected="selected"{/if}>{_p var='bottom_right'}</option>
            <option value="LT" {if (isset($aStep.position) && $aStep.position=="LT")}selected="selected"{/if}>{_p var='left_top'}</option>
            <option value="LB" {if (isset($aStep.position) && $aStep.position=="LB")}selected="selected"{/if}>{_p var='left_right'}</option>
            <option value="RT" {if (isset($aStep.position) && $aStep.position=="RT")}selected="selected"{/if}>{_p var='right_top'}</option>
            <option value="RB" {if (isset($aStep.position) && $aStep.position=="RB")}selected="selected"{/if}>{_p var='right_bottom'}</option>
            <option value="T" {if (isset($aStep.position) && $aStep.position =="T")}selected="selected"{/if}>{_p var='top'}</option>
            <option value="R" {if (isset($aStep.position) && $aStep.position =="R")}selected="selected"{/if}>{_p var='right'}</option>
            <option value="B" {if (isset($aStep.position) && $aStep.position =="B")}selected="selected"{/if}>{_p var='bottom'}</option>
            <option value="L" {if (isset($aStep.position) && $aStep.position =="L")}selected="selected"{/if}>{_p var='left'}</option>
        </select>
        </div>
</div>
<div class="extra_info">
    <div class="yntour_span_color">{_p var='time_display'}</div> <div style="margin-top: -5px;" class="tags"><input type="text" style="margin:0; margin-right: 10px" size="11" id="yntour_delay" value="{$aStep.delay}" name="tour_delay">{_p var='second_s'}</div></div>
<div class="extra_info">
    <input type="button" class="btn btn-primary" name="yn_jtour_button_save_continue" id="yn_jtour_button_save_continue" value="{_p var='save_countinue'}" onclick="return _saveCountinue();">
</div>
<input type="hidden" id="yn_tour_element" name="tour_element" value="{$aStep.step_element}">
<input type="hidden" id="yn_tour_position_body" name="tour_position" value="Top: 117 Left: 74">
<input type="hidden" id="yn_tour_fcolor_input" name="tour_fcolor" value="{$aStep.fcolor}">
<input type="hidden" id="yn_tour_bgcolor_input" name="tour_bgcolor" value="{$aStep.bgcolor}">
<input type="hidden" id="tour_tourguide_id" name="tour_tourguide_id" value="{$aStep.tourguide_id}">
<input type="hidden" id="yn_tour_position" name="position" value="{$aStep.position}">
<input type="hidden" id="id" name="id" value="{$aStep.id}">
</form>
</div>
