<?php 
/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}

<style>

	textarea#question,
	textarea#answer{
		width: 100%;
		box-sizing: border-box;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		margin: 0px;
	}

</style>

{/literal}


{$sCreateJs}
<form method="post" action="{url link='admincp.coupon.faq.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.faq_id}" /></div>
{/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if $bIsEdit}
                {phrase var='editing_faq_s'}: {$aForms.question_parsed|strip_tags}
                {else}
                {phrase var='add_faq_s'}:
                {/if}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{required}{phrase var='question'}:</label>
                <input type="text" class="form-control" name="val[question]" value="{value id=question type='input'}">
            </div>
            <div class="form-group">
                <label for="">{required}{phrase var='answer'}:</label>
                {editor id='answer' rows='15'}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary" />
            {if $bIsEdit}
                <input type="submit" value="Cancel" class="btn btn-default" onclick="window.location.href = '{url link=\'admincp.coupon.faq\'}'; return false;" />
            {/if}
        </div>
    </div>
</form>

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