
{literal}
<script type="text/javascript">
   $Behavior.ynContestShowContestProfileImage = function(){
         if($(window).width() > 1024){
             $('.js_contest_click_image').click(function(){
                   var oNewImage = new Image();
                   oNewImage.onload = function(){
                         $('#js_marketplace_click_image_viewer').show();
                         $('#js_marketplace_click_image_viewer_inner').html('<img src="' + this.src + '" style="max-width: 580px; max-height: 580px" alt="" />');
                         $('#js_marketplace_click_image_viewer_close').show();
                   };
                   oNewImage.src = $(this).attr('href');
    
                   return false;
             });
    
             $('#js_marketplace_click_image_viewer_close a').click(function(){
                   $('#js_marketplace_click_image_viewer').hide();
                   return false;
             });
       }
       else{
           $('.js_contest_click_image').removeAttr('href');
       }
   }
</script>
{/literal}

<div id="js_marketplace_click_image_viewer" style="width: 630px;">
    <div id="js_marketplace_click_image_viewer_inner">
        {phrase var='contest.loading'}
    </div>
    <div id="js_marketplace_click_image_viewer_close">
        <a href="#">{phrase var='contest.close'}</a>
    </div>
</div>

<div class="yc large_item image_hover_holder">
    <div class="yc_view_image ycontest_photo">
        <ul class="list_itype">
        {if $aContest.contest_status == 1}
            <li class="itype endraft">{phrase var='contest.draft'}</li>
        {elseif $aContest.contest_status == 2}
            <li class="itype enpending">{phrase var='contest.pending'}</li>
        {elseif $aContest.contest_status == 3}
            <li class="itype endenied">{phrase var='contest.denied'}</li>
        {elseif $aContest.contest_status == 5}
            <li class="itype enclosed">{phrase var='contest.closed'}</li>
        {else}
            {if $aContest.is_feature}<li class="itype enfeatured">{phrase var='contest.featured'}</li>{/if}
            {if $aContest.is_premium}<li class="itype enpremium">{phrase var='contest.premium'}</li>{/if}
            {if $aContest.is_ending_soon}<li class="itype endinsoon">{phrase var='contest.ending_soon'}</li>{/if}
        {/if}
        </ul>

        {if $aItem.image_path}
        <a class="large_item_image js_contest_click_image no_ajax_link" href="{img server_id=$aContest.server_id return_url=true path='core.url_pic' file='contest/'.$aContest.image_path suffix='' max_width=150}" title="{$aContest.contest_name|clean*}" style="background-image: url('{img server_id=$aContest.server_id path='core.url_pic' file='contest/'.$aContest.image_path suffix='' max_width='170' class='js_mp_fix_width' return_url=true}')">
        </a>
        {else}
        <a class="large_item_image js_contest_click_image no_ajax_link js_mp_fix_width" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}" title="{$aContest.contest_name|clean*}" style="background-image:url('{$sUrlNoImagePhoto}')">
        </a>
        {/if}
    </div>
    <ul class="yc_list_action_1">
        {if $aContest.can_invite_friend}
            <li id="yncontest_photo_invite_link">
                <a href="#" title ="{phrase var='contest.invite_friends_to_this_contest'}" onclick="$Core.box('contest.showInvitePopup',800,'&contest_id={$aContest.contest_id}'); return false;">{phrase var='contest.invite'}</a>
            </li>
        {/if}
        {if $aContest.can_follow_contest}
            <li id="yncontest_photo_follow_link">
                {if !$aContest.is_followed}
                    <a href="#" title ="{phrase var='contest.follow_this_contest'}" onclick="$.ajaxCall('contest.followContest','contest_id={$aContest.contest_id}&amp;type=1', 'GET'); return false;">{phrase var='contest.follow'}</a>
                {else}
                    <a href="#" title ="{phrase var='contest.un_follow_this_contest'}" onclick="$.ajaxCall('contest.followContest','contest_id={$aContest.contest_id}&amp;type=0', 'GET'); return false;">{phrase var='contest.un_follow'}</a>
                {/if}
            </li>

        {/if}

        {if $aContest.can_favorite_contest}
            <li id="yncontest_photo_favorite_link">
                {if !$aContest.is_favorited}
                    <a href="#" title ="{phrase var='contest.favorite_this_contest'}" onclick="$.ajaxCall('contest.favoriteContest','contest_id={$aContest.contest_id}&amp;type=1', 'GET'); return false;">{phrase var='contest.favorite'}</a>
                {else}
                    <a href="#" title ="{phrase var='contest.un_favorite_this_contest'}" onclick="$.ajaxCall('contest.favoriteContest','contest_id={$aContest.contest_id}&amp;type=0', 'GET'); return false;">{phrase var='contest.un_favorite'}</a>
                {/if}
            </li>
        {/if}
    </ul>
</div>
<ul class="yc_list_action_2">

    {if $aContest.can_submit_entry}
    <li {if $aContest.type == 3 && !Phpfox::isModule('v') && !Phpfox::isModule('ultimatevideo') && !Phpfox::isModule('videochannel')}style="display:none"{/if}>
        <a class="yc_sub_entry" href="{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name action=add}" title="{phrase var='contest.submit_an_entry'}"> {phrase var='contest.submit_an_entry'} </a>
    </li>
    {/if}
        {if $aContest.is_joined}
        <li>
        <a class="yc_joined" href="#" title="{phrase var='contest.leave_this_contest'}" onclick="$.ajaxCall('contest.leaveContest', 'contest_id={$aContest.contest_id}', 'GET'); return false;">{phrase var='contest.leave'}</a>
        </li>
        {elseif $aContest.can_join_contest}
        <li>
        <a class="yc_joined" href="#" title="{phrase var='contest.join_this_contest'}" onclick="yncontest.join.showJoinContestPopup({$aContest.contest_id}, '{phrase var='contest.terms_and_conditions'}'); return false;">{phrase var='contest.join'}</a>
        </li>
        {/if}
    <li>
        <a class="yc_promotes" href="#" title="{phrase var='contest.promote_this_contest'}" onclick="$Core.box('contest.getPromoteContestBox',600,'&contest_id={$aContest.contest_id}'); return false;">{phrase var='contest.promote'}</a>
    </li>
</ul>

