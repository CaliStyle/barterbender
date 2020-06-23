<div class="yn_suggestion_list_item">
<h3>{phrase var='suggestion.people_may_you_know'}</h3>
<form action="" method="post" name="people_may_you_know" style="overflow: hidden;">
    <div id="people_you_may_knows" style="overflow: hidden;" >
    {if count($aData)>0}
    {foreach from=$aData item=aRow key=iKey}

        <div  class="ynsuggestion_item">
            
            <span  class="ajaxLoader hide" style="position: absolute; right:120px;">
                <img src="{$sFullUrl}theme/frontend/default/style/default/image/ajax/add.gif" />
            </span>
            
            <div class="suggestion_image">         
                {$aRow.avatar}
            </div> 
            <div class="suggestion_info">          
                <div class="user_tooltip_info_user" itemprop="name">                
                    {$aRow.info}
                </div>
                <div class="suggestion_description">
                    {$aRow.create}
                </div>
            </div>  
             <div class="suggestion_action">
                <button  type="button" class="button btn btn-primary btn-sm" onclick="doProcess(this, 1, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}', '{$aRow.url}');">{phrase var='suggestion.add_friend_header_block'}</button>
            </div> 

            
        </div>    

    {/foreach}
    {else}  
    <div class="message">{phrase var='suggestion.no_recommend_to_you'}</div>
    {/if}
    </div>
    <input type="hidden" id="offset_people_you_may_knows" value = "0" />

    <img id='view_more_loader' style='display:none' alt="" src="<?php echo Phpfox::getParam('core.path');?>theme/frontend/default/style/default/image/ajax/add.gif">

    {if $total > Phpfox::getParam('suggestion.number_item_on_other_block')}
    <div id='people_you_may_know_view_more'  class="t_center" >
    <a  class='ynsug_view_more no_ajax_link' style='cursor:pointer;'
    onClick=" $('#people_you_may_know_view_more').hide();
              $('#view_more_loader').show();
              $.ajaxCall('suggestion.loadPeopleYouMayKnowAjax','offset='+$('#offset_people_you_may_knows').val()); ">
    {phrase var='suggestion.view_more'}
    </a>
    </div>
    {/if}


</form>
</div>
