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
<form method="get" action="{url link='admincp.socialpublishers.statisticdate'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='socialpublishers.search_filter'}
            </div>
        </div>
        <div class="panel-body">
            {*<div class="form-group">
                <label>
                    {_p var='socialpublishers.search_for_statistic_date'}
                </label>
                <div>
                    {select_date prefix='start_' start_year='2005' end_year='+10' field_separator=' / ' field_order='MDY' bUseDatepicker=false}
                    <div style="display: none;">{$aFilters.statistic_date}</div>
                </div>
            </div>*}
            <div class="form-group">
                <label>
                    {_p var='socialpublishers.limit_per_page'}
                </label>
                {$aFilters.display}
            </div>
            <div class="form-group">
                <label>
                    {_p var='socialpublishers.sort'}
                </label>
                {$aFilters.sort}
                <br/>
                {$aFilters.sort_by}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{_p var='core.submit'}" class="btn btn-primary" />
            <input type="submit" name="search[reset]" value="{_p var='core.reset'}" class="btn btn-default" />
        </div>
    </div>
</form>
{pager}
<form method="post" action="{url link='admincp.socialpublishers.statisticdate'}">
    {if count($aItems)}
    <div class="panel panel-default">
        <table class="table table-admin table-responsive">
            <tr>
                <th>{_p var='socialpublishers.id'}</th>
                <th>{_p var='socialpublishers.date'}</th>
                <th>{_p var='socialpublishers.facebook'}</th>
                <th>{_p var='socialpublishers.twitter'}</th>
                <th>{_p var='socialpublishers.linkedin'}</th>
            </tr>
            {foreach from=$aItems key=iKey item=aItem}
                <tr id="js_row{$aItem.id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td>{$aItem.id}</td>
                    <td>{$aItem.statistic_date|date:'core.global_update_time'}</td>
                    <td>{$aItem.total_facebook_post}</td>
                    <td>{$aItem.total_twitter_post}</td>
                    <td>{$aItem.total_linkedin_post}</td>
                </tr>
            {/foreach}
        </table>
    </div>
    {else}
    <div class="extra_info p_4">
        {_p var='socialpublishers.no_statistic_date_has_been_created'}
    </div>
    {/if}
</form>
{pager}