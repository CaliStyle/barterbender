<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="group-reassign-owner" id="js_reassign_owner_page">
    <div class="item-info mt-1 mb-1">
        {_p var='reassign_owner_page_notice'}
        <hr>
        <div class="mt-1 mb-1">
            {_p var='current_owner'}: {$aOwner|user}
        </div>
    </div>
    {module name='friend.search-small' input_name='owner' input_type='single' include_current_user=$bIncludeCurrentUser}
    <div class="mt-2">
        <button class="btn btn-primary" id="js_page_reassign_submit" onclick="
         $Core.jsConfirm({l}message: '{_p var='are_you_sure'}'{r}, function () {l}
                $.ajaxCall('pages.reassignOwner','page_id={$iPageId}&user_id='+ $('#js_reassign_owner_page #search_friend_single_input').val()); return false;
            {r}, function () {l}{r});
        ">{_p var='submit'}</button>
    </div>
</div>

{literal}
<script type="text/javascript">
    $(document).on('DOMSubtreeModified','#js_reassign_owner_page', function() {
        setTimeout(function(){
            var value = $('#js_reassign_owner_page #search_friend_single_input').val();
            if (value != "") {
                $('#js_reassign_owner_page #js_custom_search_friend').hide();
            } else {
                $('#js_reassign_owner_page #js_custom_search_friend').show();
            }
        }, 0);
    });
</script>
{/literal}