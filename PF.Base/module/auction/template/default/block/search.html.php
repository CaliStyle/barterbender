<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 *
 *
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>

{literal}
<script type="text/javascript">

    $Core.remakePostUrl = function(){

        var keyword = $("#search_keyword").val();
        var category = $("select.js_mp_category_list option:selected");
        var sort = $("#search_sort").val();
        var categoryUrl = [];
        $(category).each(function(index){
            if($(this).val() != ''){
                categoryUrl.push($(this).val());
            }
        });

        categoryUrl = categoryUrl.join(",");
        var url = window.location.href;

        if(url.match(/\/advsearch_.*?\//g))
        {

        }
        else
        {
{/literal}
    {if $sFullControllerName == 'auction.index'}
            url = url.replace(/\/auction\//g, '/auction/advsearch_true/');
    {/if}
    {if $sFullControllerName == 'auction.my-bids'}
            url = url.replace(/\/my-bids\//g, '/my-bids/advsearch_true/');
    {/if}
    {if $sFullControllerName == 'auction.my-offers'}
            url = url.replace(/\/my-offers\//g, '/my-offers/advsearch_true/');
    {/if}
    {if $sFullControllerName == 'auction.didnt-win'}
            url = url.replace(/\/didnt-win\//g, '/didnt-win/advsearch_true/');
    {/if}
    {if $sFullControllerName == 'auction.my-won-bids'}
            url = url.replace(/\/my-won-bids\//g, '/my-won-bids/advsearch_true/');
    {/if}
{literal}
        }

        if(url.match(/\/keyword_.*?\//g))
        {
            url = url.replace(/\/keyword_.*?\//g, '/keyword_'+keyword+'/');
        }
        else
        {
            url += 'keyword_'+keyword+'/';
        }


        if(url.match(/\/category_.*?\//g))
        {
            url = url.replace(/\/category_.*?\//g, '/category_'+categoryUrl+'/');
        }
        else
        {
            url += 'category_'+categoryUrl+'/';
        }

        if(url.match(/\/sort_.*?\//g))
        {
            url = url.replace(/\/sort_.*?\//g, '/sort_'+sort+'/');
        }
        else
        {
            url += 'sort_'+sort+'/';
        }

        $("#ynauction_advsearch").attr('action', url);
    }

</script>
{/literal}

<div class="ynfe adv-search-block clear-bg-padding" id ="ynauction_adv_search" {if !isset($aForms.advancedsearch)} style="display: none;" {/if}>
    <form id="ynauction_advsearch" method="post" onsubmit="$Core.remakePostUrl(); if($('#search_keywords').val()=='{phrase var='keywords'}...'){l}$('#search_keywords').val('');{r}">
        <div class="form-group content">
            <input type="hidden" value="1" name="search[submit]">
            <input type="hidden" name="search[advsearch]" value="1" />
            <input type="hidden" id="form_flag" name="search[form_flag]" value="0">

            <div class="form-group">
                <div class="lb-keyword"><label for="search_category">{phrase var='keyword'}:</label></div>
                <div class="cw-keyword">
                    <input id="search_keyword" value="{value type='input' id='keyword'}" type="text" name="search[keyword]" class="search_keyword form-control">
                </div>
            </div>
            <div class="form-group">
                    <div class="lb-category">
                        <label for="search_category">{phrase var='category'}:</label>
                    </div>
                    <div class="cw-category">
                        {$sCategories}
                    </div>
            </div>
            <div class="form-group">
                   <div class="lb-sort"><label for="search_sort">{phrase var='sort'}:</label></div>
                   <div class="cw-sort">
                       <select id="search_sort" name="search[sort]" class="form-control">
                           <option value="">{phrase var='select'}:</option>
                           <option value="top-orders" {value type='select' id='sort' default='top-orders'}>{phrase var='top_orders'}</option>
                           <option value="newest" {value type='select' id='sort' default='newest'}>{phrase var='newest'}</option>
                           <option value="oldest" {value type='select' id='sort' default='oldest'}>{phrase var='oldest'}</option>
                           <option value="a-z" {value type='select' id='sort' default='a-z'}>{phrase var='a_z'}</option>
                           <option value="z-a" {value type='select' id='sort' default='z-a'}>{phrase var='z_a'}</option>
                           <option value="most-liked" {value type='select' id='sort' default='most-liked'}>{phrase var='most_liked'}</option>
                       </select>
                   </div>
            </div>
            <div class="form-group">
                <div class="cw-cmd">
                    <button name="search[submit]" class="btn btn-sm btn-primary" type="submit">{phrase var='search'}</button>
                    {if $sFullControllerName == 'auction.index'}
                    <a href="{url link='auction'}" class="btn btn-sm btn-default">{phrase var='reset'}</a>
                    {else}
                    <a href="{url link=$sFullControllerName}" class="btn btn-sm btn-default">{phrase var='reset'}</a>
                    {/if}
                </div>
            </div>
        </div>
    </form>
</div> 