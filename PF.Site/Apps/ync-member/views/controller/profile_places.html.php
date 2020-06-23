<div class="table js_custom_groups js_custom_group_999" {if $isHideForm}style="display: none;"{/if}>
    <!-- TODO add class .active when add item-->
    <div class="ynmember-profile-place-item form-group{if count($aStudyPlaces)} active{/if}">
        <label class="text-uppercase">{_p var='Schools'}:</label>

        <div id="ynmember_place_study">
            {foreach from=$aStudyPlaces item=aPlace}
            {template file='ynmember.block.entry_profile_place}
            {/foreach}
        </div>

        <div class="ynmember_edit_place_block clearfix">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <span>{_p var='Add a school'}</span>
            <a href="{url link='ynmember.add_place' type='study'}" class="popup"></a>
        </div>
    </div>

    <div class="ynmember-profile-place-item form-group{if count($aWorkPlaces)} active{/if}">
        <label class="text-uppercase">{_p var='Workplaces'}:</label>

        <div id="ynmember_place_work">
        {foreach from=$aWorkPlaces item=aPlace}
        {template file='ynmember.block.entry_profile_place}
        {/foreach}
        </div>

        <div class="ynmember_edit_place_block clearfix">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <span>{_p var='Add a workplace'}</span>
            <a href="{url link='ynmember.add_place' type='work'}" class="popup"></a>
        </div>
    </div>

    <div class="ynmember-profile-place-item form-group{if count($aLivingPlaces)} active{/if}">
        <label class="text-uppercase">{_p var="Current place I'm living"}:</label>

        <div id="ynmember_place_living">
        {foreach from=$aLivingPlaces item=aPlace}
        {template file='ynmember.block.entry_profile_place}
        {/foreach}
        </div>

        <div class="ynmember_edit_place_block clearfix">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <span>{_p var='Add a place'}</span>
            <a href="{url link='ynmember.add_place' type='living'}" class="popup"></a>
        </div>
    </div>

    <div class="ynmember-profile-place-item form-group{if count($aLivedPlaces)} active{/if}">
        <label class="text-uppercase">{_p var='Places I Lived'}:</label>

        <div id="ynmember_place_lived">
        {foreach from=$aLivedPlaces item=aPlace}
        {template file='ynmember.block.entry_profile_place}
        {/foreach}
        </div>

        <div class="ynmember_edit_place_block clearfix">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <span>{_p var='Add a place'}</span>
            <a href="{url link='ynmember.add_place' type='lived'}" class="popup"></a>
        </div>
    </div>
</div>