<?php
?>
{literal}
	<style type="text/css">
		th, td 
		{
			text-align:left;
		}
	</style>
	<script language="JavaScript" type="text/javascript">
		$Behavior.initAdvancedmarketplace = function() {
			$("#js_mp_category_item_{/literal}{$iCategoryId}{literal}").attr({
				"selected": "selected"
			});
			
			$(".jsaction").find("a").each(function(){
				var $this = $(this);
				
				$this.click(function(evt) {
					evt.preventDefault();
					
					var $_this = $(this);
					var path = $_this.attr("href").split("/");
					var id = path[path.length - 3]
					
					$("select").val("");
					$("#search-category").val(id);
					setTimeout(function(){
						$("#frm_submitbtn").click();
					}, 1);
					
					return false;
				});
			});
		}
	</script>
{/literal}
<div id="js_admincp_manage_listings">
    <form method="get" action="{url link='admincp.advancedmarketplace.advancedmarketplace'}">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {phrase var='advancedmarketplace.listing_filter'}
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="">{phrase var='advancedmarketplace.listing_name'}:</label>
                    {$aFilters.listing}
                </div>
                <div class="form-group">
                    <label for="">{phrase var='advancedmarketplace.owner_name'}:</label>
                    {$aFilters.owner}
                </div>
                <div class="form-group">
                    <label for="">{phrase var='advancedmarketplace.category'}:</label>
                    <select id="search-category" name="search[category]" class="form-control">
                        <option value="">{phrase var='advancedmarketplace.select'}:</option>
                        {$sCategories}
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{phrase var='advancedmarketplace.status'}:</label>
                    {$aFilters.status}
                </div>
                <div class="form-group">
                    <label for="">{phrase var='advancedmarketplace.featured'}:</label>
                    {$aFilters.feature}
                </div>
                <div class="form-group">
                    <label for="">{phrase var='advancedmarketplace.sponsored'}:</label>
                    {$aFilters.sponsored}
                </div>
                <div class="form-group">
                    <label for="">{phrase var='advancedmarketplace.draft'}:</label>
                    {$aFilters.draft}
                </div>
            </div>

            <div class="panel-footer">
                <input type="submit" id="frm_submitbtn" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
                <input type="button" value="{phrase var='core.reset'}" class="btn btn-primary" onclick="return admincpManageListings.resetForm(this);"/>
            </div>
        </div>
    </form>

    {if count($aListings)}
    <form method="post" action="{url link='admincp.advancedmarketplace.advancedmarketplace'}">

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive flex-sortable">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="w20"><input type="checkbox" value="" id="checkAll" name="checkAll" onclick="javascript:selectAll()"/></th>
                            <th class="w20"></th>
                            <th>{phrase var='advancedmarketplace.listing_name'}</th>
                            <th>{phrase var='advancedmarketplace.owner_name'}</th>
                            <th class="w180">{phrase var='advancedmarketplace.category'}</th>
                            <th>{phrase var='advancedmarketplace.status'}</th>
                            {if Phpfox::getUserParam('advancedmarketplace.can_feature_listings')}
                            <th class="w60">{phrase var='advancedmarketplace.featured'}</th>
                            {/if}
                            {if Phpfox::getUserParam('advancedmarketplace.can_sponsor_advancedmarketplace')}
                            <th class="w60">{phrase var='advancedmarketplace.sponsored'}</th>
                            {/if}
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$aListings key=iKey item=aListing}
                        <tr class="{if is_int($iKey/2)} tr{else}{/if}">
                            <script lang="javascript" type="text/javascript">
                                /* {literal} */
                                var advmarket_todaylistingaction = function(owner){
                                    tb_show("{/literal}{phrase var="advancedmarketplace.today_listing" phpfox_squote=true}{literal}", $.ajaxBox('advancedmarketplace.todaylistingPopup', 'height=230&width=auto&id=' + $(owner).parent().find(".yn_lid").val()));
                                    $("#js_drop_down_cache_menu").hide();
                                    return false;
                                }
                                /* {/literal} */
                            </script>
                            <td class="">
                                <input type="checkbox" value="{$aListing.listing_id}" name="is_selected" style="margin-top:0px;" class="js_advmarketplace_checkbox"/>
                                <input type="hidden" value="{$aListing.is_featured}" id="is_selected_active_{$aListing.listing_id}" />
                                <input type="hidden" value="{$aListing.is_sponsor}" id="is_sponsor_selected_active_{$aListing.listing_id}" />
                            </td>
                            <td class="t_center">
                                <a href="#" class="js_drop_down_link" title="{phrase var='advancedmarketplace.manage'}"></a>
                                <div class="link_menu">
                                    <ul>
                                        <li><a href="{url link='advancedmarketplace.add' id=$aListing.listing_id}" ">{phrase var='advancedmarketplace.edit'}</a></li>
                                        <li><a href="{url link='admincp.advancedmarketplace.advancedmarketplace' delete=$aListing.listing_id}" class="sJsConfirm">{phrase var='delete'}</a></li>
                                        <li>
                                            <a href="#" class="yn_popup___calendar_listing" onclick="return advmarket_todaylistingaction(this);">
                                                {phrase var='advancedmarketplace.set_as_today_listing'}
                                                <input type="hidden" value="{$aListing.listing_id}" name="yn_lid" class="yn_lid" />
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            {*<td>{$aListing.listing_id}</td>*}
                            <td class="">
                                <a href="{url link='advancedmarketplace.detail.'.{$aListing.listing_id}{$aListing.title}">{$aListing.title}</a>
                                {if $aListing.post_status == 2}
                                <div>{phrase var='advancedmarketplace.draft_info'}</div>
                                {/if}</br>
                                <div class="extra_info" style="font-size:11px;">{$aListing.time_stamp}</div>
                            </td>
                            <td>{$aListing.full_name}</td>
                            <td class="jsaction">{$aListing.categories_last|category_display}</td>
                            <td>
                                {if $aListing.view_id == 0}
                                {phrase var='advancedmarketplace.opened'}
                                {elseif $aListing.view_id == 1}
                                {phrase var='advancedmarketplace.pending'}
                                {else}
                                {phrase var='advancedmarketplace.closed'}
                                {/if}
                            </td>
                            {if Phpfox::getUserParam('advancedmarketplace.can_feature_listings') && $aListing.post_status != 2 }
                            <td class="t_center ">
                                <div class="js_item_is_feature" id="js_listing_is_feature_{$aListing.listing_id}" {if $aListing.is_featured}style="display: none"{else}style="display: block"{/if}>
                                <a title="{phrase var='advancedmarketplace.feature'}" class="js_item_active_link" href="#" onclick="$.ajaxCall('advancedmarketplace.feature', 'listing_id={$aListing.listing_id}&amp;type=1&amp;admin_page=1', 'GET'); $(this).parent().hide(); $(this).parents('td:first').find('.js_item_is_un_feature').show(); return false;"><img alt="" src="{$core_path}theme/adminpanel/default/style/default/image/misc/bullet_red.png"></a>
                </div>
                <div class="js_item_is_un_feature" id="js_listing_is_un_feature_{$aListing.listing_id}" {if !$aListing.is_featured}style="display: none"{else}style="display: block"{/if}>
                <a title="{phrase var='advancedmarketplace.un_feature'}" class="js_item_active_link" hreft="#" onclick="$.ajaxCall('advancedmarketplace.feature', 'listing_id={$aListing.listing_id}&amp;type=0&amp;admin_page=1', 'GET'); $(this).parent().hide(); $(this).parents('td:first').find('.js_item_is_feature').show(); return false;"><img alt="" src="{$core_path}theme/adminpanel/default/style/default/image/misc/bullet_green.png"></a>
    </form>
    </td>
    {/if}
    {if Phpfox::getUserParam('advancedmarketplace.can_sponsor_advancedmarketplace') && $aListing.post_status != 2 }
    <td class="t_center w60">
        <div class="js_item_is_sponsor" id="js_listing_is_sponsor_{$aListing.listing_id}" {if $aListing.is_sponsor}style="display: none"{else}style="display: block"{/if}>
        <a title="{phrase var='advancedmarketplace.sponsor_this_listing'}" class="js_item_active_link" href="#" onclick="$.ajaxCall('advancedmarketplace.sponsor', 'listing_id={$aListing.listing_id}&amp;type=1', 'GET'); $(this).parent().hide(); $(this).parents('td:first').find('.js_item_is_un_sponsor').show(); return false;"><img alt="" src="{$core_path}theme/adminpanel/default/style/default/image/misc/bullet_red.png"></a>
</div>
<div class="js_item_is_un_sponsor" id="js_listing_is_un_sponsor_{$aListing.listing_id}" {if !$aListing.is_sponsor}style="display: none"{else}style="display: block"{/if}>
<a title="{phrase var='advancedmarketplace_un_sponsor'}" class="js_item_active_link" hreft="#" onclick="$.ajaxCall('advancedmarketplace.sponsor', 'listing_id={$aListing.listing_id}&amp;type=0', 'GET'); $(this).parent().hide(); $(this).parents('td:first').find('.js_item_is_sponsor').show(); return false;"><img alt="" src="{$core_path}theme/adminpanel/default/style/default/image/misc/bullet_green.png"></a>
</div>
</td>
{/if}
{if $aListing.post_status == 2}
<td></td>
{/if}
</tr>
{/foreach}
</tbody>
</table>
</div>
</div>
</form>

<div class="panel-footer">
    <form id="delete_sb" action="{url link='admincp.advancedmarketplace.advancedmarketplace'}" method="post" style="display: inline; padding-right: 3px;">
        <input type="hidden" value="" name="val[arr_selected]" id="arr_selected"/>
        <input type="hidden" value="is_delete" name="is_delete" id="is_delete"/>
        <input type="submit" name="deleteselect" value="{phrase var='advancedmarketplace.delete_selected'}" class="js_adv_delete_button sJsConfirm btn btn-primary disabled" disabled="disabled" onclick="is_listing = true;javascript:setValue();if ( setValue() ==true )document.getElementById('delete_sb').submit();" data-message="{_p var='are_you_sure'}"/>
    </form>
    {if Phpfox::getUserParam('advancedmarketplace.can_feature_listings')}<input type="submit" value="{phrase var='advancedmarketplace.featured_selected'}" name="feature_selected" style="margin-right: 5px" class="btn btn-primary" onclick="featureSelected(0); return false;" />{/if}
    {if Phpfox::getUserParam('advancedmarketplace.can_feature_listings')}<input type="submit" value="{phrase var='advancedmarketplace.un_feature_selected'}" name="un_feature_selected" style="margin-right: 5px" class="btn btn-primary" onclick="featureSelected(1); return false;" />{/if}
    {if Phpfox::getUserParam('advancedmarketplace.can_sponsor_advancedmarketplace')}<input type="submit" value="{phrase var='advancedmarketplace.sponsored_selected'}" name="sponsor_selected" class="btn btn-primary" onclick="sponsorSelected(); return false;" />{/if}
</div>
</div>
{else}
{if $bIsSearch}
<div class="extra_info">{phrase var='advancedmarketplace.no_listings_found'}</div>
{else}
<div class="extra_info">{phrase var='advancedmarketplace.no_listings_have_been_created'}</div>
{/if}
{/if}
{pager}
</div>

