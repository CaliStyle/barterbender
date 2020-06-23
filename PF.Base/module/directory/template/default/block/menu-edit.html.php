<div id="menu_edit_directory" class="page_section_menu_header">
    <ul class="action">
        {if isset($aPermission.insight)}
        <li class="yndirectory-insight {if isset($sTabView) && ($sTabView == 'insight')}active{/if}"  >
        	<a href="{url link='directory.insight.id_'.$iBusinessid}">{phrase var='insight'}</a>
        </li>
        {/if}
        {if isset($aPermission.edit)}
        <li class="yndirectory-edit-info {if isset($sTabView) && ($sTabView == 'edit')}active{/if}">
        	<a href="{url link='directory.edit.id_'.$iBusinessid}">{phrase var='edit_info'}</a>
        </li>
        {/if}
        {if isset($aPermission.coverphotos)}
        <li class="yndirectory-cover-photos {if isset($sTabView) && ($sTabView == 'cover-photos')}active{/if}">
        	<a href="{url link='directory.cover-photos.id_'.$iBusinessid}">{phrase var='cover_photos'}</a>
        </li>
        {/if}
        {if isset($aPermission.managepages)}
        <li class="yndirectory-manage-pages {if isset($sTabView) && ($sTabView == 'manage-pages')}active{/if}">
        	<a href="{url link='directory.manage-pages.id_'.{$iBusinessid}">{phrase var='manage_pages'}</a>
        </li>
        {/if}
        {if isset($aPermission.managememberroles)}
        <li class="yndirectory-manage-member-roles {if isset($sTabView) && ($sTabView == 'manage-member-roles')}active{/if}">
            <a href="{url link='directory.manage-member-roles.id_'.{$iBusinessid}">{phrase var='manage_member_roles'}</a>
        </li>
        {/if}
        {if isset($aPermission.memberrolesettings)}
        <li class="yndirectory-member-role-settings {if isset($sTabView) && ($sTabView == 'member-role-settings')}active{/if}">
            <a href="{url link='directory.member-role-settings.id_'.{$iBusinessid}">{phrase var='member_role_settings'}</a>
        </li>
        {/if}
        {if isset($aPermission.manageannouncements)}
        <li class="yndirectory-manage-announcements {if isset($sTabView) && ($sTabView == 'manage-announcements')}active{/if}">
            <a href="{url link='directory.manage-announcements.id_'.{$iBusinessid}">{phrase var='manage_announcements'}</a>
        </li>
        {/if}
        {if isset($aPermission.managemodules)}
        <li class="yndirectory-manage-modules {if isset($sTabView) && ($sTabView == 'manage-modules')}active{/if}">
            <a href="{url link='directory.manage-modules.id_'.{$iBusinessid}">{phrase var='manage_modules'}</a>
        </li>
        {/if}
        {if isset($aPermission.managebusinesstheme)}
        <li class="yndirectory-manage-business-theme {if isset($sTabView) && ($sTabView == 'manage-business-theme')}active{/if}">
            <a href="{url link='directory.manage-business-theme.id_'.{$iBusinessid}">{phrase var='manage_business_theme'}</a>
        </li>
        {/if}
        {if isset($aPermission.managepackages)}
        <li class="yndirectory-manage-packages {if isset($sTabView) && ($sTabView == 'manage-packages')}active{/if}">
            <a href="{url link='directory.manage-packages.id_'.{$iBusinessid}">{phrase var='manage_packages'}</a>
        </li>
        {/if}
        <li><a href="{url link='directory.detail.'.$iBusinessid}">{phrase var='view_this_business'}</a></li>

    </ul>
</div>
