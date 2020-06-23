<div id="yndirectory_business_detail_module_faq" class="yndirectory_business_detail_module_faq">	
	{if count($aFAQs) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}
	<div class="yndirectory-faq-list">
        {foreach from=$aFAQs item=aFAQ}
        	<div class="yndirectory-faq-item">
                <dt class="faq-open">
                    <span class='drop-button toggle-open'><i class="fa fa-plus-square"></i></span>
                    <span class="yndirectory-faq-title">{$aFAQ.question}</span>
                </dt> 
                <dd class="drop-content" style="display: none;">
                    <span class="yndirectory-faq-cont">{$aFAQ.answer|parse}</span>
                </dd>
            </div>
        {/foreach}
	</div>
</div>

{literal}
<script type="text/javascript">
    ;$Behavior.yndirectory_faq_init = function() {
    	if($('#yndirectory_business_detail_module_faq').length > 0){
            $('.drop-button').click(function(){
                var dropthis = $(this);
                
     			dropthis.parent().next().slideToggle(); // Toggle dd when the respective dt is clicked
                
                if ( $(this).hasClass('toggle-open') ) {
                    dropthis.removeClass('toggle-open');
                    dropthis.parent().removeClass('faq-open');
                } else {
                    dropthis.addClass('toggle-open');
                    dropthis.parent().addClass('faq-open');
                }
       	    }); 
    	} 
    };        
</script>
{/literal}
