<ul class="left_contest_info">
	<li>
		<span>{phrase var='contest.contest_duration'}</span>
		<span>
        {if $aContest.contest_timeline == 'end' || $aContest.is_manual_closed}
            {phrase var='contest.end'}
        {elseif $aContest.contest_timeline == 'on_going'}
            {$aContest.contest_countdown}
        {elseif $aContest.contest_timeline == 'opening'}
            {phrase var='contest.opening'}
        {/if}
        </span>
	</li>
	<li>
		<span>{phrase var='contest.submit_entries'}</span>
		<span>
        {if $aContest.submit_timeline == 'end' || $aContest.is_manual_closed}
            {phrase var='contest.end'}
        {elseif $aContest.submit_timeline == 'on_going'}
            {$aContest.submit_countdown}
        {elseif $aContest.submit_timeline == 'opening'}
            {phrase var='contest.opening'}
        {/if}
        </span>
	</li>
	<li>
		<span>{phrase var='contest.voting'}</span>
		<span>
        {if $aContest.vote_timeline == 'end' || $aContest.is_manual_closed}
            {phrase var='contest.end'}
        {elseif $aContest.vote_timeline == 'on_going'}
            {$aContest.vote_countdown}
        {elseif $aContest.vote_timeline == 'opening'}
            {phrase var='contest.opening'}
        {/if}
        </span>
	</li>
</ul>