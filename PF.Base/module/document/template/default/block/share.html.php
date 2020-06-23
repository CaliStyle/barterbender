<?php
/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="global_attachment_holder_section" id="global_attachment_document">	
	<div>
        <input id="is_profile" type="hidden" name="val[is_profile]" value="{if isset($bProfile)}yes{else}no{/if}" />
        <input type="hidden" name="val[iframe]" value="1" />
    </div>
	<div><input type="hidden" name="val[method]" value="simple" /></div>	
	<div class="table form-group">
		<div class="table_left">
			{phrase var='title'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[document_title]" style="width:90%;" id="js_form_document_title" class="feed_validation form-control" onchange="validateFeedInput();" />
		</div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{phrase var='document_file'}:
		</div>
		<div class="table_right">	
			<div><input type="file" class="form-control" name="uploadedfile" id="js_form_document_file_input" onchange="validateFeedInput(); $Core.resetActivityFeedErrorMessage();" class="feed_validation" /></div>
			<div class="extra_info">
				{phrase var='document_file_format_support'}<br />
                {phrase var='max_file_sie_maxsize_mb' maxsize=$max_file_size_mb}
			</div>
		</div>
	</div>        
    <div class="table form-group">
        <div class="table_left">
            {phrase var='categories'}:
        </div>
        <div class="table_right">
            {$sCategories}
        </div>            
    </div>
</div>
<script type="text/javascript">
{literal}
$Behavior.imageCategoryShow = function()
{
    validateFeedInput=function(){
    	var a=true;
    	$(".feed_validation:visible").each(function(b,c){
    		if($.trim(c.value)==""){
    			a&=false;
    		}
    	});
    	
    	if(a){
    		$(".activity_feed_form_button .button").removeClass("button_not_active");
    		$bButtonSubmitActive=true;
    	}else{
    		$(".activity_feed_form_button .button").addClass("button_not_active");
    		$bButtonSubmitActive=false;
    	}
    };
    
    $ActivityFeedCompleted.resetDocumentForm=function(){
    	$("#js_form_document_title").val("");
    	$("#js_form_document_file_input").val("");
    }; 
    
    $('.js_mp_category_list_fx').change(function()
    {
        var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
        
        $('.js_mp_category_list_fx').each(function()
        {
            if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
            {
                $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();                
                
                this.value = '';
            }
        });
        
        $('#js_mp_holder_' + $(this).val()).show();
    });
}
{/literal}

</script>