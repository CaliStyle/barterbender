{if count($aTodayBirthdays)}
<div class="ynmember_block_item_birthday {if count($aTodayBirthdays) >= 2}two-item{/if}{if !Phpfox::isUser()} none-login{/if}">
	<div class="ynmember_day text-uppercase"><span>{_p('today')}</span> <span>({$sToday})</span></div>
    {if $iRemainToday}<span class="ynmember_number_more"><a href="{url link='ynmember.birthday'}" style="color: white">+{$iRemainToday}</a></span>{/if}
	<div class="ynmember_block_item_inner_parent clearfix">
		{foreach from=$aTodayBirthdays name=users item=aUser}
		    {template file='ynmember.block.entry_birthday'}
		{/foreach}
	</div>
    {if Phpfox::isUser()}
	<a href="{url link='ynmember.birthdaywish'}" title="wishes all" class="ynmember_wishes_all capitalize btn btn-primary popup"><i class="fa fa-birthday-cake" aria-hidden="true"></i> {_p('Send Birthday Wishes')}</a>
    {/if}
</div>
{/if}

{if count($aUpcomingBirthdays)}
<div class="ynmember_birthday_upcoming">
	<div class="ynmember_upcoming_title uppercase fw-bold">{_p('upcoming')}</div>
	<ul>
	    {foreach from=$aUpcomingBirthdays name=users item=aUser}
			<li class="clearfix">
		        {template file='ynmember.block.entry_birthday_upcoming'}
		    </li>
	    {/foreach}
	</ul>
    {if $iRemainUpcoming}
	<a href="{url link='ynmember.birthday'}" class="ynmember_more capitalize btn btn-default btn-block" title="wishes">{_p('more upcoming birthdays')}</a>
    {/if}
</div>
{/if}
