<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


?>

{literal}
<style type="text/css">
</style>
{/literal}

{if $aFAQ}
    <div>
        <dl class="faqs">
            {foreach from=$aFAQ item=FAQ}
                <dt>{$FAQ.question}</dt>
                <dd>{$FAQ.answer}</dd>
            {/foreach}
        </dl>
    </div>
{else}
    {phrase var='no_FAQ_available'}
{/if}

{literal}
    <script type="text/javascript">
        $Behavior.ynsaFaq = function() {
            $('.faqs dd').hide();  /*Hide all DDs inside .faqs*/
            $('.faqs dt').hover(function(){$(this).addClass('hover')},function(){$(this).removeClass('hover')}).click(function(){  /*Add class "hover" on dt when hover*/
            $(this).next().slideToggle('normal');  /*Toggle dd when the respective dt is clicked*/
            });
        }
    </script>
{/literal}