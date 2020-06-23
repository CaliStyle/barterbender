<div id='yndirectory_manage_announcements'>		
    {if $sView == 'maincontent' }
        <form method="post" action="{url link='directory.manage-announcements.id_'.$iBusinessid}" id="js_manage_announcements" onsubmit="" enctype="multipart/form-data">
        <input type="hidden" name="val[business_id]" value="{$iBusinessid}" >

        <a href="{url link='directory.manage-announcements.id_'.$iBusinessid.'.view_add'}" class="btn btn-sm btn-primary" >{phrase var='post_new_announcements'}</a>
        <span class="help-block">({phrase var='total_announcement_s_total' total={$iCnt})</span>

        {if count($aAnnouncements)}
            <table class="yndirectory-table-full">
            <tr>
                <th>{phrase var='title'}</th>
                <th>{phrase var='user'}</th>
                <th>{phrase var='date'}</th>
                <th>{phrase var='options'}</th>
            </tr>
                {foreach from=$aAnnouncements item=aAnnouncement}
                    <tr>
                        <td>{$aAnnouncement.announcement_title}</td>

                        {if $aAnnouncement.iCntReadBy}
                            <td class="tip_trigger">
                                <span id='read_by_{$aAnnouncement.announcement_id}'>{phrase var='read_by_total_users' total=$aAnnouncement.iCntReadBy} </span>
                                <div class="yndirectory-announcement-readby" style="display:none;">
                                    <div class ="description_content">
                                        {foreach from=$aAnnouncement.readby item=aUser}
                                            <div class="yndirectory-announcement-readby-item">
                                                {$aUser.avatar}
                                                {$aUser|user}
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            </td>
                        {else}
                            <td></td>
                        {/if}

                        <td>{$aAnnouncement.timestamp}</td>
                        <td>
                            <a href="{url link='directory.manage-announcements.id_'.$iBusinessid.'.view_edit.idpost_'.$aAnnouncement.announcement_id}" >{phrase var='edit'}</a>
                            /
                            <a style='cursor:pointer;'class="yndirectory_delete_announcement" value="{$aAnnouncement.announcement_id}">{phrase var='delete'}</a>
                        </td>
                    </tr>
                {/foreach}
            </table>
        {else}
            <div class="help-block">
                {phrase var='there_are_no_announcements'}
            </div>
        {/if}

    </form>

    {elseif $sView == 'add'}
        {$sCreateJs}
        <form method="post" action="{url link='directory.manage-announcements.id_'.$iBusinessid.'.view_add'}" id="js_add_announcements" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">

        <input type="hidden" name="val[business_id]" value="{$iBusinessid}" >

        <div class="table form-group">
            <div class="table_left">
                {phrase var='title'}
            </div>
            <div class="table_right">
                <input class="form-control" type="text" name="val[announcement_title]" value="" >
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                {phrase var='body'}
            </div>
            <div class="table_right">
                {editor id='announcement_content'}
            </div>
        </div>

        <div class="yndirectory-button main_break">
            <button type="submit" class="btn btn-sm btn-primary" name="val[add_announcements]" id="add_announcements" value="{phrase var='post'}">{phrase var='post'}</button>
        </div>
        </form>
    {elseif $sView == 'edit'}
        {$sCreateJs}
        <form method="post" action="{url link='directory.manage-announcements.id_'.$iBusinessid.'.view_edit.idpost_'.$idpost}" id="js_add_announcements" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">

        <input type="hidden" name="val[business_id]" value="{$iBusinessid}" >
        <input type="hidden" name="val[idpost]" value="{$idpost}" >

        <div class="table form-group">
            <div class="table_left">
                {phrase var='title'}
            </div>
            <div class="table_right">
                <input class="form-control" type="text" name="val[announcement_title]" id="announcement_title" value="{if isset($aForms.announcement_title)}{$aForms.announcement_title}{/if}"  >
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                {phrase var='body'}
            </div>
            <div class="table_right">
                {editor id='announcement_content'}
            </div>
        </div>

        <div class="yndirectory-button main_break">
            <button class="btn btn-sm btn-primary" type="submit" name="val[edit_announcements]" id="edit_announcements" value="{phrase var='save_changes'}">{phrase var='save_changes'}</button>
        </div>
        </form>
    {/if}
</div>

{literal}
<script type="text/javascript">

$Behavior.initNewsToolTip = function()
{
	if($(".tip_trigger").length) {
		$(".tip_trigger").live({
			hover: function () {
				tip = $(this).find('.yndirectory-announcement-readby');
				tip.show();
				$('.tip').css("z-index", 100000);
				$('.tip').css("position", "absolute");
			},

			mouseleave: function () {
				tip = $(this).find('.yndirectory-announcement-readby');
				tip.hide();
			}
		});
	}
};

$Behavior.deleteAnnouncement = function(){ 	

	$('.yndirectory_delete_announcement').click(function(){
			var iAnnouncement = $(this).attr('value');
            $Core.jsConfirm({message: oTranslations['directory.are_you_sure_you_want_to_delete_this_announcement']}, function () {
                yndirectory.deleteAnnouncement(iAnnouncement);
            }, function () {

            });
            return false;
        });
};

</script>
{/literal}