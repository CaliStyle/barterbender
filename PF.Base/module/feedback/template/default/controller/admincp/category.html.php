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
{literal}
<style type="text/css">
.table_left
{
    width: auto;
}
.table_right
{
    margin-left: 100px;
}
.feedback_category_admin
{
	margin-left:300px;
}
</style>
{/literal}
<form method="post" ENCTYPE="multipart/form-data" action="{url link='current'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='create_a_new_category'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                {field_language phrase='name' label='Name' field='name' format='val[name_' size=30 maxlength=40 required=true}
            </div>

            <div class="form-group">
                <label>
                   {_p var='description'}
                </label>
                <textarea class="form-control" name="val[description]" cols="30" rows="5" >{$description}</textarea>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="val[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>

{if count($aCats)>0}
    <form action="{url link='current'}" method="post" id="order_display_sb" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {_p var='categories_management'}
                </div>
            </div>
            <div class="table-responsive">
                <table align="center" class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="w200">{_p var='name'}</th>
                            <th>{_p var='description'}</th>
                            <th class="w180">{_p var='number_of_times_used'}</th>
                            <th class="w100">{_p var='options'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aCats key=iKey item=cat}
                        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td>{$cat.name|shorten:30:'...'}</td>
                            <td>{$cat.description|shorten:100:'...'}</td>
                            <td>{$cat.numbers}</td>
                           <td>
                            <a id="edit_{$cat.category_id}" href="#?call=feedback.callEditCategory&amp;height=270&amp;width=400&amp;cat_id={$cat.category_id}&amp;page={$pageNumber}" class="inlinePopup" title="{_p var='edit_category'}">{_p var='edit'}</a>  |
                            <a id="delete_{$cat.category_id}" href="javascript:void(0);" onclick="deleteCategory({$cat.category_id});return false;" title="{_p var='delete_category'}">{_p var='delete'}</a>
                           </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </form>
{else}
{phrase var ='feedback.no_categories_found'}
{/if}
<div class="t_right">
    {pager}
</div>

