<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<script type="text/javascript">
{literal}
        $Behavior.ynppInitEvent = function() 
        {
                $('#update').click(function(event)
                {
                        event.preventDefault();
                        if($('#public_message'))
                        {
                                $('#public_message').remove();
                        }
                        if($('#core_js_messages'))
                        {
                                $('#core_js_messages').html('');
                        }
                        $('#update').ajaxCall('profilepopup.updateGlobalSettings', '&item_type=event' + '&' + $('#globalSettings').serialize());
                }); 
                
                $('.checkbox').change(function(){
                        var sIdName = '#js_row' + $(this).get(0).id.replace('js_id_row', '');
                        if ($(sIdName).hasClass('is_checked'))
                        {
                                $(sIdName).removeClass('is_checked');
                        }
                        $('.sJsCheckBoxButton').removeClass('disabled');
                        $('.sJsCheckBoxButton').attr('disabled', false); 
                });
                $("#js_check_box_all").change(function(){
                        $('.checkRow').removeClass('is_checked');
                        $('.sJsCheckBoxButton').removeClass('disabled');
                        $('.sJsCheckBoxButton').attr('disabled', false); 
                });
        };
{/literal}
</script>
<style type="text/css">
        {literal}
        {/literal}
</style>

{if count($aAllItems)}
    <div class="panel panel-default">
        <form id="globalSettings" method="post" action="{url link='admincp.profilepopup.event'}">
            <div class="panel-heading">
                <div class="panel-title">
                    <div class="p_4"><span style="font-weight: bold; text-decoration: underline; font-size: 15px;">{phrase var='profilepopup.notice'}</span>: {phrase var='profilepopup.event_global_settings_info'}</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive flex-sortable">
                    <table id="js_drag_drop" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{phrase var='profilepopup.field_label'}</th>
                            <th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                            <th>{phrase var='profilepopup.global_settings_ordering'}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$aAllItems key=iKey item=aItem}
                        <tr id="js_row{$aItem.item_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td>{$aItem.lang_name}</td>
                            <td><input type="checkbox" name="display[]" class="checkbox" {$aItem.checked} value="{$aItem.item_id}" id="js_id_row{$aItem.item_id}" /></td>
                            <td>
                                {if $aItem.name != 'cover_photo'}
                                <input type="text" name="ordering[]" value="{$aItem.ordering}" id="ordering[]" size="5" maxlength="5" style="text-align: center;" />
                                <input type="hidden" name="id[]" value="{$aItem.item_id}" />
                                {/if}
                            </td>
                        </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel-footer">
                <input type="button" id="update" name="update" value="{phrase var='profilepopup.global_settings_update'}" class="btn btn-primary sJsCheckBoxButton" />
            </div>
        </form>
    </div>
{else}
        <div class="p_4">
                {phrase var='profilepopup.no_event_global_settings'}
        </div>
{/if}
