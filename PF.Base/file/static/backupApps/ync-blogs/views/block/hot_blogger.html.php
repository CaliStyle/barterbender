<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 06/01/2017
 * Time: 18:14
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ynadvancedblog_hot_blogger">
    <ul class="clearfix">
        {foreach from=$aItems item=aHotBlogger}
            <li>
                <div class="ynadvblog_avatar">
                    {if !empty($aHotBlogger.user_image)}
                        <a href="#" style="background-image: url('{img user=$aHotBlogger suffix='_200_square' return_url=true}');"></a>
                    {else}
                        {img user=$aHotBlogger suffix='_200_square'}
                    {/if}

                    <div class="ynadvancedblog-blogger-hover">
                        <div class="clearfix">
                            <div class="ynadvblog_avatar_hover">
                                {if !empty($aHotBlogger.user_image)}
                                    <a href="#" style="background-image: url('{img user=$aHotBlogger suffix='_200_square' return_url=true}');"></a>
                                {else}
                                    {img user=$aHotBlogger suffix='_200_square'}
                                {/if}
                            </div>
                            <div class="ynadvblog_info_hover">
                                {$aHotBlogger|user}
                                <div class="ynadvblog_hover_desc">
                                    {if !empty($aHotBlogger.cf_about_me)}
                                        {$aHotBlogger.cf_about_me}
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="ynadvblog_hover_option">
                            <div class="ynadvblog_hover_entri clearfix">
                                <div class="pull-left">
                                    {if $aHotBlogger.total_entries == 1}
                                        {$aHotBlogger.total_entries}&nbsp;{_p var='entry'}
                                    {else}
                                        {$aHotBlogger.total_entries}&nbsp;{_p var='entries'}
                                    {/if}
                                </div>
                                <div class="pull-right" id="js_ynblog_update_follow_{$aHotBlogger.user_id}">
                                    {if $aHotBlogger.is_followed}
                                        <a title="{_p('Un-Follow')}" onclick="ynadvancedblog.updateFollowLink({$aHotBlogger.user_id},0);return false;">
                                            <i class="fa fa-minus" aria-hidden="true"></i> {_p var='Un-Follow'}
                                        </a>
                                    {elseif $aHotBlogger.canFollow}
                                        <a title="{_p('Follow')}" onclick="ynadvancedblog.updateFollowLink({$aHotBlogger.user_id},1);return false;">
                                            <i class="fa fa-plus" aria-hidden="true"></i> {_p var='follow'}
                                        </a>
                                    {/if}
                                </div>
                            </div>
                            <div class="ynadvblog_hover_followers clearfix">
                                <div class="pull-left" id="js_ynblog_total_update_follow_{$aHotBlogger.user_id}">
                                    {if $aHotBlogger.total_follower == 1}
                                        {$aHotBlogger.total_follower}&nbsp;{_p var='follower'}
                                    {else}
                                        {$aHotBlogger.total_follower}&nbsp;{_p var='followers'}
                                    {/if}
                                </div>
                                <div class="pull-right">
                                    <a charset="btn btn-default" href="{$aHotBlogger.user_name|ynblog_profile}" class="post">
                                        <i class="fa fa-wpforms" aria-hidden="true"></i> {_p var='More entries'}
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