<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 18:44
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {if $bIsEdit}
                {_p('edit_faqs')}:
            {else}
                {_p('add_faqs')}:
            {/if}
        </div>
    </div>
        <form method="post" action="{url link='current'}">
            <div class="panel-body">
                {if $bIsEdit}
                <div><input type="hidden" name="idFaq" value="{$iEditId}" /></div>
                {/if}
                <div class="form-group">
                    <label for="faq-question">{required}{_p('Question')}:</label>
                    <input type="text" maxlength="250" name="val[question]" id="faq-question" class="form-control" cols="30" rows="5" value="{if isset($aForms.question)}{$aForms.question}{/if}"/>
                    <label for="faq-answer">{required}{_p('Answer')}:</label>
                    <textarea name="val[answer]" class="form-control" id="faq-answer" cols="30" rows="10">{if isset($aForms.answer)}{$aForms.answer}{/if}</textarea>
                </div>
            </div>
            <div class="panel-footer">
                <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
            </div>
        </form>
</div>