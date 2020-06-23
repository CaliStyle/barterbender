<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/4/16
 * Time: 11:37
 */
?>
{foreach from=$aFiles item=file}
{if !empty($file)}
<script type="text/javascript" src="{$file}"></script>
{/if}
{/foreach}
{literal}
<script type="text/javascript">
    $(document).ready(function() {
        window.onload = function () {
            window.print();
            setTimeout(function(){window.close();}, 1);
        }
    });

</script>
{/literal}

{literal}
<style>

body{
    padding: 0;
    font-family: Arial;
}
a{
    text-decoration: none;
    color: #297fc7;
}
/*Product Embed*/
.ynstore-product-detail-block.ynstore-product-embed{
    width: 420px;
    border: 1px solid #ddd;
    padding: 15px 20px;
}
.ynstore-product-detail-block.ynstore-product-embed *{
    box-sizing: border-box;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-title {
  font-size: 22px;
  font-weight: 700;
  color: #555;
  line-height: 26px;
  margin-bottom: 7px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-title .ynstore-featured {
  font-size: 11px;
  color: #FFF;
  background-color: #ffa800;
  padding: 4px 6px;
  border-radius: 3px;
  font-weight: normal;
  text-transform: uppercase;
  display: inline-flex;
  justify-content: center;
  align-items: center;
  height: 22px;
  position: relative;
  top: -4px;
  border: 1px solid #f19d25;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-title .ynstore-featured .ico {
  font-size: 11px;
  margin-right: 5px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-timestamp-from {
  color: #999;
  margin-bottom: 5px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-timestamp-from span.ynstore-from a {
  font-weight: 700;
  color: #555;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-timestamp-from span.ynstore-from a:hover {
  color: #297fc7;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-location {
  text-transform: capitalize;
  margin-bottom: 15px;
  color: #999;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info {
  position: relative;
  align-items: center;
  margin-bottom: 15px;
  overflow: hidden;
}
.ynstore-price-old{
    color: #999;
    text-decoration: line-through;
}

.ynstore-product-detail-block.ynstore-product-embed .ynstore-img img{
    max-width: 100%;
    max-height: 500px;
    margin-bottom: 15px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-ratings-reviews-block{
    display: flex;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-ratings-reviews-block .ynstore-review-count{
    margin-left: 10px;
    text-transform: lowercase;
    border: 1px solid #297fc7;
    border-radius: 3px;
    font-size: 11px;
    padding: 1px 6px;
    margin-top: 1px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block {
  float: left;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-discount-block {
  overflow: hidden;
  margin: -1px;
  float: left;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-discount-block .ynstore-discount {
  height: 62px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #ffa800;
  border: 1px solid #ffa800;
  font-size: 20px;
  font-weight: 700;
  flex-direction: column;
  padding-left: 12.5px;
  position: relative;
  line-height: 24px;
  margin-right: 5px;
  padding-right: 12.5px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-meta-block {
  overflow: hidden;
  min-height: 60px;
  display: flex;
  align-items: left;
  justify-content: space-between;
  padding-right: 15px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-price-block {
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-price-block .ynstore-price {
  font-size: 20px;
  font-weight: 700;
  color: #555;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-price-block .ynstore-price span {
  text-transform: lowercase;
  font-weight: 400;
  font-size: 14px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-price-block .ynstore-price span i {
  font-size: 18px;
}
@media (max-width: 600px) {
  .ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block {
    position: static;
    width: 100%;
    text-align: center;
    text-align: left;
  }
  .ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block:before,
  .ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block:after {
    display: none;
  }
  .ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-price-block {
    margin-bottom: 5px;
  }
  .ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-price-block .ynstore-meta-block {
    padding: 10px 15px;
    flex-direction: column;
  }
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic {
  display: flex;
  padding: 5px 0;
  float: right;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  padding-left: 15px;
  margin-left: 20px;
  border-left: 1px solid #ddd;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item:first-of-type {
  border-left: 0;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item span {
  font-weight: 700;
  display: block;
  font-size: 18px;
  color: #555;
  margin-bottom: 3px;
  line-height: 18px;
  text-align: right;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item .ico {
  position: relative;
  top: 1px;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item.ynstore-hover {
  cursor: pointer;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item.ynstore-hover:hover {
  color: #297fc7;
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item.ynstore-hover:hover span {
  color: #297fc7;
}
@media (max-width: 600px) {
  .ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic {
    margin: auto;
  }
  .ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item {
    margin-left: 10px;
  }
  .ynstore-product-detail-block.ynstore-product-embed .ynstore-info .ynstore-product-statistic .ynstore-statistic-item:first-of-type {
    margin-left: 0;
    padding-left: 0;
  }
}
.ynstore-product-detail-block.ynstore-product-embed .ynstore-ratings-btn-block {
  display: flex;
  justify-content: space-between;
  flex-flow: wrap;
  margin-bottom: 10px;
}
.yn-rating .ico {
  color: #ffa800;
  margin-left: 2px;
}
.yn-rating .ico.hover {
  color: #ffa800!important;
}
.yn-rating .ico.yn-rating-disable {
  color: #ccc;
}
.yn-rating.yn-rating-small .ico {
  font-size: 12px;
}
.yn-rating.yn-rating-normal .ico {
  font-size: 16px;
}
.yn-rating.yn-rating-large .ico {
  font-size: 24px;
}

</style>
{/literal}

<div class="ynstore-product-detail-block ynstore-product-embed">
    <div class="ynstore-title">
        <span style="{if empty($aItem.is_featured)} display: none; {/if}" title="{_p('Featured Product')}" class="ynstore-featured" id="ynstore_product_detail_feature">
            <i class="ico ico-diamond"></i>
            {_p('Feature')}
        </span>
        {$aItem.name|clean}
    </div>



    <div class="ynstore-timestamp-from">
        <span class="ynstore-timestamp">
            {$aItem.product_creation_datetime|date:'core.global_update_time'}
        </span>

        <span class="ynstore-from">
            {_p var='ynsocialstore.from'}
            <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}">{$aItem.store_name}</a>
        </span>
    </div>

    <div class="ynstore-location">
        {if !empty($aItem.location.address)}
            {_p var='ynsocialstore.at'} {$aItem.location.address}
        {/if}
    </div>
    
    <div class="ynstore-img">
        {if $aItem.logo_path}
            <img src="{img server_id=$aItem.server_id path='core.url_pic' file=$aItem.logo_path suffix='_400' return_url='true'}" alt="">
        {else}
            <img src="{param var='core.path_actual'}PF.Base/module/ynsocialstore/static/image/product_default.jpg" alt="">
        {/if}
    </div>

    <div class="ynstore-info">
        <div class="ynstore-product-price-block">
            {if $aItem.discount_percentage != 0 && $aItem.discount_price != 0 && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
            <div class="ynstore-discount-block" id="js_product_discount_percentage">
                <span class="ynstore-discount">
                    {$aItem.discount_percentage}%
                    <b>{_p var='ynsocialstore.off'}</b>
                </span>
            </div>
            {/if}

            <div class="ynstore-meta-block">
                <div class="ynstore-price-block">
                    <span class="ynstore-price">
                        <b id="js_product_discount_price">{if isset($aItem.currency_symbol)}{$aItem.currency_symbol}{/if}{$aItem.discount_display|number_format:2}</b>
                        {if isset($aItem.product_type) && $aItem.product_type =='physical'} 
                        <span>{if isset($aItem.product_type) && $aItem.product_type =='physical' && !empty($aItem.uom_title)} <i>/</i> {$aItem.uom_title|convert}{/if}</span>
                        {/if}
                    </span>
                    {if $aItem.discount_percentage != 0 && $aItem.discount_price != 0 && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
                    <span class="ynstore-price-old">{if isset($aItem.currency_symbol)}{$aItem.currency_symbol}{/if}{$aItem.product_price}</span>
                    {/if}
                </div>
            </div>
        </div>
    </div>
    <div class="ynstore-ratings-reviews-block">
        <div class="ynstore-rating yn-rating yn-rating-normal">
            <span class="rating">{$aItem.rating}</span>
            {for $i = 0; $i < 5; $i++}
            {if $i < (int)$aItem.rating}
            <i class="ico ico-star" aria-hidden="true"></i>
            {elseif ((round($aItem.rating) - $aItem.rating) > 0) && ($aItem.rating - $i) > 0}
            <i class="ico ico-star-half-o" aria-hidden="true"></i>
            {else}
            <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
            {/if}
            {/for}
        </div>

        <a href="javascript:void(0);" onclick="gotoReviewSection()" class="ynstore-review-count">
            {$aItem.total_review}&nbsp;{if $aItem.total_review == 1}{_p var='ynsocialstore.review'}{else}{_p var='ynsocialstore.reviews'}{/if}
        </a>
    </div>
    
</div>
