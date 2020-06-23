<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 4938 2012-10-23 08:21:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aPackages)}
<div class="panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='packages'}
        </div>
    </div>
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered"  cellpadding="0" cellspacing="0" id="js_ynsa_package_list">
            <thead>
                <tr class="nodrop nodrag">
                    <th class="w20"></th>
                    <th class="w20"></th>
                    <th class="w20">{_p var='id'}</th>
                    <th>{_p var='name'}</th>
                    <th>{_p var='price'}</th>
                    <th>{_p var='benefit'}</th>
                    <th>{_p var='allowed_ad_types'}</th>
                    <th style="width:50px;">{_p var='active'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aPackages key=iKey item=aPackage}
                <tr class="">
                    <td class="drag_handle"><input type="hidden" name="val[ordering][{$aPackage.package_id}]" value="{$aPackage.package_order}" /></td>
                    <td class="">
                        <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="{url link='admincp.socialad.package.add' id=$aPackage.package_id}">{_p var='edit'}</a></li>
                                <li><a href="{url link='admincp.socialad.package' delete=$aPackage.package_id}" onclick="return confirm('{_p var='admincp.are_you_sure' phpfox_squote=true}');">{_p var='delete'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class=""> <span class="jsYnsaPackageId"> {$aPackage.package_id} </span></td>
                    {*<td class="">{$aPackage.package_name|clean|shorten:50:'...'}</td>*}
                    <td class=""><a href="#" onclick="tb_show('{_p var='package'}', $.ajaxBox('socialad.showPackagePopup', 'height=400&width=900&package_id={$aPackage.package_id}')); return false;">{$aPackage.package_name|clean|shorten:50:'...'}</a></td>
                    <td class="">{$aPackage.package_price_text}</td>
                    <td class="">{$aPackage.package_benefit_text}</td>
                    <td class="">{$aPackage.package_allow_ad_type_text}</td>
                    <td class="">
                        <div class="js_item_is_active"{if !$aPackage.package_is_active} style="display:none;"{/if}>
                            <a href="#?call=socialad.togglePackage&amp;id={$aPackage.package_id}&amp;active=0" class="js_item_active_link" title="{_p var='rss.deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active"{if $aPackage.package_is_active} style="display:none;"{/if}>
                            <a href="#?call=socialad.togglePackage&amp;id={$aPackage.package_id}&amp;active=1" class="js_item_active_link" title="{_p var='rss.activate'}"></a>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
<div class="extra_info">{_p var='drag_and_drop_to_arrange_package_display_order'}</div>

{else}
	<div class="extra_info">{_p var='no_packages_found'}</div>
{/if}

<script>
{literal}
$Behavior.ynsaInitDragDropPackage = function() {
	$('#js_ynsa_package_list').tableDnD({
		onDragClass : 'ynsaTableHighlight',
		onDrop: function() {

			var arrayId = [];
			$('.jsYnsaPackageId').each(function() {
				arrayId.push(parseInt($(this).html(), 10));
			});
			var idList = arrayId.join(',');
			$.ajaxCall('socialad.updatePackageOrder', 'list=' + idList);	
		}
	});
}
{/literal}

</script>

