{literal}
<style>
	#ynjobposting_company_image img
	{
		max-width: 319px !important;
	}

</style>
{/literal}
{if count($aImages) > 1}
<div class="js_box_thumbs_holder2">
{/if}
    <div class="jobposting_image_holder">
        <div class="jobposting_image">
			{if $aCompany.image_path != ""}
                <a id="ynjobposting_company_image"
                   onclick="tb_show('', $(this).attr('href'), $(this));return false;"
                   class="js_jobposting_click_image "
                   href="{img return_url=true
                   server_id=$aCompany.server_id
                   title=$aCompany.name
                   path='core.url_pic'
                   file="jobposting/".$aCompany.image_path suffix=''}">


                    {img thickbox=true
                    server_id=$aCompany.server_id
                    title=$aCompany.name
                    path='core.url_pic'
                    file="jobposting/".$aCompany.image_path
                    suffix=''
                    width='100%'
                    max_width='173'
                    max_height='200'}
                </a>

			{else}
                <a id="ynjobposting_company_image">
                    <img src="{$coreUrlModule}jobposting/static/image/default/default_ava.png" alt="">
                </a>
			{/if}



        </div>
        {if count($aImages) > 1}
        <div class="jobposting_view_image_extra js_box_image_holder_thumbs">
            <ul class="clearfix">
            	{foreach from=$aImages name=images item=aImage}
            		<li>
						<a href="{img
						return_url=true
						thickbox=true server_id=$aImage.server_id
						title=$aCompany.name path='core.url_pic'
						file="jobposting/".$aImage.image_path suffix=''}"
						   onclick="tb_show('', $(this).attr('href'), $(this));return false;"
							>
            				{img thickbox=true server_id=$aImage.server_id title=$aCompany.name path='core.url_pic' file="jobposting/".$aImage.image_path suffix='' width='50' height='50'}
						</a>
            		</li>
            	{/foreach}
            </ul>
            <div class="clear"></div>
        </div>
        {/if}
    </div>
{if count($aImages) > 1}
</div>
{/if}


{if $ControllerName=="jobposting.view"}
<div class="ynjp_detail_links block">
	<ul class="action">
            {if PHpfox::getUserId()>0}
		<li><a href="javascript:void(0);" onclick="tb_show('{phrase var='invite_friends'}', $.ajaxBox('jobposting.blockInvite', 'width=800&height=350&type=job&id={$aJob.job_id}'));">{phrase var='invite_friends'}</a></li>
		<li id="js_jp_follow_link"><a href="#" onclick="$.ajaxCall('jobposting.changeFollow', 'type=job&id={$aJob.job_id}&current={$iIsFollowed}'); return false;">{if $iIsFollowed}{phrase var='unfollow'}{else}{phrase var='follow'}{/if}</a></li>
		<li id="js_jp_favorite_link"><a href="#" onclick="$.ajaxCall('jobposting.changeFavorite', 'type=job&id={$aJob.job_id}&current={$iIsFavorited}'); return false;">{if $iIsFavorited}{phrase var='unfavorite'}{else}{phrase var='favorite'}{/if}</a></li>
            {/if}
		<li><a href="javascript:void(0);" onclick="tb_show('{phrase var='promote_job'}', $.ajaxBox('jobposting.blockPromoteJob', 'width=550&height=350&id={$aJob.job_id}'));">{phrase var='promote_job'}</a></li>
	</ul>
</div>
{else}
<div class="ynjp_detail_links block">
	<ul class="action">
            {if PHpfox::getUserId()>0}
		<li><a href="javascript:void(0);" onclick="tb_show('Invite Friends', $.ajaxBox('jobposting.blockInvite', 'width=800&height=350&type=company&id={$aCompany.company_id}'));">{phrase var='invite_friends'}</a></li>
		<li id="js_jp_follow_link"><a href="#" onclick="$.ajaxCall('jobposting.changeFollow', 'type=company&id={$aCompany.company_id}&current={$iIsFollowed}'); return false;">{if $iIsFollowed}{phrase var='unfollow'}{else}{phrase var='follow'}{/if}</a>
		<li id="js_jp_favorite_link"><a href="#" onclick="$.ajaxCall('jobposting.changeFavorite', 'type=company&id={$aCompany.company_id}&current={$iIsFavorited}'); return false;">{if $iIsFavorited}{phrase var='unfavorite'}{else}{phrase var='favorite'}{/if}</a></li>
           {/if}
	</ul>
</div>
{if PHpfox::getUserId()>0 && $hideBtnWorkingCompany == false}
<div id="join_leave_company">
	{if $iCompany==0}
		<input type="button" class="btn btn-success" onclick="workingCompany({$aCompany.company_id},1) " value="{phrase var='working_at_this_company'}"/>
	{else}
		<input type="button" class="btn btn-success" onclick="workingCompany({$aCompany.company_id},0)" value="{phrase var='leave_this_company'}"/>
	{/if}	
</div>
{/if}

{if $iCompany==-1}
	<div class="mb-2">
		<input style="width: 100%;" type="button" class="btn btn-warning" value="{phrase var='waiting_for_approve'}"/>
	</div>
{/if}
{/if}

{literal}
<script type="text/javascript">
	function workingCompany(company_id, working){
		
		if(working == 1)
		{
			tb_show('{/literal}{phrase var='notice'}{literal}', $.ajaxBox('jobposting.showPopupConfirmYesNo', 'height=300&width=300&function=workingcompany&company_id='+company_id+'&working='+working+'&phare=do_you_want_to_work_this_company'));
		}	
		else{
			tb_show('{/literal}{phrase var='notice'}{literal}', $.ajaxBox('jobposting.showPopupConfirmYesNo', 'height=300&width=300&function=workingcompany&company_id='+company_id+'&working='+working+'&phare=do_you_want_to_leave_this_company'));
		}
		return false;
		
	}
</script>
{/literal}
