<div id="ynmember_place_{$aPlace.place_id}" class="ynmember_add_place_block{if $aPlace.type=='work'} workplaces{elseif $aPlace.type=='living'} live{elseif $aPlace.type=='lived'} lived{/if} clearfix">
    <i class="fa fa-graduation-cap" aria-hidden="true"></i>
    <div class="ynmember_add_place_block_info">
    {if $aPlace.type == 'study'}
        <strong>{_p var='School'}:</strong>
        <span>{$aPlace.location_title}</span>
    {elseif $aPlace.type == 'work'}
        <strong>{_p var='Company'}:</strong>
        <span>{$aPlace.location_title}</span>
    {/if}
    </div>
    <div class="ynmember_add_place_block_info">
        <strong>{_p var='Location'}:</strong>
        <span>{$aPlace.location_address}</span>
        {if $aPlace.type == 'study'}
        <label>
            <input disabled type="checkbox"{if $aPlace.current} checked{/if}>{_p var='I currently study here'}
        </label>
        {elseif $aPlace.type == 'work'}
        <label>
            <input disabled type="checkbox"{if $aPlace.current} checked{/if}>{_p var='I currently work here'}
        </label>
        {/if}
    </div>
    <div class="dropdown">
        <i class="fa fa-pencil-square-o dropdown-toggle" aria-hidden="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="{url link='ynmember.add_place' type=$aPlace.type id=$aPlace.place_id}" onclick="closeEditPlace(this);" class="popup">{_p var='Edit'}</a></li>
            <li>
                <a href="javascript:void(0)" class="delete" onclick="ynmember.deletePlace({$aPlace.place_id})">
                {_p var='Delete'}
                </a>
            </li>
        </ul>
    </div>
</div>