<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.ynfrInitializeStatisticJs = function(){
        $("#js_from_date_listing").datepicker({
            dateFormat: 'mm/dd/yy',
            onSelect: function(dateText, inst) {
                var $dateTo = $("#js_to_date_listing").datepicker("getDate");
                var $dateFrom = $("#js_from_date_listing").datepicker("getDate");
                if($dateTo)
                {
                    $dateTo.setHours(0);
                    $dateTo.setMilliseconds(0);
                    $dateTo.setMinutes(0);
                    $dateTo.setSeconds(0);
                }

                if($dateFrom)
                {
                    $dateFrom.setHours(0);
                    $dateFrom.setMilliseconds(0);
                    $dateFrom.setMinutes(0);
                    $dateFrom.setSeconds(0);
                }

                if($dateTo && $dateFrom && $dateTo < $dateFrom) {
                    tmp = $("#js_to_date_listing").val();
                    $("#js_to_date_listing").val($("#js_from_date_listing").val());
                    $("#js_from_date_listing").val(tmp);
                }
                return false;
            }
        });
        $("#js_to_date_listing").datepicker({
            dateFormat: 'mm/dd/yy',
            onSelect: function(dateText, inst) {
                var $dateTo = $("#js_to_date_listing").datepicker("getDate");
                var $dateFrom = $("#js_from_date_listing").datepicker("getDate");

                if($dateTo)
                {
                    $dateTo.setHours(0);
                    $dateTo.setMilliseconds(0);
                    $dateTo.setMinutes(0);
                    $dateTo.setSeconds(0);
                }

                if($dateFrom)
                {
                    $dateFrom.setHours(0);
                    $dateFrom.setMilliseconds(0);
                    $dateFrom.setMinutes(0);
                    $dateFrom.setSeconds(0);
                }

                if($dateTo && $dateFrom && $dateTo < $dateFrom) {
                    tmp = $("#js_to_date_listing").val();
                    $("#js_to_date_listing").val($("#js_from_date_listing").val());
                    $("#js_from_date_listing").val(tmp);
                }
                return false;
            }
        });

        $("#js_from_date_listing_anchor").click(function() {
            $("#js_from_date_listing").focus();
            return false;
        });

        $("#js_to_date_listing_anchor").click(function() {
            $("#js_to_date_listing").focus();
            return false;
        });
    };
</script>
{/literal}

<!-- Filter Search Form Layout -->
<form class="ynfr" method="GET" action="{url link='admincp.directory.managebusiness'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var="directory.search_filter"}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="title">{phrase var="directory.business"}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title">
            </div>

            <div class="form-group">
                <label for="owner">{phrase var='business_owner'}:</label>
                <input class="form-control" type="text" name="search[owner]" value="{value type='input' id='owner'}" id="owner">
            </div>

            <div class="form-group">
                <label for="creator">{phrase var='creator'}:</label>
                <input class="form-control" type="text" name="search[creator]" value="{value type='input' id='creator'}" id="creator">
            </div>
            <div class="form-group">
                <label for="category_id">{phrase var='category'}:</label>
                <select name="search[category_id]" id="category_id" class="form-control">
                    <option value="0">{phrase var='any'}</option>
                    {foreach from=$aCategories item=aCategoriesItem}
                        {if Phpfox::isPhrase($this->_aVars['aCategoriesItem']['title'])}
                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategoriesItem']['title']) ?>
                        {else}
                            {assign var='value_name' value=$aCategoriesItem.title|convert}
                        {/if}
                        <option value="{$aCategoriesItem.category_id}" {value type='select' id='category_id' default=$aCategoriesItem.category_id}>
                            {$value_name}
                        </option>
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <label for="status">{phrase var='status'}:</label>
                <select name="search[status]" id="status" class="form-control">
                    <option value="0">{phrase var='any'}</option>
                    <option value="published" {value type='select' id='status' default = 'published'}>{phrase var='published'}</option>
                    <option value="denied" {value type='select' id='status' default = 'denied'}>{phrase var='denied'}</option>
                    <option value="expired" {value type='select' id='status' default = 'expired'}>{phrase var='expired'}</option>
                    <option value="pending" {value type='select' id='status' default = 'pending'}>{phrase var='pending'}</option>
                    <option value="pending_for_claiming" {value type='select' id='status' default = 'pending_for_claiming'}>{phrase var='pending_for_claiming'}</option>
                    <option value="claiming" {value type='select' id='status' default = 'claiming'}>{phrase var='claiming'}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="feature">{phrase var="directory.featured"}:</label>
                <select name="search[feature]" id="feature" class="form-control">
                    <option value="0">{phrase var='any'}</option>
                    <option value="featured"  {value type='select' id='feature' default = 'featured'}>{phrase var='featured'}</option>
                    <option value="not_featured"  {value type='select' id='feature' default = 'not_featured'}>{phrase var='not_featured'}</option>
                </select>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" id="filter_submit" name="search[submit]" value="{phrase var='search'}" class="btn btn-primary">
        </div>
    </div>
</form>
<br/>

<span id="yndirectory_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
{if count($aList) >0}
    <form action="{url link='current'}" method="post" id="business_list" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {phrase var="directory.business"}
                </div>
            </div>
            
            <div class="table-responsive flex-sortable">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="t_center w40"></th>
                            <th class="t_center">{phrase var='business'}</th>
                            <th class="t_center">{phrase var='category'}</th>
                            <th class="t_center">{phrase var='creator'}</th>
                            <th class="t_center">{phrase var='business_owner'}</th>
                            <th class="t_center">{phrase var='status'}</th>
                            <th class="t_center">{phrase var='featured'}</th>
                            <th class="t_center">{phrase var='valid_packages'}</th>
                        </tr>
                    </thead>

                    { foreach from=$aList key=iKey item=aItem }
                    <tr class="{if $iKey%2 == 0 }tr{/if}">
                        <!-- Options -->
                        <td class="t_center w40">
                            <a href="#" class="js_drop_down_link" title="{_p var='Options'}"></a>
                            <div class="link_menu">
                                <ul>
                                    {if $aItem.type == 'claiming'}
                                        {if $aItem.bCanDelete == true}
                                            <li><a href="javascript:void(0)" onclick="managebusiness.confirmdeleteBusiness({$aItem.business_id}); return false;">{phrase var='delete'}</a></li>
                                        {/if}
                                    {else}
                                        {if $aItem.bCanApprove == true && $aItem.type == 'business' && $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.pending')}
                                            <li><a href="javascript:void(0)" onclick="managebusiness.approveBusiness({$aItem.business_id}); return false;">{phrase var='approve'}</a></li>
                                            <li><a href="javascript:void(0)" onclick="managebusiness.denyBusiness({$aItem.business_id}); return false;">{phrase var='deny'}</a></li>
                                        {/if}
                                        {if $aItem.bCanDelete == true}
                                            <li><a href="javascript:void(0)" onclick="managebusiness.confirmdeleteBusiness({$aItem.business_id}); return false;">{phrase var='delete'}</a></li>
                                        {/if}

                                        {if $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.completed')
                                        || $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.approved')
                                        || $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.running')
                                        }
                                            <li><a href="javascript:void(0)" onclick="managebusiness.openTransferownerBusiness({$aItem.business_id}); return false;">{phrase var='transfer_owner'}</a></li>
                                        {/if}
                                    {/if}
                                </ul>
                            </div>
                        </td>

                        <td>
                            <a href="{permalink module='directory.detail' id=$aItem.business_id title=$aItem.name}" title="{$aItem.name|clean}">
                                {$aItem.name|clean|shorten:35:'...'}
                            </a>
                        </td>

                        <td class="t_center">
                            {if $aItem.category_title}
                                {if Phpfox::isPhrase($this->_aVars['aItem']['category_title'])}
                                    {phrase var=$aItem.category_title}
                                {else}
                                    {$aItem.category_title|convert|clean|shorten:25:'...'}
                                {/if}
                            {/if}
                        </td>

                        <td class="t_center">
                            {$aItem.creator_data|user}
                        </td>

                        <td class="t_center">
                            {if $aItem.type == 'claiming'}
                                {_p var='N/A'}
                            {else}
                                {$aItem|user}
                            {/if}
                        </td>

                        <td class="t_center">
                            {if $aItem.type == 'claiming'}
                                {phrase var='pending_for_claiming'}
                            {elseif $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.approved')
                            || $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.running')
                            }
                                {phrase var='published'}
                            {else}
                                {$aItem.business_phrase_status}
                            {/if}
                        </td>

                        <td id ="item_update_featured_{$aItem.business_id}" class="t_center w60">
                            {if  $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.approved')
                            || $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.running')
                            }

                            <div class="js_item_is_active" style="{if !$aItem.featured}display:none;{/if}">
                                <a href="javascript:void(0);"
                                   onclick="managebusiness.confirmFeaturedBackEnd({$aItem.business_id},1,{$aItem.is_unlimited},'{$aItem.expired_date}');"
                                   class="js_item_active_link" title="{phrase var='deactivate'}"></a>
                            </div>
                            <div class="js_item_is_not_active" style="{if $aItem.featured}display:none;{/if}">
                                <a href="javascript:void(0);" class="js_item_active_link"
                                   onclick="managebusiness.confirmFeaturedBackEnd({$aItem.business_id},0,{$aItem.is_unlimited},'{$aItem.expired_date}');"
                                   title="{phrase var='activate'}"></a>
                            </div>
                            {/if}
                        </td>

                        <td class="t_center">
                            {if $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.completed')
                            || $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.approved')
                            || $aItem.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.running')
                            }
                                {$aItem.package_name|shorten:35:'...'}
                            {else}
                            &nbsp;
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                </table>
                <?php if ($this->getLayout('pager')): ?>
                <div class="panel-footer">
                    {pager}
                </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
{else}
    <div class="alert alert-info">
        {phrase var='no_businesses_found'}
    </div>
{/if}
