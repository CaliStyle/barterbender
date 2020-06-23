<?php 
/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Socialad
 * @version        3.01
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
        {if $bIsEdit}
            {phrase var='editing_faq_s'}: {$aForms.question|strip_tags}
        {else}
            {phrase var='add_faq_s'}:
        {/if}
        </div>
    </div>
{$sCreateJs}
<form method="post" action="{url link='admincp.socialad.faq.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.faq_id}" /></div>
{/if}
    <div class="panel-body">
        <div class="form-group">
            <label for="question">
                {required}{phrase var='question'}:
            </label>
            <textarea name="val[question]" class="form-control" id="question" cols="30" rows="5" >{value id='question' type='textarea'}</textarea>
        </div>
        <div class="table">
            <label for="answer">
                {required}{phrase var='answer'}:
            </label>
            {editor id='answer' rows='15'}
        </div>
    </div>
	<div class="panel-footer">
		<input type="submit" value="{phrase var='submit'}" class="btn btn-primary" />
        {if $bIsEdit}<input type="submit" value="Cancel" class="btn btn-default" onclick="window.location.href = '{url link=\'admincp.socialad.faq\'}'; return false;" />{/if}
	</div>
</form>
</div>
{literal}
	<script type="text/javascript" language="JavaScript">
		$Behavior.yncFaqAddInit = function(){
			Editor.setId('question');
			Editor.getEditors();

		    $("#question").click(function() {
		        Editor.setId('question');
		    });
		    $("#js_editor_menu_question").bind("click", function(){
		        Editor.setId("question");
		    });
		    $("#layer_question").bind("click", function(){
		        Editor.setId("question");
		    });

		    $("#js_editor_menu_answer").bind("click", function(){
		        Editor.setId("answer");
		    });
		    $("#layer_answer").bind("click", function(){
		        Editor.setId("answer");
		    });

		    $("a.js_hover_title").bind("click", function() {
		        Editor.setId('question');
		    });	

		}
	</script>
{/literal}