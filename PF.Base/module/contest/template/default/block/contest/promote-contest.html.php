<form id="yncontest_promote_contest_form">
	<div class="promote_contest_form_top">
		<div class="table_right">
			<textarea class="form-control" id="yncontest_promote_contest_badge_code_textarea" readonly="readonly" cols="40" rows="15" style="height:150px;">{$sBadgeCode}</textarea>
		</div>
		<ul class="checklist_grp vertical">
			<li>
				<label><input type="checkbox" checked ="true" name="val[photo]" onclick="$('#yncontest_promote_contest_form').ajaxCall('contest.changePromoteBadge', 'contest_id={$iContestId}&amp')" /> {phrase var='contest.show_photo'}</label>
			</li>
			<li>
				<label><input type="checkbox" checked ="true" name="val[description]" onclick="$('#yncontest_promote_contest_form').ajaxCall('contest.changePromoteBadge', 'contest_id={$iContestId}&amp')" /> {phrase var='contest.show_short_description'}</label>
			</li>
	</div>

	<div class="yncontest promote_contest promote_box">
		<div id ="yncontest_promote_iframe">{$sBadgeCode}</div>
	</div>
</form>

