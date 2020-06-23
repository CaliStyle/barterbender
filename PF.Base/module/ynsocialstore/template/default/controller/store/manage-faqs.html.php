<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/18/16
 * Time: 21:28
 */
?>
{if !empty($sError)}
{$sError}
{else}
<div id='edit_faq' class="ynstore-manage-faqs">
    {if count($aFAQs)}
    <form method="post" action="{url link='ynsocialstore.manage-faqs.id_'.$iEditId'}" id="js_edit_faq"  enctype="multipart/form-data">
        <input type="hidden" name="val[store_id]" value="{$iEditId}" >
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{_p('FAQs')}</th>
                        <th>{_p('Show')}</th>
                        <th>{_p('Option')}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$aFAQs key=iKey item=iFAQ}
                    <tr>
                        <input type="hidden" name="val[faq][]" value="{$iFAQ.faq_id}">
                        <td>{$iFAQ.question|parse}</td>
                        <td align="center">{if $iFAQ.is_active}{_p var='ynsocialstore.yes'}{else}{_p var='ynsocialstore.no'}{/if}</td>
                        <td>
                            <a href="#" class="ynstore-action-btn" onclick="ynsocialstore.editFAQ({$iFAQ.faq_id},{$iEditId});">
                                <i class="ico ico-compose"></i>
                                {_p('Edit')}
                            </a>
                            <a href="#" class="ynstore-action-btn ynstore-delete" onclick="ynsocialstore.confirmDeleteFAQs({$iFAQ.faq_id},{$iEditId})">
                                <i class="ico ico-trash-alt"></i>
                                {_p var='ynsocialstore.delete'}
                            </a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <p class="ynstore-tips bg-info">
            {_p('No FAQs found.')}
        </p>
    {/if}
        <div>
            <a href="#" class="btn btn-default" onclick="tb_show('{_p('Add FAQ')}', $.ajaxBox('ynsocialstore.AddFaqStoreBlock', 'height=300&width=500&action=add&store_id='+{$iEditId})); return false;">{_p('Add FAQs')}</a>
        </div>
    </form>
</div>
{/if}
