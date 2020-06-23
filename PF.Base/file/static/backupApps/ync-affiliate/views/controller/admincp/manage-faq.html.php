<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 14:41
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<!------------------------------------------>
<!-- Filter Search Form Layout -->
<form method="post" enctype="multipart/form-data" action="">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
            {_p('Search Filter')}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="faq_question">
                    {_p('FAQ question')}:
                </label>
                <input class="form-control" id="faq_question" type="text" name="search[faq_question]" value="{if isset($sSearch)}{$sSearch}{/if}" id="faq_question" size="50"/>
            </div>
        </div>
        <!-- Submit Buttons -->
        <div class="panel-footer">
            <input type="submit" id="yn_filter_affiliate_submit" name="search[submit]" value="{_p('Submit')}" class="btn btn-primary"/>
            <input type="button" class="btn btn-default" id="yn_filter_affiliate_reset" value="{_p('Reset')}" onclick="window.location = '{url link='admincp.yncaffiliate.manage-faq'}'"/>
        </div>
    </div>
</form>
<hr>
<!------------------------------------------>
{if count($aItems) > 0}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='faqs'}
        </div>
    </div>
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" id="_sort" data-sort-url="{url link='yncaffiliate.admincp.faq.order'}">
            <thead>
                <tr>
                    <th class="w20"></th>
                    <th class="w20"></th>
                    <th>{_p('Question')}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aItems key=iKey item=aItem}
                <tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aItem.faq_id}">
                    <td class="w20 t_center">
                        <i class="fa fa-sort"></i>
                    </td>
                    <td class="w20 t_center">
                        <a href="#" class="js_drop_down_link" title="Manage"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a class="popup" href="{url link='admincp.yncaffiliate.add-faq' idFaq=$aItem.faq_id}">{_p var='Edit'}</a></li>
                                <li><a href="{url link='admincp.yncaffiliate.manage-faq' deleteFaq=$aItem.faq_id}" class="sJsConfirm">{_p var='Delete'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="td-flex">
                        {_p var=$aItem.question}
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
{else}
    <div>{_p('No FAQs Found')}</div>
{/if}
