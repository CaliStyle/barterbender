{module name='socialad.sub-menu'}

<button
	id="js_ynsa_addfaq_btn"
	class="btn btn-success btn-sm"
	onclick="tb_show('{phrase var='add_new_faq'}', $.ajaxBox('socialad.showAddNewFAQPopup', 'height=400&width=350')); return false;"
 >{phrase var='add_new_faq'}</button>
<div class="clear"></div>
{if $aFAQ}
    <div>
        <dl class="faqs">
            {foreach from=$aFAQ item=FAQ}
                <dt>{$FAQ.question}</dt>
                <dd class="item_view_content">{$FAQ.answer|parse}</dd>
            {/foreach}
        </dl>
    </div>
{else}
    {phrase var='no_faq_available'}
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
