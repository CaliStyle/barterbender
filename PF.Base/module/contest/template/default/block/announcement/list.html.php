<script type="text/javascript">
    $Behavior.initContestAnnoucementFormValidation = function(){l}
        yncontest.initializeValidator($('#core_js_contest_form_announcement'));
    {r};
</script>

{if $aContest.user_id==PHpfox::getUserId()}
<form method="post" name="core_js_contest_form_announcement" id="core_js_contest_form_announcement" action="{url link='contest'}{$aContest.contest_id}/{$aContest.contest_name}/">
    <input type="hidden" name="val[contest_id]" value="{$aContest.contest_id}"/>
    <input type="hidden" name="val[announcement_id]" id="yncontest_announcement_id" value=""/>
<div class="table form-group">
    <div class="table_left">
        {required}{phrase var='contest.news_headline'}
    </div>
    <div class="table_right">
        <input type="text" class="yncontest_add_announcement required form-control" name="val[headline]" id='contest_announcement_headline'/>
    </div>
</div>

<div class="table form-group">
    <div class="table_left">
        {phrase var='contest.link'}
    </div>
    <div class="table_right">
        <input type="text" name="val[link]" class="yncontest_add_announcement url form-control" id='contest_announcement_link'/>
        <div class="extra_info">{phrase var='contest.example_http_www_yourwebsite_com'}</div>
    </div>
</div>

<div class="table form-group">
    <div class="table_left">
        {required}{phrase var='contest.content'}
    </div>
    <div class="table_right">
        <textarea class="form-control" rows="4" cols="50" class="yncontest_add_announcement required" name="val[content]" id='contest_announcement_content'></textarea>
    </div>
</div>

{required} {phrase var='contest.required_fields'}
<div class="table_clear">
    <input type="submit" value="{phrase var='contest.post'}" class="button btn btn-primary btn-sm" id='contest_add_announcement'/>

    <div id="contest_update_announcement" style="display: none">
        <input type="submit" name="val[update_announcement]" class="button btn btn-primary btn-sm" value="{phrase var='contest.update'}"/>
        &nbsp;&nbsp;
        <input type="reset" class="button btn btn-default btn-sm" value="{phrase var='contest.cancel'}" onclick="yncontest.announcement.cancelEditAnnouncement();"/>
    </div>
</div>
</form>
{/if}

<div class="yc_announcement">
{foreach from=$aAnnouncement item=Announcement}
<div class="yc_announcement_item" id='yc_announcemet_{$Announcement.announcement_id}'>
    <h4 id='contest_headline_{$Announcement.announcement_id}'>{$Announcement.headline}</h4>
	<p class="extra_info">{$Announcement.time_stamp|date:'contest.contest_time_stamp_announcement'}</p>
	<div class="m_4" id='contest_content_{$Announcement.announcement_id}'>{$Announcement.content|parse|shorten:'350':'comment.view_more':true|split:350}</div>
    {if $Announcement.link}
        <div >
    		{phrase var='contest.more_at'}: <a href="{$Announcement.link}" id='contest_link_{$Announcement.announcement_id}'>{$Announcement.link}</a>
    	</div>
    {/if}
    {if $aContest.user_id == Phpfox::getUserId()}
        <ul class="actions">
        <li><a href="JavaScript:void(0);" onclick="yncontest.announcement.editAnnouncement({$Announcement.announcement_id})">{phrase var='contest.edit'}</a> </li>
        <li> / </li>
        <li><a href="JavaScript:void(0);" onclick="yncontest.announcement.deleteAnnouncement({$Announcement.announcement_id}, '{phrase var='contest.are_you_sure_you_want_to_delete_this_announcement'}');">{phrase var='contest.delete'}</a></li>
         </ul>
     {/if}
</div>
{/foreach}
{if count($aAnnouncement)==0 && $aContest.user_id!=PHpfox::getUserId()}
<div>
	{phrase var='contest.no_announcement_found'}
</div>
{/if}
</div>
