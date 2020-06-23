<?php
/**
 * @copyright   [YouNet_COPYRIGHT]
 * @author      YouNet Company
 * @package     Module_GettingStarted
 * @version     3.02p5
 */
defined('PHPFOX') or exit('NO DICE!');
?>


{$sCreateJs}
<form id="js_form" name="js_form" method="post" enctype="multipart/form-data" action="" onsubmit="{$sGetJsForm}">
    <div class="panel panel-default">
        <div class="panel-heading">
            {if !$bIsEdit}{phrase var='gettingstarted.add_knowledge_base_category'}{else}{phrase var='gettingstarted.edit_knowledge_base_category'}{/if}
        </div>

        {if $bIsEdit}
            <input type="hidden" name="article_category_id" value="{$aForms.article_category_id}" />
        {/if}
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {required}{phrase var='gettingstarted.language'}:
                </label>
                <select id="language_id" class="form-control" name="val[language_id]" onchange="$.ajaxCall('gettingstarted.getCatSelectByLanguage', 'language_id='+this.value+'&name=val[parent_id]'{if $bIsEdit}+'&selected={$aForms.parent_id}&reduced={$aForms.article_category_id}'{/if}, 'GET'); return false;" >
                    {foreach from=$aLanguages item=aLanguage}
                    <option value="{$aLanguage.language_id}"{if $bIsEdit && $aLanguage.language_id==$aForms.language_id} selected="selected"{/if}>{$aLanguage.title}</option>
                    {/foreach}
                </select>
                <span id="loading"></span>
            </div>

            <div class="form-group">
                <label>
                    {phrase var="gettingstarted.parent_category"}:
                </label>
                <div id="categories_bylanguage">
                    {$aCats}
                    <span id="loading"></span>
                </div>
            </div>

            <div class="form-group">
                <label>
                    {required}{phrase var="gettingstarted.name"}:
                </label>
                <input type="text" class="form-control" id="article_category_name" name="val[article_category_name]" value="{value type='input' id='article_category_name'}" size="50"/>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" id="add" name="{if $bIsEdit}update{else}Add{/if}" value="{if $bIsEdit}Save{else}Add{/if}" class="btn btn-primary" />
        </div>
    </div>
</form>


