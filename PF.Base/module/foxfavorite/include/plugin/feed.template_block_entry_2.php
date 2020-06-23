<?php
if (Phpfox::getParam('core.site_wide_ajax_browsing') && Phpfox::isUser() && (Phpfox::getUserBy('view_id') == 0))
{
    ?>

    <?php
    $sModule = (defined('PHPFOX_IS_USER_PROFILE')) ? 'profile' : Phpfox::getLib('module')->getModuleName();
    
    $iItemId = Phpfox::getService('foxfavorite')->getItemId($sModule);
    $bIsViewItem = Phpfox::getService('foxfavorite')->isViewItem($sModule);
    $bIsAvailModule = Phpfox::getService('foxfavorite')->isAvailModule($sModule);
    $bIsAlreadyFavorite = Phpfox::getService('foxfavorite')->isAlreadyFavorite($sModule, $iItemId);

    if ($bIsViewItem && $bIsAvailModule && !Phpfox::isAdminPanel() && !defined('PHPFOX_IS_USER_PROFILE'))
    {
        switch ($sModule)
        {
            case 'pages':
                break;
            case 'advancedmarketplace':
                break;
            case 'advancedphoto':
            case 'photo':
                $bIsTheater = phpfox::getLib('request')->get('theater');
                if ($bIsTheater)
                {
                    break;
                }
            case 'contest':
                break;
            case 'coupon':
                break;
            case 'event':
                break;
            case 'fevent':
                break;
            case 'foxfeedspro':
                break;
            case 'jobposting':
                break;
            case 'karaoke':
                break;
            case 'music':
                if ($sController == 'view-album')
                {
                    break;
                }
            case 'musicsharing':
                break;
            case 'resume':
                break;
            case 'videochannel':
                break;
            default: //for modules use theme_template_body__end
                ?>
                <div id="yn_section_menu"  style="display:none;">
                    <ul>
                        <li  class="yn_page_favorite">
                            <a href="#" onclick="$('#js_favorite_link_unlike_<?php echo $iItemId; ?>').show(); $('#js_favorite_link_like_<?php echo $iItemId; ?>').hide(); $.ajaxCall('foxfavorite.addFavorite', 'type=<?php echo $sModule; ?>&amp;id=<?php echo $iItemId; ?>', 'GET'); return false;" class="favor" id="js_favorite_link_like_<?php echo $iItemId; ?>" title="<?php echo _p('foxfavorite.add_to_your_favorites'); ?>" <?php if ($bIsAlreadyFavorite) {?> style="display:none;"<?php } ?>>
                                <img class="v_middle" src="<?php echo $favor_img; ?>" style="max-width:19px; max-left:19px;" />
                                <?php echo _p('foxfavorite.favorite'); ?>
                            </a>
                        </li>
                    </ul>
                    <ul>
                        <li  class="yn_page_unfavorite">
                            <a class="unfavor" title="<?php echo _p('foxfavorite.remove_from_your_favorite'); ?>" href="#" onclick="$('#js_favorite_link_like_<?php echo $iItemId; ?>').show(); $('#js_favorite_link_unlike_<?php echo $iItemId; ?>').hide(); $.ajaxCall('foxfavorite.deleteFavorite', 'type=<?php echo $sModule; ?>&amp;id=<?php echo $iItemId; ?>', 'GET'); return false;" id="js_favorite_link_unlike_<?php echo $iItemId; ?>"<?php if (!$bIsAlreadyFavorite){?> style="display:none;"<?php } ?>>
                                <img class="v_middle" src="<?php echo $unfavor_img; ?>" style="max-width:19px; max-left:19px;" />
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
                            $('.main_breadcrumb_holder').append($bt_favor); //cosmic theme
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
                    };                       
                </script>
                <?php
                break;
        }
    }
}
?>