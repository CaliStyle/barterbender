<form method="post" action="" id="js_change_role_id">
<input type="hidden" name='business_id' value="{$iBusinessId}">
<input type="hidden" name='user_id' value="{$iUserId}">
{if count($aMemberRoles)}
	{foreach from=$aMemberRoles item=aMemberRole}
		<div class="yndirectory-change-role-item">
			<div class="yndirectory-ratio">
				<input type="radio" name="new_role_id" value={$aMemberRole.role_id} {if $aMemberRole.role_id == $aCurrentRoleOfUser.role_id }checked="checked"{/if}>
			</div>
			<div class="yndirectory-form-title">
				{$aMemberRole.role_title}
			</div>
		</div>
	{/foreach}
{/if}
<div class="yndirectory-button">
	<input type="button" name="change_role_id" id="change_role_id" onclick="changeRole()" value="{phrase var='change_role'}">
</div>
</form>
{literal}
<script type="text/javascript">
;
var changeRole = function(){
	$.ajaxCall('directory.changeMemberRoleId',$('#js_change_role_id').serialize(), 'post');
	js_box_remove(this);
	return false;
};
</script>
{/literal}