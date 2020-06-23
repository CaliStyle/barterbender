<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" ENCTYPE="multipart/form-data" action="{url link='admincp.feedback.category'}">
    <div class="form-group">
        <input type="hidden" name="val[category_id]" value="{$aForms.category_id}" />
        <input type="hidden" name="val[name]" value="{$aForms.name}" />
        <label>
            {_p var='category_name'}
        </label>
        <div class="form-group">
            {field_language phrase='name' label='Name' field='name' format='val[name_' size=30 maxlength=40 required=true}
        </div>
    </div>

    <div class="form-group">
        <label>
           {_p var='category_description'}
        </label>
        <textarea class="form-control" name="val[description]" cols="30" rows="5" >{$aForms.description}</textarea>
    </div>
    <input type="hidden" name="val[page]" value = "{$page}" />
    <input type="submit" name="val[editcategory]" value="{_p var='save_changes'}" class="btn btn-primary" />
</form>