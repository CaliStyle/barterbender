<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='manage_business_creators'}
        </div>
    </div>

    <div class="panel-body">
        <form method="post" action="{url link='admincp.directory.bcreators'}" id="js_add_creator_form" name="js_add_creator_form" >
            <p class="help-block">{phrase var='add_members_who_will_be_allowed_to_add_businesses_for_claiming'}</p>
            <div id ="directory_add_business_creator" class="table" style="border: 0">
                <div id="js_custom_search_friend"></div>
                <div id="js_custom_search_friend_placement" style="clear:both;">
                </div>
            </div>
            <input type="submit" name="val[submit]" value="{phrase var='add_creator'}" class="btn btn-primary">
        </form>
    </div>

    {if count($aCreators) > 0}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{phrase var='full_name'}</th>
                    <th class="t_center">{phrase var='options'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aCreators key=iKey item=aCreator}
            <tr>
                <td>{$aCreator|user}</td>
                <td class="t_center">
                    <a href="{$sCreatorLink}delete_{$aCreator.user_id}" title="{phrase var='remove'}" class="sJsConfirm">{phrase var='remove'}</a>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <?php if ($this->getLayout('pager')): ?>
    <div class="panel-footer">
        {pager}
    </div>
    <?php endif; ?>
    {/if}
</div>
{literal}
<style type="text/css">
	.js_temp_friend_search_form_holder ul{
        display: flex;
        flex-flow: wrap;
        border: 1px solid #eee;
        border-top: none;
        padding-top: 10px;
        padding-left: 10px;
        padding-right: 10px;
	}
    .js_temp_friend_search_form_holder ul li {
        width: 50%;
        margin-top: 0;
        min-height: 60px;
    }
    .js_temp_friend_search_form_holder ul li div .image{
        display: inline-block;
        vertical-align: middle;
    }
    .js_temp_friend_search_form_holder ul li div .image img{
        width: 50px;
        height: 50px;
    }
    .js_temp_friend_search_form_holder ul li div .image a{
        display: block;
    }
    .js_temp_friend_search_form_holder ul li div .user{
        margin-left: 8px;
    }
    span.item-user-selected {
        padding: 10px;
        display: inline-block;
        border: 1px solid #eee;
        border-radius: 20px;
        margin-top: 5px;
        margin-right: 5px;
    }
    span.item-user-selected i {
        padding-left: 5px;
    }
</style>
<script type="text/javascript">
    $Behavior.ynjpSearchFriends = function () {
        $Core.searchFriends({
            'id': '#js_custom_search_friend',
            'placement': '#js_custom_search_friend_placement',
            'max_search': 10,
            'input_name': 'bcreator',
            'is_mail': true,
            'input_type': 'multiple',
            'default_value': 'Search for member to show'
        });

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
                $.ajaxCall('directory.getLiveSearchForBusinessCreator', 'parent_id=' + $($oObj).attr('id') + '&search_for=' + $($oObj).val() + '&width=' + this._get('width') + '&total_search=' + $Core.searchFriendsInput._get('max_search'), 'GET');
                return;
            }
        };
    };
</script>
{/literal}

