<div class="yn_suggestion_list_item">
<br><h3>{phrase var='suggestion.jobs_you_may_like'}</h3><br>
<form action="" method="post" name="jobs_you_may_like" style="overflow: hidden;">
    <div id="jobs_you_may_like" style="overflow: hidden;" >
    {if count($aData)>0}
    {foreach from=$aData item=aRow}

        <div class="ynsuggestion_item">
            
            <span class="ajaxLoader hide" style="position: absolute; right:120px;"><img src="{$sFullUrl}theme/frontend/default/style/default/image/ajax/add.gif" /></span>
            
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
                <button type="button" class="button btn btn-primary btn-sm" style="margin-left: 10px; float: right;" onclick="doProcess(this, 1, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}', '{$aRow.url}'); return false;">{$aRow.accept}</button>
            </div> 

            
        </div>    

    {/foreach}
    {else}
    <div class="message">{phrase var='suggestion.no_recommend_to_you'}</div>
    {/if}
    </div>
    <input type="hidden" id="offset_jobs_you_may_like" value = "0" />

    <img id='view_more_loader' style='display:none' alt="" src="<?php echo Phpfox::getParam('core.path');?>theme/frontend/default/style/default/image/ajax/add.gif">

    {if $total > Phpfox::getParam('suggestion.number_item_on_other_block')}
    <div id='jobs_you_may_like_view_more'  class="t_center" >
    <a  class='ynsug_view_more no_ajax_link' style='cursor:pointer;'
    onClick=" $('#jobs_you_may_like_view_more').hide();
              $('#view_more_loader').show();
              $.ajaxCall('suggestion.loadJobsYouMayLikeAjax','offset='+$('#offset_jobs_you_may_like').val()); ">
    {phrase var='suggestion.view_more'}
    </a>
    </div>
    {/if}

</form>
</div>
