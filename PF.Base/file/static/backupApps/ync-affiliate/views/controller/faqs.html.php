{if count($aFaqs)}
<ul class="yncaffiliate_faq_items">
    {foreach from=$aFaqs key=iKey item=aItem}
        <li class="yncaffiliate_faq_item">
            <div class="title fw-bold"><i class="fa fa-caret-right" aria-hidden="true"></i>{$aItem.question|clean}</div>
            <div class="content">{$aItem.answer|parse}</div>
        </li>
    {/foreach}
    {pager}
</ul>
{elseif $iPage <=1}
    {_p var='no_faqs_has_been_added'}
{/if}
{literal}
<script type="text/javascript">
	$Behavior.AffiliateFaq = function(){
		$('.title').click(function(){
			var parent = $(this).parents('.yncaffiliate_faq_item');
			parent.find('.content').slideToggle('fast');
			$(this).toggleClass('active');
		});
	}
</script>
{/literal}