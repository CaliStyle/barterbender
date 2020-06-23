<?php
if (!Phpfox::getParam('core.site_wide_ajax_browsing') && Phpfox::isUser() && (Phpfox::getUserBy('view_id') == 0))
{
    ?>
 
    <?php
    $sModule = 'advancedmarketplace';
    $iItemId = (defined('PHPFOX_IS_USER_PROFILE')) ? Phpfox::getLib('request')->get('req2') : Phpfox::getLib('request')->getInt('req3');
    
    $bIsViewItem = Phpfox::getService('foxfavorite')->isViewItem($sModule);
    $bIsAvailModule = Phpfox::getService('foxfavorite')->isAvailModule($sModule);
    $bIsAlreadyFavorite = Phpfox::getService('foxfavorite')->isAlreadyFavorite($sModule, $iItemId);

    if ($bIsViewItem && $bIsAvailModule && !Phpfox::isAdminPanel() && !defined('PHPFOX_IS_USER_PROFILE'))
    {
        ?>
        <div id="section_menu" style="position:static;margin-bottom:10px;overflow:hidden" class="yn_sectionmenu">
            <ul>
                <li  class="yn_page_favorite">
                    <a href="#" onclick="$('#js_favorite_link_unlike_<?php echo $iItemId; ?>').show(); $('#js_favorite_link_like_<?php echo $iItemId; ?>').hide(); $.ajaxCall('foxfavorite.addFavorite', 'type=<?php echo $sModule; ?>&amp;id=<?php echo $iItemId; ?>', 'GET'); return false;" class="favor" id="js_favorite_link_like_<?php echo $iItemId; ?>" title="<?php echo _p('foxfavorite.add_to_your_favorites'); ?>" <?php if ($bIsAlreadyFavorite) { ?> style="display:none;"<?php } ?>>
                        <?php echo _p('foxfavorite.favorite'); ?>
                    </a>
                </li>                
                <li  class="yn_page_unfavorite">
                    <a class="unfavor" title="<?php echo _p('foxfavorite.remove_from_your_favorite'); ?>" href="#" onclick="$('#js_favorite_link_like_<?php echo $iItemId; ?>').show(); $('#js_favorite_link_unlike_<?php echo $iItemId; ?>').hide(); $.ajaxCall('foxfavorite.deleteFavorite', 'type=<?php echo $sModule; ?>&amp;id=<?php echo $iItemId; ?>', 'GET'); return false;" id="js_favorite_link_unlike_<?php echo $iItemId; ?>"<?php if (!$bIsAlreadyFavorite) { ?> style="display:none;"<?php } ?>>
                        <?php echo _p('foxfavorite.unfavorite'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <script type="text/javascript" language="javascript">
            $Behavior.onCreateFavoriteButton = function() {
                if($('#section_menu').size() == 0 && $('#yn_section_menu').html() != null)
                {
                    $bt_favor = '<div id="section_menu">' + $('#yn_section_menu').first().html() + '</div>';
                    $('#breadcrumb_holder').append($bt_favor); //default theme
                    $('.item_info').append($bt_favor); //cosmic theme
                    $('#section_menu').show();
                }
                else
                {
                    $bt_favor = $('.yn_page_favorite').first();
                    $bt_unfavor = $('.yn_page_unfavorite').first();
                    if($('#section_menu').find('ul').size() == 0)
                    {
                        $('#section_menu').prepend("<ul></ul>");
                    }
                    if($('#section_menu').find('.yn_page_favorite').size() == 0)
                    {
                        $('#section_menu').find('ul').prepend($bt_favor);
                    }
                    if($('#section_menu').find('.yn_page_unfavorite').size() == 0)
                    {
                        $('#section_menu').find('ul').prepend($bt_unfavor);
                    }
                    $('#section_menu').show();
                }
                $Behavior.inlinePopup();
                $('#yn_section_menu').html('');
            };
        </script>
        <?php
    }
}
?>
