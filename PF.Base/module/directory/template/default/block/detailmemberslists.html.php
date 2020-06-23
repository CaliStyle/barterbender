<div class="yndirectory-member-list">
	{if count($aMembers) < 1}
		<div class="help-block">
		{phrase var='no_users_found'}
		</div>
	{/if}
	<div class="yndirectory-member-list-container">
	{foreach from=$aMembers name=member item=aMember}
		<div class="yndirectory-member-item">
			<div class="yndirectory-member-row-image">
				{img user=$aMember suffix='_50_square' max_width=50 max_height=50}
                {if ($aBusiness.user_id != $aMember.user_id && $canChangeRoles) || ($aBusiness.user_id == Phpfox::getUserId() && $aBusiness.user_id != $aMember.user_id)}
				 <div class="item_bar_action_holder">
                    <a data-toggle="dropdown" class="item_bar_action"><span>{_p var='actions'}</span><i class="ico ico-gear-o"></i></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                        {if $canChangeRoles && $aBusiness.user_id != $aMember.user_id}
                            <a href="javascript:void(0)" class="button" onclick="tb_show(oTranslations['directory.change_role'], $.ajaxBox('directory.showPopupChangeMemberRole', 'height=300&width=530&user_id='+{$aMember.user_id}+'&business_id=' + {$aMember.business_id} ));">{phrase var='change_role'}</a>
                        {/if}
                        </li>
                        <li>
                        {if $aBusiness.user_id == Phpfox::getUserId() && $aBusiness.user_id != $aMember.user_id}
                            <a href="javascript:void(0)" class="button" onclick="yndirectory.confirmDeleteMemberOfBusiness({$aMember.user_id},{$aMember.business_id});">{_p var='directory_delete_member'}</a>
                        {/if}
                        </li>
                    </ul>
                 </div>
                {/if}
			</div>
			<div class="item-user">
				{$aMember|user}
			</div>
            {if $aMember.role_title == 'Admin'}
            <div class="item-role">
				<span class="ico ico-crown"></span>
				{$aMember.role_title}
			</div>
            {/if}
		</div>
	{/foreach}
	</div>

	<div class="clear"></div>
	{module name='directory.paging'}	
</div>
{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}