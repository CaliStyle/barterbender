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
    float: left;
    padding-top: 0px !important;
}
.table_right
{
  margin-left: 100px;
  padding: 0px !important;
  border: none !important;
}

select{
  margin: 0px !important;
}
</style>
{/literal}
<div id="error_message" style="display: none;"></div>
<form  method="post" enctype="multipart/form-data" action="{url link='feedback.manage'}" onsubmit="return validForm(this);">
<input type="hidden" name="val[feedback_id]" value="{$aFeedBack.feedback_id}" />
<input type="hidden" name="val[feedback_title_url]" value="{$aFeedBack.title_url}" />
    <div class="table form-group">
        <label for="title">{_p var='title'}{required} </label>         
        <input type="text" class="form-control" name="val[title]" value="{$aFeedBack.title}" id="title" size="40" />
      
    </div>
<div class="table form-group">
       <label>{_p var='description'}{required}</label>
        <textarea class="form-control" name="val[description]" cols="30" rows="5" >{$aFeedBack.feedback_description}</textarea>
</div>
<div class="table form-group">
    <label>
       {_p var='category'}
    </label>
    
       <select class="form-control" id="cat[category_id]" name="val[category_id]">
         {foreach from=$aCats item=cat}
             {if $cat.category_id eq $aFeedBack.feedback_category_id}
              <option value="{$cat.category_id}" selected>{$cat.name}</option>
              {else}                 
                 <option value="{$cat.category_id}">{$cat.name}</option>
              {/if}    
         {/foreach}
       </select>
    
    
</div>
<div class="table form-group">
    <label>
      {_p var='serverity'}
    </label>
    
   <select class="form-control" id="ser[serverity_id]" name="val[serverity_id]">
     <option label="" value="0"></option>
     {foreach from=$aSers item=aSer}
         {if $aSer.serverity_id eq $aFeedBack.feedback_serverity_id}
            <option value="{$aSer.serverity_id}" selected>{$aSer.name}</option>
            {else}
            <option value="{$aSer.serverity_id}">{$aSer.name}</option>
         {/if}
     {/foreach}
   </select>
    
    
</div>
{if Phpfox::isModule('tag')}{module name='tag.add' sType=feedback}{/if}
    <div class="table form-group">
    <label>
      {_p var='feedback_visibility'}
    </label>
    
   <select class="form-control" id="privacy" name="val[privacy]">
        {if $aFeedBack.privacy == 1}
         <option value="1" selected>{_p var='public'}</option>
         {else}
         <option value="1" >{_p var='public'}</option>
         {/if}
        {if $aFeedBack.privacy == 2}
          <option value="2" selected>{_p var='private'}</option>
        {else}                
          <option value="2" >{_p var='private'}</option>
        {/if}
   </select>
    
    
</div>
<div class="table form-group">
    <input type="submit" name="editfeedback" value="{_p var='save_changes'}" class="btn btn-sm btn-primary" />
    <a class="btn btn-sm btn-warning" href="javascript:void(0);" class="btn btn-default" onclick="tb_remove();">{_p var='cancel'}</a>
</div>
</form>

{literal}
<script type="text/javascript">
    var validForm = function(){
        $('#error_message').html('');
        title = $('input[name="val[title]"]').val();
        description = $('textarea[name="val[description]"]').val();
        bError = true;
        if(title == "")
        {
            $('#error_message').append('<div class="error_message">'+oTranslations['title_cannot_be_empty']+'</div>').show();
            bError = false;
        }
        if(description == "")
        {
            $('#error_message').append('<div class="error_message">'+oTranslations['description_of_feedback_cannot_be_empty']+'</div>').show();
            bError = false;
        }
        return bError;
    }
</script>
{/literal}