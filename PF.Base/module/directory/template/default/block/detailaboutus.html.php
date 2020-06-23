<div id="yndirectory_business_detail_module_aboutus" class="yndirectory_business_detail_module_aboutus">	
    {if !empty($aAboutUs.contentpage)}
	    <div class="yndirectory-about-content item_view_content">{$aAboutUs.contentpage|parse}</div>
    {else}
	    <div class="help-block">
	        {phrase var='no_item_s_found'}.
	    </div>
    {/if}
</div>
