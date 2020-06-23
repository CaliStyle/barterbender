<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/14/16
 * Time: 9:24 AM
 */
?>

<div class="ynstore-store-faqs-block">
    <div class="ynstore-title">{_p var='ynsocialstore.faqs'}</div>

    {if count($aFAQs) > 0}
    <div class="ynstore-actions">
        <a href="javascript:void(0)" id="button_expand_collapse" data-value="1" onclick="expandCollapseAll()">{_p var='ynsocialstore.expand_all'}</a>
    </div>
    {/if}

    <div id="ynstore_store_detail_module_faq">
        {if count($aFAQs) < 1}
            <div class="extra_info">
                {_p var='ynsocialstore.no_item_s_found'}.
            </div>
        {/if}

        <div class="ynstore-faq-items">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                {foreach from=$aFAQs item=aFAQ}
                <div class="panel panel-default ynstore-faq-item">
                    <div class="panel-heading" role="tab" id="heading{$aFAQ.faq_id}">
                        <div class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#ynstore_faq_{$aFAQ.faq_id}" aria-expanded="false" aria-controls="ynstore_faq_{$aFAQ.faq_id}">
                                <i class="ico ico-caret-right"></i>
                                {$aFAQ.question}
                            </a>
                        </div>
                    </div>
                    <div id="ynstore_faq_{$aFAQ.faq_id}" class="panel-collapse fade collapse" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            {$aFAQ.answer|parse}
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>

{literal}
<script type="text/javascript">
    function expandCollapseAll() {
        var oItem = $('#button_expand_collapse');
        var is_collapsed = oItem.attr('data-value');

        if($('#ynstore_store_detail_module_faq').length > 0 && is_collapsed == '1'){
            $('.ynstore-faq-item .panel-collapse').addClass('in').css('height','auto');
            $('.ynstore-faq-item .panel-title a').attr('aria-expanded','true');
            oItem.html("{/literal}{_p var='ynsocialstore.collapse_all'}{literal}")
            oItem.attr('data-value', 0);

        } else if ($('#ynstore_store_detail_module_faq').length > 0 && is_collapsed == '0') {
            $('.ynstore-faq-item .panel-collapse').removeClass('in');
            $('.ynstore-faq-item .panel-title a').attr('aria-expanded','false');
            oItem.html("{/literal}{_p var='ynsocialstore.expand_all'}{literal}")
            oItem.attr('data-value', 1);
        }
    }
</script>
{/literal}
