<div>{phrase var='add_member_who_will_be_new_owner_of_the_business'}</div>
<span id="yndirectory_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
<input type="hidden" value="{$iBusinessId}" id="owner_business_id">

<div id ="directory_add_business_creator"> 
    <div class="go_left" style="margin-right:5px;">
        <div id="js_custom_search_friend"></div>
        
    </div>
    <div style="margin-top: 10px;">       
        <div id="js_custom_search_friend_placement" style="clear:both;">

        </div>
    </div><div class="clear"></div>
</div>
<br />
<button class="btn btn-sm btn-primary" onclick="{if $frontend}yndirectory.transferownerBusiness();{else}managebusiness.transferownerBusiness();{/if} return false;">{phrase var='ok'}</button>

{literal}
<script type="text/javascript">
    /* re-define */
    $Core.searchFriendsInput.aLiveUsers = {};
    $Core.searchFriendsInput.processClick = function ($oObj, $iUserId) {
        if ($("#js_custom_search_friend_placement").length > 0) {
            if ($("#js_custom_search_friend_placement input[name='owner[]']").length > 0) {
                $('.js_custom_search_friend_holder ul').html('');
            }
        }
        if (!isset(this.aFoundUsers[$iUserId])) {
            return false;
        }

        if (isset(this.aLiveUsers[$iUserId])) {
            return false;
        }

        this.aLiveUsers[$iUserId] = true;
        $Behavior.reloadLiveUsers = function () {
            $Core.searchFriendsInput.aLiveUsers = {};
            $Behavior.reloadLiveUsers = function () {
            }
        };
        this.bNoSearch = false;

        var $aUser = this.aFoundUser = this.aFoundUsers[$iUserId];
        var $oPlacement = $(this._get('placement'));

        //$($oObj).parents('.js_friend_search_form:first').find('.js_temp_friend_search_input').val('').focus();
        $($oObj).closest('.js_temp_friend_search_form').html('').hide();

        var $sHtml = '';
        $sHtml += '<li>';

        $sHtml += '<a href="#" class="friend_search_remove" title="Remove" onclick="$Core.searchFriendsInput.removeSelected(this, ' + $iUserId + ');  return false;">Remove</a>';
        if (!this._get('inline_bubble')) {
            $sHtml += '<div class="friend_search_image">' + $aUser['user_image'] + '</div>';
        }
        $sHtml += '<div class="friend_search_name">' + $aUser['full_name'] + '</div>';
        if (!this._get('inline_bubble')) {
            $sHtml += '<div class="clear"></div>';
        }
        $sHtml += '<div><input type="hidden" name="' + this._get('input_name') + '[]" value="' + $aUser['user_id'] + '" /></div>';
        $sHtml += '</li>';
        this.sHtml = $sHtml;

        $('.js_custom_search_friend_holder').css('border', '1px #ccc solid');

        if (empty($oPlacement.html())) {
            $oPlacement.html('<div class="js_custom_search_friend_holder"><ul' + (this._get('inline_bubble') ? ' class="inline_bubble"' : '') + '></ul>' + (this._get('inline_bubble') ? '<div class="clear"></div>' : '') + '</div>');
        }

        if (this._get('onBeforePrepend')) {
            this._get('onBeforePrepend')(this._get('onBeforePrepend'));
        }

        $oPlacement.find('ul').prepend(this.sHtml);

        if (this._get('onclick')) {
            this._get('onclick')(this._get('onclick'));
        }

        if (this._get('global_search')) {
            window.location.href = $aUser['user_profile'];
            $($oObj).closest('.js_temp_friend_search_form').html('').hide();
        }

        this.aFoundUsers = {};

        if (this._get('inline_bubble')) {
            $('#' + this._get('search_input_id') + '').val('').focus();
        }

        return false;
    };

    $Core.searchFriendsInput.removeSelected = function ($oObj, $iUserId) {
        if (isset(this.aLiveUsers[$iUserId])) {
            delete this.aLiveUsers[$iUserId];
        }
        $($oObj).parents('li:first').remove();
        $('.js_custom_search_friend_holder').css('border', 'none');
    };

    $Core.searchFriends({
        'id': '#js_custom_search_friend',
        'placement': '#js_custom_search_friend_placement',
        'max_search': 10,
        'input_name': 'owner',
        'is_mail': true
    });

    $Core.searchFriendsInput.buildFriends = function ($oObj) {
        $.ajaxCall('directory.getUserForTransferOwner', '', 'GET');
    };

    $Core.searchFriendsInput.getFriends = function ($oObj) {

        if (empty($oObj.value)) {
            this.closeSearch($oObj);
            return;
        }
        if (this.bNoSearch) {
            this.bNoSearch = false;
            return;
        }
        if (isset(this.aParams['is_mail']) && this.aParams['is_mail'] == true) {
            $.ajaxCall('directory.getLiveSearchForTranferOwner', 'parent_id=' + $($oObj).attr('id') + '&search_for=' + $($oObj).val() + '&width=' + this._get('width') + '&total_search=' + $Core.searchFriendsInput._get('max_search'), 'GET');
            return;
        }
    };
</script>
{/literal}