<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 09/01/2017
 * Time: 15:12
 */
defined('PHPFOX') or exit('NO DICE!');
?>


<div class="ynadvancedblog_hot_blogger">
    <ul class="clearfix">
        {foreach from=$aItems item=aOtherAuthor}
        <li>
            <div class="ynadvblog_avatar">
                {if !empty($aOtherAuthor.user_image)}
                <a href="#" style="background-image: url('{img user=$aOtherAuthor suffix='_200_square' return_url=true}');"></a>
                {else}
                {img user=$aOtherAuthor suffix='_200_square'}
                {/if}

                <div class="ynadvancedblog-blogger-hover">
                    <div class="clearfix">
                        <div class="ynadvblog_avatar_hover">
                            {if !empty($aOtherAuthor.user_image)}
                            <a href="#" style="background-image: url('{img user=$aOtherAuthor suffix='_200_square' return_url=true}');"></a>
                            {else}
                            {img user=$aOtherAuthor suffix='_200_square'}
                            {/if}
                        </div>
                        <div class="ynadvblog_info_hover">
                            {$aOtherAuthor|user}
                            <div class="ynadvblog_hover_desc">
                                {if !empty($aOtherAuthor.cf_about_me)}
                                {$aOtherAuthor.cf_about_me}
                                {/if}
                            </div>
                        </div>
                    </div>
                    <div class="ynadvblog_hover_option">
                        <div class="ynadvblog_hover_entri clearfix">
                            <div class="pull-left">
                                {if $aOtherAuthor.total_entries == 1}
                                {$aOtherAuthor.total_entries}&nbsp;{_p var='entry'}
                                {else}
                                {$aOtherAuthor.total_entries}&nbsp;{_p var='entries'}
                                {/if}
                            </div>
                            <div class="pull-right" id="js_ynblog_update_follow_{$aOtherAuthor.user_id}">
                                {if $aOtherAuthor.is_followed}
                                <a title="{_p('Un-Follow')}" onclick="ynadvancedblog.updateFollowLink({$aOtherAuthor.user_id},0);return false;">
                                    <i class="fa fa-minus" aria-hidden="true"></i> {_p var='Un-Follow'}
                                </a>
                                {elseif $aOtherAuthor.canFollow}
                                <a title="{_p('Follow')}" onclick="ynadvancedblog.updateFollowLink({$aOtherAuthor.user_id},1);return false;">
                                    <i class="fa fa-plus" aria-hidden="true"></i> {_p var='follow'}
                                </a>
                                {/if}
                            </div>
                        </div>
                        <div class="ynadvblog_hover_followers clearfix">
                            <div class="pull-left" id="js_ynblog_total_update_follow_{$aOtherAuthor.user_id}">
                                {if $aOtherAuthor.total_follower == 1}
                                {$aOtherAuthor.total_follower}&nbsp;{_p var='follower'}
                                {else}
                                {$aOtherAuthor.total_follower}&nbsp;{_p var='followers'}
                                {/if}
                            </div>
                            <div class="pull-right">
                                <a charset="btn btn-default" href="{$aOtherAuthor.user_name|ynblog_profile}" class="post">
                                    <i class="fa fa-search" aria-hidden="true"></i> {_p var='More entries'}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        {/foreach}
    </ul>
</div>

{literal}
<script type="text/javascript">
    $Behavior.ynadvblogShowpopup = function(){
        // hover show popup
        $('.ynadvblog_avatar').mouseover(function(){
            $(this).parents('.ynadvancedblog_hot_blogger li').find('.ynadvancedblog-blogger-hover').stop().fadeIn({duration:'300'});
        });
        $('.ynadvblog_avatar').mouseout(function(){
            $(this).parents('.ynadvancedblog_hot_blogger li').find('.ynadvancedblog-blogger-hover').stop().fadeOut({duration:'300'});
        });
    };
</script>
{/literal}
