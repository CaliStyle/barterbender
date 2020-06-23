<div id='yndirectory_manage_member_roles'>
	<form method="post" action="{url link='directory.manage-member-roles.id_'.$iBusinessid}" id="js_manage_member_roles" onsubmit="" enctype="multipart/form-data">

			<input type="hidden" name="val[business_id]" value="{$iBusinessid}" >
			
			<div class="yndirectory-table">
				<div class="yndirectory-th">
					<span>{phrase var='role_title'}</span>
					<span>{phrase var='options'}</span>
				</div>
				{foreach from=$aMemberRoles item=aRole}
					<div class="yndirectory-tr">
						<span>{$aRole.role_title}</span>
						<span>	
						{if !$aRole.is_default}
							<a style="cursor:pointer;" onclick="tb_show(oTranslations['directory.edit_role'], $.ajaxBox('directory.addNewRoleBlock', 'height=300&width=530&business_id='+{$iBusinessid}+'&role_id='+{$aRole.role_id}));">{phrase var='edit'}</a>
							/
							<a style="cursor:pointer;" class="yndirectory_delete_member_role" value="{$aRole.role_id}" name="{$iBusinessid}">{phrase var='delete'}</a>
						{/if}
						</span>
					</div>
				{/foreach}
			</div>
			<div class="main_break">
				<a style="cursor:pointer;" class="btn btn-sm btn-primary" onclick="tb_show(oTranslations['directory.add_new_role'], $.ajaxBox('directory.addNewRoleBlock', 'height=300&width=530&business_id='+{$iBusinessid}));">{phrase var='add_new_role'}</a>
			</div>
		</form>
</div>

{literal}
<script type="text/javascript">
;
$Behavior.deleteRolemember = function () {

    $('.yndirectory_delete_member_role').click(function () {
        var iRoleId = $(this).attr('value');
        var iBusinessId = $(this).attr('name');
        $Core.jsConfirm({message: oTranslations['directory.confirm_delete_role_member']}, function () {
            yndirectory.deleteRoleMember(iRoleId, iBusinessId);
        }, function () {

        });
        return false;
    });
};

</script>
{/literal}