<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_NewsFeed
 * @version          2.04
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<style type="text/css">
    .table_right{
        margin-left: 100px;
    }
</style>
{/literal}

<form method="post" ENCTYPE="multipart/form-data" action="{url link='admincp.foxfeedspro.categories'}" onsubmit="return checksubmit()">
<input type="hidden" value="{$cat_edit.category_id}" name="cat_edit[category_id]">
<input type="hidden" value="{$iPage}" name="cat_edit[iPage]" />
<div class="form-group">
    <label for="">{required}Category Name :</label>
    <input type="text" name="cat_edit[name]" value="{$cat_edit.category_name}" class="input form-control" id="name_category" />
</div>

<div class="form-group">
    <label for="">Description :</label>
    <textarea class="form-control" type="text" name="cat_edit[description]" cols="30" rows="5" >{$cat_edit.category_description}</textarea>
</div>

<div class="form-group">
    <label for="">Display Order :</label>
    <input class="form-control" width="40px" size="5" type="text"  name="cat_edit[cat_order]" value="{$cat_edit.category_order}"/>
</div>
<div class="clear">
    <input type="submit" name="cat_edit[submit]" value="{phrase var='core.submit'}" class="btn btn-primary btn-sm"  />
</div>
</form>
{literal}
<script type="">
    function checksubmit()
    {
        if (document.getElementById('name_category').value == "")
        {
            alert('The name of category cannot be empty');
            return false;
        }
            
        return true;
    }
</script>
{/literal}