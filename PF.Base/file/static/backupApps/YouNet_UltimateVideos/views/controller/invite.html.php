<div class="block ynag_invite_friend">
    <label>{_p('invite_friends')}</label>
    <div class="content">
        {if $type == 1}
            {module name="friend.search" input="invite" friend_item_id=$aItem.video_id}
        {else}
            {module name="friend.search" input="invite" friend_item_id=$aItem.playlist_id}
        {/if}
        <form method="post"
              action="{if $type==1}{permalink module='ultimatevideo.invite' id=$aItem.video_id type=1}{else}{permalink module='ultimatevideo.invite' id=$aItem.playlist_id type=2}{/if}"
              id="ynag_invite_friend_form" enctype="multipart/form-data" class="">
            <div class="ynag_new_guest_list" style="display:none">
                <div class="block">
                    <div class="title">{_p('new_guest_list')}</div>
                    <div class="content">
                        <div class="table_right">
                            <div class="label_flow" class="form-control">
                                <div id="js_selected_friends"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h3>{_p('add_a_personal_message')}</h3>
            <div class="table form-group mt-2">
                <label>
                    {_p('subject')}:
                </label>
                    <input type="text" name="val[subject]" class="form-control" value="{$subject}" id="subject"
                           maxlength="255"/>
            </div>
            <div class="table form-group">
                <label>
                    {_p('message')}:
                </label>
                <textarea cols="40" rows="8" name="val[personal_message]" class="form-control">{$message}</textarea>
            </div>
            <div class="clear"></div>
            <div class="p_top_8">
                <button type="submit" name="val[submit_invite]" id="btn_invitations_submit"
                        value="send_invite" class="btn btn-sm btn-primary"
                        onclick="return checkSelectedFriendYNUV(this);">
                    {_p('send_invitations')}
                </button>
            </div>
        </form>
    </div>
</div>
{literal}
    <script type="text/javascript">
        function checkSelectedFriendYNUV(obj) {
            if ($("[name='val[invite][]'").length == 0 || $("[name='val[invite][]'").val() == "") {
                return false;
            }
            return true;
        }
    </script>
{/literal}
