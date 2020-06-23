<div id="yndirectory_business_detail_module_activities" class="yndirectory_business_detail_module_activities">
	{module name='feed.display'}
</div>

{literal}
<script type="text/javascript">
    $Behavior.yndirectory_activities_init = function() {
    	if($('#yndirectory_business_detail_module_activities').length > 0){
	    	$('#js_main_feed_holder').show();
	    	$('.ym-feed-header').css("display","-moz-box");
	    	$('.ym-feed-header').css("display","box");
	    	$('.ym-feed-header').css("display","-webkit-box");
	    	$('#content').find('#js_feed_content').each(function(index, el) {
	    		$(this).show();
	    	});

	    	if ($('#btn_display_with_friend').length) {
	    	    $('#btn_display_with_friend').hide();
            }
            $('#content-stage').css('box-shadow', 'none');
    	}
		{/literal}
		{if !Phpfox::getUserParam('directory.can_comment_on_business')}
			$('.activity_feed_form_share').remove();
			$('.activity_feed_form').remove();
		{/if}
		{literal}
    }        
</script>
{/literal}
