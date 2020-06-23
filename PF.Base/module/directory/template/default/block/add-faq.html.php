<form method="post" action="" id="js_add_faq_page">
	<input type="hidden" name="faq_id" id="faq_id" value="{$faq_id}">
	<input type="hidden" name="business_id" id="business_id" value="{$business_id}">

	<div class="table form-group">
		<div class="table_left">
			Question
		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="question" id="question" value="{if isset($aFaq.question)}{$aFaq.question}{/if}">
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left">
		Answer
		</div>
		<div class="table_right">
			<textarea  name="answer" class="form-control" id="answer"  rows="6" cols="24">{if isset($aFaq.answer)}{$aFaq.answer}{/if}</textarea>
		</div>
	</div>
	<div class="yndirectory-message" id='message'></div>
	<div class = "yndirectory-button">
		<button  type="button" class="btn-primary btn-sm" name="update_faq" id="update_faq" onclick="submitAddFaq();">{phrase var='save_changes'}</button>
	</div>
</form>
{literal}
<script type="text/javascript">
    function submitAddFaq() {
        $.ajaxCall('directory.addFaq','faq_id='+$('#faq_id').val()+'&faq_data='+$('#js_add_faq_page').serialize(), 'post');
        if($('#js_add_faq_page #question').val() != '' && $('#js_add_faq_page #answer').val().trim() != '' ){
            js_box_remove(this);
        }
        return false;
    }
</script>
{/literal}