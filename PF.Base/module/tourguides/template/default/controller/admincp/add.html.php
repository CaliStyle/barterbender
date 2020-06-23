<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>

{literal}
<style>
   .yntour_add_step_number {
    display: block !important;
    font-size: 11px !important;
    font-weight: bold !important;
    
    line-height: 22px !important;
    padding: 0 7px !important;
    position: absolute !important;
    
} 
</style>
{/literal}
{$sCreateJs}
<form method="post" action="{url link="admincp.tourguides.add"}" id="js_form" onsubmit="{$sGetJsForm}">
    {if $bIsEdit}
        <div><input type="hidden" name="id" value="{$aForms.id}" /></div>
    {/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='tourguide_details'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {required}{_p var='name'}:
                </label>
                <input class="form-control" type="text" name="val[name]" value="{value type='input' id='name'}" id="name" size="50" />
            </div>
            <div class="form-group">
                <label>
                    {required}{_p var='url'}:
                </label>
                <input class="form-control" type="text" name="val[url]" value="{value type='input' id='url'}" id="url" size="50" /> <!--<span><a id="yntour_get_controller" href="javascript:void(0);" onclick="_getController(this);">{_p var='get_controller'}</a></span>-->
            </div>
            {*<div class="form-group">
                <label>
                    {required}{_p var='controller'}:
                </label>
                <select class="form-control" name="val[controller]" id="yn_tour_controller" onchange="_changeController(this);">
                    {foreach from=$aBlocks key=sUrl item=aModules}
                    {if $sUrl !=""}
                    <option value="{$sUrl}" {if isset($aForms.controller) && $aForms.controller == $sUrl}selected{/if}>{$sUrl}</option>
                    {/if}
                    {/foreach}
                </select>
            </div>*}
            <div class="form-group">
                <label>
                    {required}{_p var='type'}:
                </label>
                <select class="form-control" name="val[is_member]" id="yn_tour_guide_member" onchange="_changeMember(this);">
                    <option value="2" {if isset($aForms.is_member) && $aForms.is_member == 2}selected{/if}>{_p var='anyone'}</option>
                    <option value="1" {if isset($aForms.is_member) && $aForms.is_member == 1}selected{/if}>{_p var='member'}</option>
                    <option value="0" {if isset($aForms.is_member) && $aForms.is_member == 0 }selected{/if}>{_p var='guest'}</option>
                </select>
            </div>

            <div class="form-group">
                <label>
                    {_p var='is_active'}:
                </label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1'}/> {phrase var='core.yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0' selected='true'}/> {phrase var='core.no'}</span>
                </div>
            </div>
            <div class="form-group">
                <label>
                    {_p var='is_auto'}:
                </label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_auto]" value="1" {value type='radio' id='is_auto' default='1'}/> {phrase var='core.yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_auto]" value="0" {value type='radio' id='is_auto' default='0' selected='true'}/> {phrase var='core.no'}</span>
                </div>
            </div>

            <!-- Is use controller? -->
            {if $bIsEdit && empty($aForms.controller) == false}
                <div class="form-group">
                    <label>
                        {_p var='is_use_controller_var' yntgfullcontrollername=$aForms.controller}:
                    </label>
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active"><input type="radio" name="val[is_use_controller]" value="1" {value type='radio' id='is_use_controller' default='1'}/> {phrase var='core.yes'}</span>
                        <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_use_controller]" value="0" {value type='radio' id='is_use_controller' default='0' selected='true'}/> {phrase var='core.no'}</span>
                    </div>
                </div>
            {/if}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='admincp.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>
{if $bIsEdit}
<input type="hidden" value="{$aForms.is_member}" name="yntour_tour_type_id" id="yntour_tour_type_id">
{/if}
    {if $bIsEdit}
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                {_p var='mange_tour_guides_steps'}
                </div>
            </div>
            {if count($aSteps) <=0}
                <div class="error_message" style="margin: 0;">{_p var='there_are_no_steps_added'}</div>
                <div class="panel-footer"> <input type="button" class="btn btn-primary" value="{_p var='add_new_step'}" onclick="_onAddNewStep({$aForms.id});return false;"/></div>
            {else}
                <div class="table-responsive">
                    <table id="js_drag_drop" class="table table-bordered" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="w20"></th>
                                <th class="w20">{_p var='step'}</th>
                                <th>{_p var='description'}</th>
                                <th class="t_center w140">{_p var='actions'}</th>
                                <th class="t_center w80">{_p var='active'}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $iStepOrder = 0;?>
                        {foreach from=$aSteps key=iKey item=aStep}
                        <?php $iStepOrder++;?>
                        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td class="drag_handle" style="background-image: none!important;"><input type="hidden" name="valstep[ordering][{$aStep.id}]" value="{$aStep.orderring}"/></td>
                            <td><span class="yntour_add_step_number" style="background-color:{$aStep.bgcolor};padding:4px;color:{$aStep.fcolor};"><?php echo $iStepOrder;?></span></td>
                            <td>{$aStep.default_description|clean|shorten:150:'core.view_more':true}</td>
                            <td class="t_center">
                                <a href="javascript:void(0);" onclick="tb_show('{phrase var="tourguides.step"} <?php echo $iStepOrder;?>', $.ajaxBox('tourguides.editstep', 'height=450&amp;width=420&amp;id={$aStep.id}&amp;step=<?php echo $iStepOrder;?>')); return false;">{phrase var='core.edit'} </a> | <a  href="{url link='admincp.tourguides.add' delete=$aStep.id tour=$aStep.tourguide_id}" class="sJsConfirm">{phrase var='core.delete'}</a>
                            </td>
                            <td class="t_center">
                                <div class="js_item_is_active"{if !$aStep.is_active} style="display:none;"{/if}>
                                    <a href="#?call=tourguides.updateActivityStep&amp;id={$aStep.id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                                </div>
                                <div class="js_item_is_not_active"{if $aStep.is_active} style="display:none;"{/if}>
                                    <a href="#?call=tourguides.updateActivityStep&amp;id={$aStep.id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                                </div>
                            </td>
                        </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <input type="button" class="btn btn-primary" value="{_p var='add_new_step'}" onclick="_onAddNewStep({$aForms.id});$(this).prop('disabled',true);return false;"/>
                </div>
            {/if}
        </div>
    {/if}
{literal}
<script type="text/javascript">
function _onAddNewStep(id)
{
   $Core.ajax('tourguides.onaddnewstep', 
    {
        params: 
        {     
            data: id
        },
        type: 'POST',     
        success: function(sData)            
        {
            if(sData)
            {
                sData = $.parseJSON(sData);
            }
            if(sData.url)
            {
                if(sData.is_member == 0)
                {
                    if(confirm("{/literal}{_p var='are_you_sure_to_go_this_page_to_add_step_nyou_have_to_log_out_to_see_this_url_due_to_it_s_only_for'}{literal}"))
                    {
                        window.location.href = sData.admincp_url_return;
                    }
                    
                }
                else
                {
                     window.location.href = sData.url; 
                }
                
            }
        }
    }); 
}
function _changeColor(ele)
{
    
    var $p = $(ele).parent().parent();
        if($p.attr('id'))
            {
            if($p.attr('id') == 'yntour_bg_color')
                {
                $('#yn_tour_bgcolor_input').val($(ele).attr('rel'));
                $('div#yntour_bg_color div.yntour_bgcolor').removeClass('yntour_color_selected'); 
                $(ele).addClass('yntour_color_selected');
            }
            if($p.attr('id') == 'yntour_font_color')
                {
                $('#yn_tour_fcolor_input').val($(ele).attr('rel'));
                $('div#yntour_font_color div.yntour_bgcolor').removeClass('yntour_color_selected'); 
                $(ele).addClass('yntour_color_selected');
            }
        }                      
}
function _onchangeYnTour(ele)
{
    $('#yn_tour_position').val($(ele).val());  
}

function _onchangeYnTourLang(ele)
{    
    var sId = $(ele).val();        
    $('.js_tour_lang_area.active').removeClass('active').fadeOut(100,function(){$('#js_tour_lang_area_'+sId).fadeIn(300).addClass('active');});
}

function _saveCountinue()
{
    $Core.ajax('tourguides.saveEditStep', 
    {
        params: 
        {     
            data: $("#yntour_edit_form").serialize(),
        },
        type: 'POST',     
        success: function(sData)            
        {
            if(sData)
            {
                sData = $.parseJSON(sData);
            }
            if(sData.id >0)
            {
                tb_remove();
                window.location.href = window.location.href;
            }
        }
    });
}
function _getController(ele)
{
    $(ele).html($.ajaxProcess('no_message'));
    
    $Core.ajax('tourguides.getcontroler', 
    {
        params: 
        {     
            data: $("#url").val(),
        },
        type: 'POST',     
        success: function(sData)            
        {
            
            if(sData)
            {
                sData = $.parseJSON(sData);
            }
            if(sData.success == true)
            {
                $('#yn_tour_controller').val(sData.controller);
                if(sData.controller == 'core.index-visitor')
                {
                    $('#yn_tour_guide_member').val(0);
                }
            }
            else
            {
                alert(sData.message);
            }
            $(ele).html("{/literal}{_p var='get_controller'}{literal}");
        }
    });
}
function _changeController(ele){
   if($(ele).val() == 'core.index-visitor')
   {
        $('#yn_tour_guide_member').val(0); 
   } 
   if($(ele).val() == 'core.index-member')
   {
        $('#yn_tour_guide_member').val(1); 
   } 
}
function _changeMember(ele){
   if($(ele).val() == 1 && $('#yn_tour_controller').val() =='core.index-visitor')
   {
        $('#yn_tour_controller').val('core.index-member');
   } 
    if($(ele).val() == 0 && $('#yn_tour_controller').val() =='core.index-member')
   {
        $('#yn_tour_controller').val('core.index-visitor');
   } 
}
</script>
{/literal}