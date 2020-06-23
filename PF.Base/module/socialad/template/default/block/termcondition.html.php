
{if $sTermsAndConditions}
	<div class="form-group extra_info ynsaTermAndCondition">
        <label for="ynsa_term_condition_field" style="font-weight: 400 !important;">
            <div class="ynsaClearFix ynsaIntro checkbox" style="margin-left: 20px">
                <input {if isset($aForms)}checked{/if} type="checkbox" id="ynsa_term_condition_field" style="margin-right: 5px;" />{phrase var='by_clicking_submitphrase_you_agree_to_the_following_terms_and_conditions' SubmitPhrase=$sSaSubmitPhrase}.<br>
            </div>
        </label>
        <div class="ynsaContent">
        {$sTermsAndConditions}
        </div>
	</div>
{/if}
