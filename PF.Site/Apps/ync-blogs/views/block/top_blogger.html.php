<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 06/01/2017
 * Time: 18:14
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="p-listing-container p-advblog-blogger-container col-4" data-mode-view="{$sModeViewDefault}">
    {foreach from=$aItems item=aItem}
        {template file='ynblog.block.entry_blogger'}
    {/foreach}
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

            $('.ynadvancedblog_hot_blogger').find('.ynadvblog_avatar').find('a:first').removeAttr('title');
        };
    </script>
{/literal}