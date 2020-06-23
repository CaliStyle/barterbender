<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{literal}
<style type="text/css">
    .description_content ul li
    {
        list-style: square inside none;
    }
    .description_content ol li
    {
        list-style: decimal inside none;
    }
    .description_content li
    {
        list-style: disc inside none;
    }
</style>
{/literal}

{literal}
<script type="text/javascript">
    $Behavior.initManageArticle = function() {
        $("select[name=\"search[language]\"]").change(function() {
            $.ajaxCall('gettingstarted.getCategoryForSearchArticle', 'language_id=' + this.value, 'GET'); return false;
        });
    }
</script>
{/literal}
<form method="get" accept-charset="utf-8"  action="{url link='admincp.gettingstarted.managearticle'}search">
    <div class="panel panel-default">
        <div class="panel-heading">{phrase var ='gettingstarted.search_filters'}</div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {phrase var='gettingstarted.keyword'}
                </label>
                {$aFilters.title}
            </div>
             <div class="form-group">
                <label>
                    {phrase var='gettingstarted.language'}:
                </label>
                 {$aFilters.language id='language'}
            </div>

            <input type="hidden" value="{$language_hidden}" id ="language_hidden" name="language_hidden"/>
            <div class="form-group" >
                <label>
                    {phrase var='gettingstarted.category'}:
                </label>
                {$aFilters.type}
            </div>
            <div class="form-group">
                <label>
                    {phrase var='gettingstarted.featured'}:
                </label>
                {$aFilters.featured}
            </div>
        </div>

        <div class="panel-footer">
            <input type="hidden" value="search_" name="se"/>
            <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="button" onclick="window.location.href='{url link='admincp.gettingstarted.managearticle'}'; return false;"  name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-default" />
        </div>
    </div>
</form>
{if count($aCategories)}

<form id="form1" method="post" action="{url link='admincp.gettingstarted.managearticle'}" style="margin-top:30px;">
    <div class="panel panel-default">
        <div class="panel-heading">
            {phrase var='gettingstarted.knowledge_base_article'}
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="width:8px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                    <th style="width:170px;">{phrase var='gettingstarted.title'}</th>
                    <th style="width:320px;">{phrase var='gettingstarted.description'}</th>
                    <th>{phrase var='gettingstarted.language'}</th>
                    <th>{phrase var='gettingstarted.category'}</th>
                    <th>{phrase var='gettingstarted.edit'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aCategories key=iKey item=aCategory}
                <tr id="js_row{$aCategory.article_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td><input type="checkbox" name="id[]" class="checkbox" value="{$aCategory.article_id}->{$aCategory.language_id}" id="js_id_row{$aCategory.article_id}" /></td>
                    <td>{$aCategory.title}</td>
                    <td class="description_content item_view_content">{$aCategory.description_parsed|shorten:300:'Expand':true}</td>
                    <td>{$aCategory.language_title}</td>
                    <td>{$aCategory.article_category_name|convert|clean}</td>
                    <td style="width: 45px;"><a href="{permalink module='admincp.gettingstarted.addarticle' id=$aCategory.article_id title=$aCategory.language_id}">{phrase var='gettingstarted.edit'}</a></td>
                </tr>
                {/foreach}
            </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <div class="table_bottom">
                <input type="submit" name="delete" value="{phrase var='gettingstarted.delete_selected'}" class="sJsConfirm delete btn btn-danger sJsCheckBoxButton disabled" disabled="true" />
            </div>
        </div>
        {else}
            {if $bIsSearch}
                <div class="error-message">{phrase var='gettingstarted.no_search_results_found'}</div>
            {else}
            <div class="p_4">
                {phrase var='gettingstarted.no_knowledge_base_articles_have_been_created'}<a href="{url link='admincp.gettingstarted.addarticle'}">{phrase var='gettingstarted.create_one_now'}</a>
            </div>
            {/if}
        {/if}
        <input type="hidden" value="{$language_hidden}" id ="language_hidden" name="language_hidden"/>
    </div>
</form>

{pager}
