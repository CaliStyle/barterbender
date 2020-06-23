<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if phpfox::isUser()}
    <ul class="yn_ul_profile_favorite" style="display:none;">
        <li class="yn_profile_favorite">
            <a href="#" onclick="$('#js_favorite_link_unlike_{$iItemId}').show(); $('#js_favorite_link_like_{$iItemId}').attr('style','display:none !important'); $.ajaxCall('foxfavorite.addFavorite', 'type={$sModule}&amp;id={$iItemId}', 'GET'); return false;" class="favor inlinePopup btn btn-round btn-default btn-icon" id="js_favorite_link_like_{$iItemId}" title="{phrase var='foxfavorite.add_to_your_favorites'}" {if $bIsAlreadyFavorite} style="display:none !important;"{/if}>
                    <i class="fa fa-star"></i>
                    <span>{phrase var='foxfavorite.favorite'}</span>
            </a>
        </li>
        <li class="yn_profile_unfavorite">
            <a class="unfavor btn btn-round btn-icon btn-default" title="{phrase var='foxfavorite.remove_from_your_favorite'}" href="#" onclick="$('#js_favorite_link_like_{$iItemId}').show(); $('#js_favorite_link_unlike_{$iItemId}').attr('style','display:none !important'); $.ajaxCall('foxfavorite.deleteFavorite', 'type={$sModule}&amp;id={$iItemId}', 'GET'); return false;" id="js_favorite_link_unlike_{$iItemId}" {if !$bIsAlreadyFavorite} style="display:none !important;"{/if}>
            <i class="fa fa-star-o"></i>
                    <span>{phrase var='foxfavorite.unfavorite'}</span>
            </a>
        </li>
    </ul>
    {literal}
    <script type="text/javascript" language="javascript">
        var addthem = true;
        $Behavior.onCreateFavoriteButton = function() {
            //alert("hello");
            if ($('#page_profile_index').length)
            {
                if (addthem)
                {
                    $bt_favor = $('.yn_profile_favorite').first();
                    $bt_unfavor = $('.yn_profile_unfavorite').first();
                    $str =  $bt_unfavor.html() + $bt_favor.html() ;
                    if ($('.profile_viewer_actions').length) {
                        $('.profile_viewer_actions').prepend($str);
                    } else {
                        $('.profile-viewer-actions').prepend($str);
                    }
                    $Behavior.inlinePopup();
                    addthem = false;
                }
                $('.yn_ul_profile_favorite').remove();
            }
        };
    </script>
    {/literal}
{/if}