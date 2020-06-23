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
.table_right{
    margin-left: 100px;
}
.table .table_left{
  float:left;
}
</style>
{/literal}
<form method="post" enctype="multipart/form-data" action="{url link='admincp.feedback.feedbacks'}">
<input type="hidden" name="val[feedback_id]" value="{$aFeedBack.feedback_id}" />
    <div class="table form-group">
            <div class="table_left">
                    <label for="title">{_p var='title'}{required} </label>
            </div>
            <div class="table_right" style="margin-left: 100px;">
                    <input class="form-control" type="text" name="val[title]" value="{$aFeedBack.title}" id="title" size="40" />
            </div>
      <div class="clear"></div>
    </div>
<div class="table form-group">
    <div class="table_left">
       {_p var='description'}{required}
    </div>
    <div class="table_right" style="margin-left: 100px;">
        <textarea class="form-control" name="val[description]" cols="30" rows="5" >{$aFeedBack.feedback_description}</textarea>
    </div>
    <div class="clear"></div>
</div>
<div class="table form-group">
    <div class="table_left">
       {_p var='category'}
    </div>
    <div class="table_right" style="margin-left: 100px;">

       <select id="cat[category_id]" name="val[category_id]" class="form-control">
           {$aFeedBack.feedback_category_id}
           <option label="" value="0">Uncategorized</option>
         {foreach from=$aCats item=cat}
             {if $cat.category_id eq $aFeedBack.feedback_category_id}
              <option value="{$cat.category_id}" selected>{$cat.name}</option>
              {else}
                 <option value="{$cat.category_id}">{$cat.name}</option>
              {/if}
         {/foreach}
       </select>
    </div>
    <div class="clear"></div>
</div>
    <div class="table form-group">
        <div class="table_left">
          {_p var='serverity'}
        </div>
        <div class="table_right" style="margin-left: 100px;">
           <select id="ser[serverity_id]" name="val[serverity_id]" class="form-control">
             <option label="" value="0">Unseverity</option>
             {foreach from=$aSers item=aSer}
                 {if $aSer.serverity_id eq $aFeedBack.feedback_serverity_id}
                    <option value="{$aSer.serverity_id}" selected>{$aSer.name}</option>
                    {else}
                    <option value="{$aSer.serverity_id}">{$aSer.name}</option>
                 {/if}
             {/foreach}
           </select>
        </div>
        <div class="clear"></div>
    </div>

    {if Phpfox::isModule('tag')}
        <div class="table form-group">
            <div class="table_left">
                {_p var='topics'}:
            </div>
            <div class="table_right" style="margin-left: 100px;" >
                <input type="text" name="val{if $iItemId}[{$iItemId}]{/if}[tag_list]" value="{value type='input' id='tag_list'}" size="30" class="form-control"/>
                <div class="help-block">
                    {_p var='separate_multiple_topics_with_commas'}
                </div>
            </div>
	        <div class="clear"></div>
        </div>
    {/if}
    <div class="table form-group">
    <div class="table_left" >
      {_p var='feedback_visibility'}
    </div>
    <div class="table_right" style="margin-left: 100px;">
       <select id="privacy" name="val[privacy]" class="form-control">
            {if $aFeedBack.privacy == 1}
             <option label="Public" value="1" selected>{_p var='feedback_public'}</option>
             {else}
             <option label="Public" value="1" >{_p var='feedback_public'}</option>
             {/if}
            {if $aFeedBack.privacy == 2}
              <option value="2" label="Private" selected>{_p var='feedback_private'}</option>
            {else}
              <option value="2" label="Private" >{_p var='feedback_private'}</option>
            {/if}
            {*{if $aFeedBack.privacy == 3}
            <option label="Pending" value="3" selected>{_p var='feedback_pending'}</option>
            {else}
            <option label="Pending" value="3" >{_p var='feedback_pending'}</option>
            {/if}*}
       </select>
    </div>
    <div class="clear"></div>
</div>
{if $aFeedBack.privacy == 3}
<div class="table form-group">
    <div class="table_left">
     {_p var='send_mail_to_none_user'}
    </div>
    <div class="table_right" style="margin-left: 100px;">
    	<div class="item_is_active_holder">
        	<span class="js_item_active item_is_active"><input type="radio" name="val[send_mail_to_none_user]" value="1"  /> {phrase var='admincp.yes'}</span>
           	<span class="js_item_active item_is_not_active"><input type="radio" name="val[send_mail_to_none_user]" value="0" {if 1 } {value type='radio' id='send_mail_to_none_user' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
        </div>
    </div>
    <div class="clear"></div>
</div>
{/if}
<div class="table form-group">
    <input type="submit" name="editfeedbackforadmin" value="{_p var='save_changes'}" class="btn btn-sm btn-primary" style="background:#2681D5;" />
    <input type="button" class="btn btn-sm btn-warning" href="javascript:void(0);" onclick="tb_remove();" value="{_p var='cancel'}">
</div>
</form>
