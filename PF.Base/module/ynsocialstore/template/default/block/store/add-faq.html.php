<form method="post" onsubmit="return onSubmitAddFaq()" id="js_add_faq_page">
	<input type="hidden" name="faq_id" id="faq_id" value="{$faq_id}">
	<input type="hidden" name="store_id" id="store_id" value="{$store_id}">

	<div class="table form-group">
		<div class="table_left">
			{_p('Question')}
		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="question" id="question" value="{if isset($aFaq.question)}{$aFaq.question}{/if}">
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left">
		{_p('Answer')}
		</div>
		<div class="table_right">
			<textarea  name="answer" class="form-control" id="answer"  rows="6" cols="24">{if isset($aFaq.answer)}{$aFaq.answer}{/if}</textarea>
		</div>
	</div>
	<div class="table checkbox">
		<label>
			<input  type="checkbox" name="disable" class="" {if isset($aFaq.is_active) && $aFaq.is_active == 0}checked{/if} /> 
			{_p('Hide this FAQ')}
		</label>
	</div>
	<div class="ynstore-message" id='message'></div>

	<div class="ynstore-button">
		<button type="submit" class="btn btn-primary" name="update_faq" id="update_faq">{_p('Save Change')}</button>
	</div>
</form>
{literal}
<script type="text/javascript">
    function onSubmitAddFaq() {
        $.ajaxCall('ynsocialstore.addFaq','faq_id='+$('#faq_id').val()+'&faq_data='+$('#js_add_faq_page').serialize(), 'post');
        if($('#js_add_faq_page #answer').val().trim() !== '' ){
            js_box_remove(this);
        }
        return false;
    }
</script>
{/literal}