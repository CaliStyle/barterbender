<?php 
    /**
    * [PHPFOX_HEADER]
    * 
    * @copyright		[PHPFOX_COPYRIGHT]
    * @author  		Raymond Benc
    * @package 		Phpfox
    * @version 		$Id: index.html.php 1544 2010-04-07 13:20:17Z Raymond_Benc $
    */

    defined('PHPFOX') or exit('NO DICE!'); 

?>
<form action="{url link='admincp.socialpublishers.modules'}" method="post">
        <div class="panel panel-default">
            {foreach from=$aModules item=aModule}
                <div class="panel-heading">
                    <div class="panel-title">
                        {_p var=$aModule.title}
                    </div>
                </div>
                <div class="panel-body table">
                    <table>
                        <tr>
                            <td style="width: 100%;">
                                <div class="table_left">
                                    {required}{_p var='socialpublishers.active'}
                                </div>
                                <div class="table_left">
                                    {required}{_p var='socialpublishers.publish_provides'}
                                </div>
                                <div class="table_right">
                                    <label><input type="checkbox" value="1" name="val[{$aModule.module_id}][facebook]" {if $aModule.facebook == 1}checked{/if}/>{_p var='socialpublishers.facebook'}</label>
                                    <label><input type="checkbox" value="1" name="val[{$aModule.module_id}][twitter]" {if $aModule.twitter == 1}checked{/if}/>{_p var='socialpublishers.twitter'}</label>
                                    <label><input type="checkbox" value="1" name="val[{$aModule.module_id}][linkedin]" {if $aModule.linkedin == 1}checked{/if}/>{_p var='socialpublishers.linkedin'}</label>
                                </div>
                            </td>
                            <td class="w40">
                                <div class="item_can_be_closed_holder">
                                    <div class="js_item_is_active"{if !$aModule.is_active} style="display:none;"{/if}>
                                        <a href="#?call=socialpublishers.activeModule&amp;id={$aModule.module_id}&amp;active=0" class="js_item_active_link" title="{_p('deactivate')}"></a>
                                    </div>
                                    <div class="js_item_is_not_active"{if $aModule.is_active} style="display:none;"{/if}>
                                        <a href="#?call=socialpublishers.activeModule&amp;id={$aModule.module_id}&amp;active=1" class="js_item_active_link" title="{_p('activate')}"></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            {/foreach}
            <div class="panel-footer">
                <input type="submit" value="{_p var='core.submit'}" class="btn btn-primary" name="submit" />
            </div>
        </div>
</form>
{literal}
<style type="text/css">
    input[type=checkbox], input[type=radio]
    {
        margin-right: 5px;
    }
</style>
{/literal}