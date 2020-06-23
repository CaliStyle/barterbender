
<div id="ynauction_detail_module_activities" class="ynauction_detail_module_activities">
</div>

{literal}
<script type="text/javascript">
    $Behavior.ynauction_activities_init = function() {
		if($('#ynauction_trix_header_activity').length){
			$('#ynauction_trix_header_activity').show();
		}
    	if($('#ynauction_detail_module_activities').length > 0){
	    	$('#js_main_feed_holder').show();
	    	$('.ym-feed-header').css("display","-moz-box");
	    	$('.ym-feed-header').css("display","box");
	    	$('.ym-feed-header').css("display","-webkit-box");
	    	$('#content').find('#js_feed_content').each(function(index, el) {
	    		$(this).show();
	    	});
    	} 
    }
</script>
{/literal}
