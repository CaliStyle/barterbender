<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 09/01/2017
 * Time: 15:12
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
    };
</script>
{/literal}
