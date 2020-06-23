<form method="post" action="{url link='current'}" id='yncontest_join_form'>

    <div id="core_js_messages">
        <div class="error_message" style='display:none' id='yncontest_must_agree'> {phrase var='contest.you_must_agree_with_terms_and_conditions_to_join_this_contest'}</div>
    </div>

    <div class="table_right">
        {if $aContest.term_condition}
        <div class='yncontest_term_condition_scroll'>
        {$aContest.term_condition}
        </div>
        {/if}
    </div>

    <label><input type="checkbox"  id='yncontest_join_agree_term_condition'  name='val[agree_join]' /> {phrase var='contest.i_agree'}</label>

    <div class="table_clear">
        <ul class="table_clear_button">
            <li> <button type='button' id='yncontest_join_button' class="button btn btn-primary btn-sm" name='val[submit]' onclick="yncontest.join.submitJoinContest({$aContest.contest_id}); return false;">{phrase var='contest.join'}</button> </li>
        </ul>
        <div class="clear"></div>
    </div>

    <div id ="yn_contest_waiting_join" style='display:none'> {img theme='ajax/add.gif'} </div>

</form>
