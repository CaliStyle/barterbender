{if $aUser.places.living_name}
<div class="ynmember_address ynmember_list_item">
    <span class="overflow"><i class="fa fa-map-marker" aria-hidden="true"></i>{_p var='Live in'} </span>
    <a href="{$aUser.places.living_place|ynmember_place}" class="max-width" title="{$aUser.places.living_name}">{$aUser.places.living_name}</a>
</div>
{/if}
{if $aUser.places.work_name}
<div class="ynmember_address ynmember_list_item">
    <span class="overflow"><i class="fa fa-briefcase" aria-hidden="true"></i>{_p var='Work at'} </span>
    <a href="{$aUser.places.work_place|ynmember_place}" class="max-width" title="{$aUser.places.work_name}">{$aUser.places.work_name}</a>
</div>
{/if}
{if $aUser.places.study_name}
<div class="ynmember_study ynmember_list_item">
    <span class="overflow"><i class="fa fa-graduation-cap" aria-hidden="true"></i>{_p var='Studied at'} </span>
    <a href="{$aUser.places.study_place|ynmember_place}" class="max-width" title="{$aUser.places.study_name}">{$aUser.places.study_name}</a>
</div>
{/if}

{if $aUser.total_groups}
<div class="ynmember_group ynmember_list_item {if $aUser.total_groups > 1} have_more{/if}">
    <span class="overflow"><i class="fa fa-users" aria-hidden="true"></i>{_p var='Groups'}:</span>
    <a href="{url link='groups.'$aUser.groups[0].page_id}" class="max-width">{$aUser.groups[0].title}</a>
    {if ($aUser.total_groups > 1)}
    <span class="dropdown">
        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">(+{$aUser.total_groups|ynmember_subtract} {_p var='others'})</a>
        <ul class="ynmember_group_popup dropdown-menu dropdown-menu-left">
            {foreach from=$aUser.groups name=ynmembergroups item=aGroup}
            {if $phpfox.iteration.ynmembergroups > 1}
            <li><a href="{url link='groups.'$aGroup.page_id}">{$aGroup.title}</a></li>
            {/if}
            {/foreach}
        </ul>
    </span>
    {/if}
</div>
{/if}
{if $aUser.total_pages}
<div class="ynmember_group ynmember_list_item {if $aUser.total_pages > 1} have_more{/if}">
    <span class="overflow"><i class="fa fa-users" aria-hidden="true"></i>{_p var='Pages'}:</span>
    <a href="{url link='pages.'$aUser.pages[0].page_id}" class="max-width">{$aUser.pages[0].title}</a>
    {if ($aUser.total_pages > 1)}
    <span class="dropdown">
        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">(+{$aUser.total_pages|ynmember_subtract} {_p var='others'})</a>
        <ul class="ynmember_group_popup dropdown-menu dropdown-menu-left">
            {foreach from=$aUser.pages name=ynmemberpages item=aPage}
            {if $phpfox.iteration.ynmemberpages > 1}
            <li><a href="{url link='pages.'$aPage.page_id}">{$aPage.title}</a></li>
            {/if}
            {/foreach}
        </ul>
    </span>
    {/if}
</div>
{/if}