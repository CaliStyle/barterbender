<?php
/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK, TienNPL
 * @package        Module_Coupon
 * @version        3.01
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<!-- Filter Search Form Layout -->
<form class="ynfr" method="post" action="{url link='admincp.coupon.coupon'}">
    <!-- Form Header -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var="search_filter"}
            </div>
        </div>
        <div class="panel-body">
            <!-- Coupon Name-->
            <div class="form-group">
                <label for="">{phrase var="coupon_name"}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title" size="50" />
            </div>
            <!-- Coupon Username -->
            <div class="form-group">
                <label for="">{phrase var="username"}:</label>
                <input class="form-control" type="text" name="search[username]" value="{value type='input' id='username'}" id="username" size="50" />
            </div>
            <!-- Category -->
            <div class="form-group">
                <label for="">{phrase var="category"}:</label>
                <select class="form-control" name="search[category_id]" id="category_id">
                    <option value="0">{phrase var="all"}</option>
                    {$aCategoryOptions}
                </select>
            </div>
            <!-- Location -->
            <div class="form-group">
                <label for="">{phrase var="location"}:</label>
                <select class="form-control" name="search[country_iso]" id="country_iso">
                    <option value="">{phrase var="any"}</option>
                    {foreach from = $aCountries item = aCountry}
                    <option value="{$aCountry.country_iso}" {if isset($aForms.country_iso) and $aForms.country_iso == $aCountry.country_iso}selected{/if}>{$aCountry.country_iso|location}</optioon>
                    {/foreach}
                </select>
            </div>
            <!-- Status -->
            <div class="form-group">
                <label for="">{phrase var="status"}:</label>
                <select class="form-control" name="search[status]">
                    <option value="">{phrase var="all"}</option>
                    <option value="draft" {value type='select' id='status' default = draft}>{phrase var="draft"}</option>
                    <option value="running" {value type='select' id='status' default = running}>{phrase var="running"}</option>
                    <option value="pending" {value type='select' id='status' default = pending}>{phrase var="pending"}</option>
                    <option value="upcoming" {value type='select' id='status' default = upcoming}>{phrase var="upcoming"}</option>
                    <option value="pause" {value type='select' id='status' default = pause}>{phrase var="paused"}</option>
                    <option value="endingsoon" {value type='select' id='status' default = endingsoon}>{phrase var="ending_soon"}</option>
                    <option value="closed" {value type='select' id='status' default = closed}>{phrase var="closed"}</option>
                    <option value="denied" {value type='select' id='status' default = denied}>{phrase var="denied"}</option>
                </select>
            </div>
            <!-- Feature -->
            <div class="form-group">
                <label for="">{phrase var="featured"}:</label>
                <select class="form-control" name="search[feature]">
                    <option value="all" {value type='select' id='feature' default = all}>{phrase var="all"}</option>
                    <option value="1"  {value type='select' id='feature' default = 1}>{phrase var="featured"}</option>
                    <option value="2"  {value type='select' id='feature' default = 2}>{phrase var="unfeatured"}</option>
                </select>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" id="filter_submit" name="search[submit]" value="{phrase var='search'}" class="btn btn-primary" />
            <input type="submit" id="filter_submit" name="search[reset]" value="{phrase var='reset'}" class="btn btn-default" />
        </div>
    </div>
</form>
<br/>

<!-- Coupon Listing Space -->
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var="coupon_listing"}
        </div>
    </div>

    {if count($aCoupons) >0}
	<form action="{url link='current'}" method="post" id="coupon_list" >
        <div class="panel-body">
        <div class="table-responsive flex-sortable">
		    <table class="table table-bordered">
                <!-- Table rows header -->
                <thead>
                    <tr>
                        <th class="table_row_header"><input type="checkbox" onclick="coupon.checkAllCoupon();" id="coupon_list_check_all" name="coupon_list_check_all"/></th>
                        <th class="table_row_header"></th>
                        <th>{phrase var='coupon_name'}</th>
                        <th class="table_row_header">{phrase var='username'}</th>
                        <th class="table_row_header">{phrase var='category'}</th>
                        <th class="table_row_header">{phrase var='location'}</th>
                        <th class="table_row_header">{phrase var='start_date'}</th>
                        <th class="table_row_header">{phrase var='end_date'}</th>
                        <th class="table_row_header">{phrase var='status'}</th>
                        <th class="table_row_header">{phrase var='featured'}</th>
                    </tr>
                </thead>
                <!-- Coupon Rows -->
                <tbody>
                    { foreach from=$aCoupons key=iKey item=aCoupon }
                    <tr id="coupon_{$aCoupon.coupon_id}" class="coupon_row {if $iKey%2 == 0 } coupon_row_even_background{else} coupon_row_odd_background{/if}">
                        <!-- Check Box -->
                        <td class="t_center">
                            <input type = "checkbox" class="coupon_row_checkbox" id="coupon_{$aCoupon.coupon_id}" name="coupon_row[]" value="{$aCoupon.coupon_id}" onclick="coupon.checkDisableStatus();"/>
                        </td>
                        <!-- Options -->
                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title="Options">  </a>
                            <div class="link_menu">
                                <ul>
                                    {if !$aCoupon.is_closed}
                                    <li><a href="{$aCoupon.edit_url}">{phrase var='admincp.edit'}</a></li>
                                    {/if}
                                    <li><a href="javascript:void(0);" onclick="return coupon.deleteCoupon('{$aCoupon.coupon_id}');">{phrase var='admincp.delete'}</a></li>
                                    <!-- Approve/Deny feature -->
                                    {if !$aCoupon.is_draft and !$aCoupon.is_approved}
                                        <li><a href="javascript:void(0);" onclick="return coupon.approveCoupon('{$aCoupon.coupon_id}');">{phrase var='approve'}</a></li>
                                        <li><a href="javascript:void(0);" onclick="return coupon.denyCoupon('{$aCoupon.coupon_id}');">{phrase var='deny'}</a></li>
                                    {/if}
                                    <!-- Pause/Resume feature -->
                                    {if !$aCoupon.is_draft and $aCoupon.is_approved and !$aCoupon.is_closed}
                                        {if $aCoupon.status == 4}
                                            <li><a href="javascript:void(0);" onclick="return coupon.resumeCoupon('{$aCoupon.coupon_id}');">{phrase var='resume'}</a></li>
                                        {else}
                                            <li><a href="javascript:void(0);" onclick="return coupon.pauseCoupon('{$aCoupon.coupon_id}');">{phrase var='pause'}</a></li>
                                        {/if}
                                    {/if}
                                    <!-- Close feature -->
                                    {if !$aCoupon.is_draft and $aCoupon.is_approved and !$aCoupon.is_closed}
                                        <li><a href="javascript:void(0);" onclick="return coupon.closeCoupon('{$aCoupon.coupon_id}');">{phrase var='close'}</a></li>
                                    {/if}
                                </ul>
                            </div>
                        </td>
                        <!-- Coupon title -->
                        <td>
                            <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}">
                                {$aCoupon.title|shorten:35:'...'}
                            </a>
                        </td>
                        <!-- Coupon username -->
                        <td class="t_center">
                            {$aCoupon|user}
                        </td>
                        <!-- Coupon category -->
                        <td class="t_center">
                            {if $aCoupon.category_name}
                                {if Phpfox::isPhrase($this->_aVars['aCoupon']['category_name'])}
                                    {phrase var=$aCoupon.category_name}
                                {else}
                                    {$aCoupon.category_name|convert|clean|shorten:25:'...'}
                                {/if}
                            {else}
                                {phrase var="none"}
                            {/if}
                        </td>
                        <!-- Coupon location -->
                        <td class="t_center">
                            {if $aCoupon.country_iso}
                                {$aCoupon.country_iso|location|shorten:25:'...'}
                            {else}
                                {phrase var="none"}
                            {/if}
                        </td>
                        <!-- Coupon start date -->
                        <td class="t_center">
                            {$aCoupon.start_time|date:'core.global_update_time'}
                        </td>
                        <!-- Coupon end date -->
                        <td class="t_center">
                            {$aCoupon.end_time|date:'core.global_update_time'}
                        </td>
                        <!-- Coupon status -->
                        <td class="t_center">
                            {if $aCoupon.status == 1}
                                {phrase var="running"}
                            {elseif $aCoupon.status == 2}
                                {phrase var="upcoming"}
                            {elseif $aCoupon.status == 3}
                                {phrase var="pending"}
                            {elseif $aCoupon.status == 4}
                                {phrase var="paused"}
                            {elseif $aCoupon.status == 5}
                                {phrase var="endingsoon"}
                            {elseif $aCoupon.status == 6}
                                {phrase var="closed"}
                            {elseif $aCoupon.status == 7}
                                {phrase var="draft"}
                            {elseif $aCoupon.status == 8}
                                {phrase var="denied"}
                            {else}
                                -
                            {/if}
                        </td>
                        <!-- Coupon featured -->
                        <td id ="item_update_featured_{$aCoupon.coupon_id}" align="center">
                            {if !$aCoupon.is_draft and $aCoupon.status != 0}
                            <a href="javascript:void(0);"
                               onclick="coupon.updateFeatured({$aCoupon.coupon_id}, {$aCoupon.is_featured})">
                                <div style="width:50px;">
                                    {if $aCoupon.is_featured }
                                        {img theme='misc/bullet_green.png' alt=''}
                                    {else}
                                        {img theme='misc/bullet_red.png' alt=''}
                                    {/if}
                                </div>
                            </a>
                            {else}
                            -
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
		    </table>
        </div>
        <div class="form-group">
            {pager}
        </div>
        </div>
		<!-- Delete selected button -->
		<div class="panel-footer t_right">
			<input type="submit" name="val[approve_selected]" id="approve_selected" disabled value="{phrase var='approve_selected'}" class="sJsConfirm approve_selected btn btn-primary disabled" />
	        <input type="submit" name="val[delete_selected]" id="delete_selected" disabled value="{phrase var='delete_selected'}" class="sJsConfirm delete_selected btn btn-primary disabled" />
	    </div>
	</form>
{else}
	<div class="extra_info" style="margin-left: 15px;">
		{phrase var="no_coupons_found"}
	</div>
{/if}
</div>
