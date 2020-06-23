<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 5:50 PM
 */
?>
{if !empty($sError)}
    {$sError}
{else}
    <input type="hidden" id="ynsocialstore_pagename" value="detailproduct">
    {if $iPage <= 1}
    <div class="ynstore_product_detail_checkout">
        {module name='ynsocialstore.product.attributes-detail'}
    </div>

    <div class="ynstore-actions-social-block">
        <div class="ynstore-store-detail-block">
            <div class="ynstore-actions-social">
                {addthis url=$aItem.bookmark_url title=$aItem.name}

                <div class="ynstore-embedcode dropdown">
                    <a data-caption="HTML Code" onclick="$(this).parent('div').toggleClass('open'); if($('.ynstore_store_html_code_block textarea').length){l} $('.ynstore_store_html_code_block textarea').get(0).select();{r}" title="HTML Code" class="btn btn-default">
                        <i class="fa fa-code"></i>
                        {_p('Embed')}
                        <i class="fa fa-angle-down"></i>
                    </a>

                    <div class="dropdown-menu ynstore_store_html_code_block">
                        <textarea id="ynstore_html_code_value" readony class="form-control disabled"><iframe width="500" height="550" src="{$sUrl}"></iframe></textarea>

                        <div class="text-right">
                           <button type="button" onclick="$(this).parents('.ynstore-embedcode').toggleClass('open');" class="btn btn-sm btn-default">
                              {_p var='close'}
                           </button>
                            <button type="button" class="yns-copy-btn btn btn-sm btn-primary" onclick="ynsocialstore.copy_embed_code(this)" data-clipboard-target="#ynstore_html_code_value">
                                {_p('Copy code')}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="ynstore-product-review-block">
        <div class="page_section_menu page_section_menu_header">
            <ul class="nav nav-tabs nav-justified" id="ynstore_tab">
                <li class=" {if !$bIsReviewTab}active{/if}">
                    <a href="" id="ynstore_product_overview">
                        {_p var='ynsocialstore.overview'}
                    </a>
                </li>

                <li class =" {if $bIsReviewTab}active{/if}">
                    <a href="" id="ynstore_product_reviews">
                        {_p var='ynsocialstore.reviews'} ({$aItem.total_review})
                    </a>
                </li>
            </ul>
        </div>

        <div id="ynstore_product_overview-content" class="{if $bIsReviewTab}hide{/if}">
            {module name='ynsocialstore.product.detail-overview'}
        </div>
        <div id="ynstore_product_reviews-content" class="{if !$bIsReviewTab}hide{/if}">
            {module name='ynsocialstore.product.detail-reviews'}
        </div>
    </div>

    <div class="ynstore_product_detail-comment">
        {module name='feed.comment'}
    </div>

    {literal}
    <script language="javascript" type="text/javascript">
        $Behavior.ynstoreProductViewDetail = function(){
            var fadeTTime = 100;
            $("#ynstore_product_overview").on('click',function(evt){
                evt.preventDefault();
                $("#ynstore_product_reviews-content").addClass('hide');
                $("#ynstore_product_overview-content").removeClass('hide');
                $("#ynstore_product_overview-content").stop(false, false).fadeIn(fadeTTime);
                $("#ynstore_tab").find(".active").removeClass("active");
                $(this).parent().addClass("active");
                return false;
            });
            $("#ynstore_product_reviews").on('click',function(evt){
                evt.preventDefault();
                $("#ynstore_product_overview-content").addClass('hide');
                $("#ynstore_product_reviews-content").removeClass('hide');
                $("#ynstore_product_reviews-content").stop(false, false).fadeIn(fadeTTime);
                $("#ynstore_tab").find(".active").removeClass("active");
                $(this).parent().addClass("active");
                return false;
            });
            {/literal}
            {if $bIsReviewTab}
                if($('.page_section_menu.page_section_menu_header').length){l}
                    setTimeout(function(){l}
                        $('html, body').animate({l}
                            scrollTop: $('.page_section_menu.page_section_menu_header').offset().top - 100
                        {r}, 400);
                    {r},100);
                {r}
            {/if}
            {literal}
        };
    </script>
    {/literal}
    {/if}
{/if}
