<?php
if (phpfox::getUserBy('view_id') != 7 && Phpfox::isUser())
{
    $sModule = 'advancedphoto';
    $favor_img = phpfox::getParam('core.path') . 'module/foxfavorite/static/image/favorite.png';
    $unfavor_img = phpfox::getParam('core.path') . 'module/foxfavorite/static/image/unfavorite.png';
    $iItemId = $this->_aVars['aForms']['photo_id'];
    $bIsAlreadyFavorite = phpfox::getService('foxfavorite')->isAlreadyFavorite($sModule, $iItemId);
    if (phpfox::getLib('request')->get('req1') !== 'advancedphoto')
    {
        $bIsAvailModule = phpfox::getService('foxfavorite')->isAvailModule($sModule);

        if ($bIsAvailModule !== false)
        {
            $sVersion = phpfox::getVersion();
            $fVersion = (float) $sVersion;
            if ($fVersion >= 3.3)
            {
                ?>
                <li class="favor_dot" style="display:none">
                    <span>&#183;</span>
                </li>
                <li class="favor_photo yn_page_favorite" style="list-style-type: none;">
                    <a href="#" onclick="$('#js_favorite_link_unlike_<?php echo $iItemId; ?>').show(); $('#js_favorite_link_like_<?php echo $iItemId; ?>').hide(); $.ajaxCall('foxfavorite.addFavorite', 'type=<?php echo $sModule; ?>&amp;id=<?php echo $iItemId; ?>', 'GET'); return false;" class="inlinePopup" id="js_favorite_link_like_<?php echo $iItemId; ?>" title="<?php echo _p('foxfavorite.add_to_your_favorites'); ?>" style="display:none;">
                        <?php echo _p('foxfavorite.favorite'); ?>
                    </a>
                </li>
                <li class="unfavor_photo yn_page_unfavorite" style="list-style-type: none;">
                    <a  title="<?php echo _p('foxfavorite.remove_from_your_favorite'); ?>" href="#" onclick="$('#js_favorite_link_like_<?php echo $iItemId; ?>').show(); $('#js_favorite_link_unlike_<?php echo $iItemId; ?>').hide(); $.ajaxCall('foxfavorite.deleteFavorite', 'type=<?php echo $sModule; ?>&amp;id=<?php echo $iItemId; ?>', 'GET'); return false;" id="js_favorite_link_unlike_<?php echo $iItemId; ?>" style="display:none;">
                        <?php echo _p('foxfavorite.unfavorite'); ?>
                    </a>
                </li>

                <script type="text/javascript" language="javascript">
                    $Behavior.onCreateFavoriteButton = function() {
                        //$(".yn_favor_photo_button").remove();
                        $bt_favor = $('.favor_photo');//.addClass("yn_favor_photo_button");
                        $bt_unfavor = $('.unfavor_photo');//.addClass("yn_favor_photo_button");
                        $favor_dot = $('.favor_dot');//.addClass("yn_favor_photo_button");
                        var item_id = <?php echo $iItemId; ?>;
                        var bIsAlreadyFavorite = <?php echo ($bIsAlreadyFavorite ? "true" : "false"); ?>;
                        
                        if($('#js_photo_view_comment_holder').find('.js_feed_comment_border').find('.comment_mini_link_like').find('ul:first').find('.favor_dot').size()==0) {
                            $('#js_photo_view_comment_holder').find('.js_feed_comment_border').find('.comment_mini_link_like').find('ul:first').append($favor_dot);
                        }
                        if($('#js_photo_view_comment_holder').find('.js_feed_comment_border').find('.comment_mini_link_like').find('ul:first').find('.favor_photo').size()==0) {
                            $('#js_photo_view_comment_holder').find('.js_feed_comment_border').find('.comment_mini_link_like').find('ul:first').append($bt_favor);
                        }
                        if($('#js_photo_view_comment_holder').find('.js_feed_comment_border').find('.comment_mini_link_like').find('ul:first').find('.unfavor_photo').size()==0) {
                            $('#js_photo_view_comment_holder').find('.js_feed_comment_border').find('.comment_mini_link_like').find('ul:first').append($bt_unfavor);
                        }
                        
                        if(!bIsAlreadyFavorite) //$('.js_feed_comment_border').find('.comment_mini_link_like').find('ul:first').find($bt_favor).size() < 2 && 
                        {
                            $('.favor_dot:first').show();
                            $('#js_favorite_link_like_'+item_id+':first').show();
                        }
                        if(bIsAlreadyFavorite) //$('.js_feed_comment_border').find('.comment_mini_link_like').find('ul:first').find($bt_unfavor).size() < 2 && 
                        {	
                            $('.favor_dot:first').show();
                            $('#js_favorite_link_unlike_'+item_id+':first').show();
                        }
                    };
                </script>
                <?php
            }
            else
            {
                ?>
                <style type="text/css">
                    .favor_photo, .unfavor_photo{
                        font-size:12px;
                        font-weight:bold;
                    }                    
                </style>

                <li class="favor_dot" style="display:none">
                    <span>&#183;</span>
                </li>
                <li class="favor_photo">
                    <a href="#" onclick="$('#js_favorite_link_unlike_<?php echo $iItemId; ?>').show(); $('#js_favorite_link_like_<?php echo $iItemId; ?>').hide();$.ajaxCall('foxfavorite.addFavorite', 'type=<?php echo $sModule; ?>&amp;id=<?php echo $iItemId; ?>', 'GET'); return false;" class="inlinePopup" id="js_favorite_link_like_<?php echo $iItemId; ?>" title="<?php echo _p('foxfavorite.add_to_your_favorites'); ?>" style="display:none;">
                        <?php echo _p('foxfavorite.favorite'); ?>
                    </a>
                </li>
                <li class="unfavor_photo">
                    <a  title="<?php echo _p('foxfavorite.remove_from_your_favorite'); ?>" href="#" onclick="$('#js_favorite_link_like_<?php echo $iItemId; ?>').show(); $('#js_favorite_link_unlike_<?php echo $iItemId; ?>').hide(); $.ajaxCall('foxfavorite.deleteFavorite', 'type=<?php echo $sModule; ?>&amp;id=<?php echo $iItemId; ?>', 'GET'); return false;" id="js_favorite_link_unlike_<?php echo $iItemId; ?>" style="display:none;">
                        <?php echo _p('foxfavorite.unfavorite'); ?>
                    </a>
                </li>

                <script type="text/javascript" language="javascript">
                    $Behavior.onCreateFavoriteButton = function() {                        
                        $bt_favor = $('.favor_photo');//.addClass("yn_favor_photo_button");
                        $bt_unfavor = $('.unfavor_photo');//.addClass("yn_favor_photo_button");
                        $favor_dot = $('.favor_dot');//.addClass("yn_favor_photo_button");
                        var item_id = <?php echo $iItemId; ?>;
                        var bIsAlreadyFavorite = <?php echo ($bIsAlreadyFavorite ? "true" : "false"); ?>;
                        $('.photo_view_comment').find('.js_feed_comment_border').find('ul:first').append($favor_dot);
                        $('.photo_view_comment').find('.js_feed_comment_border').find('ul:first').append($bt_favor);
                        $('.photo_view_comment').find('.js_feed_comment_border').find('ul:first').append($bt_unfavor);
                        if($('.photo_view_comment').find('.js_feed_comment_border').find('ul:first').find($bt_favor).size() < 2 && !bIsAlreadyFavorite)
                        {
                            $('.favor_dot:first').show();
                            $('#js_favorite_link_like_'+item_id+':first').show();
                        }
                        if($('.photo_view_comment').find('.js_feed_comment_border').find('ul:first').find($bt_unfavor).size() < 2 && bIsAlreadyFavorite)
                        {	
                            $('.favor_dot:first').show();
                            $('#js_favorite_link_unlike_'+item_id+':first').show();
                        }
                    };
                </script>
                <?php
            }
        }
    }
}
?>