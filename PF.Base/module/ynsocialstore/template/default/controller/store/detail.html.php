<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/6/16
 * Time: 11:05 AM
 */
?>
{if !empty($sError)}
    {$sError}
{else}
    {if $iPage <= 1}
        <div id="ynsocialstore_detailstore">
            <div class="ynsocialstore-hiddenblock">
                <input type="hidden" value="detailstore" id="ynsocialstore_pagename" name="ynsocialstore_pagename">
            </div>
        </div>
    {/if}
    {if !empty($firstpage)}
        {module name='ynsocialstore.store.detail'.$firstpage}

        {if $aStore.theme_id == 1}
        {literal}
        <script type="text/javascript">
            $Behavior.ynstoreScrollToPage = function () {
                var cover_block_ele = $('#js_block_border_ynsocialstore_store_detailcover');
                if (cover_block_ele.length) {
                    $("html, body").animate({
                            scrollTop: cover_block_ele.height() / 2 + cover_block_ele.offset().top
                    }, 400);
                    $Behavior.ynstoreScrollToPage = function () {}
                }
            }
        </script>
        {/literal}
        {/if}
    {/if}

    {if $aStore.theme_id == 2}
    {literal}
    <script type="text/javascript">
        $Behavior.ynstoreScrollToPageTheme2 = function () {
            var cover_block_ele_2 = $('#js_block_border_ynsocialstore_store_detailcover-2');
            if (cover_block_ele_2.length) {
                $("html, body").animate({
                    scrollTop: cover_block_ele_2.height() / 2 + (cover_block_ele_2.offset().top) / 2
                }, 400);
                $Behavior.ynstoreScrollToPageTheme2 = function () {}
            }
        }
    </script>
    {/literal}
    {/if}
<input type="hidden" name="ynsocialstore_detail_store_id" id="ynsocialstore_detail_store_id" value="{$aStore.store_id}">
{/if}

