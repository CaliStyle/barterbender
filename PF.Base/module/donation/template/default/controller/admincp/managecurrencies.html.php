{literal}
	<style>
		#site_content{
			background: #FFF;
			padding: 10px;
			box-sizing: border-box;
		}
	</style>
{/literal}

<form method='post' action="{url link='admincp.donation.managecurrencies'}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
            {foreach from=$aCurrencies key=key item=sCurrency}
                {if ($key % 3 eq 0) and ($key != 0 )}
                    <div class="clear"></div>
                {/if}

                <div class="yn_donation_currency_checkbox" >
                    <input type='checkbox' value="{$sCurrency}" name="aVals[aCurrencies][]"
                    {if in_array($sCurrency ,$aCurrentCurrencies)} checked='1' {/if}
                    /> {$sCurrency}
                </div>
            {/foreach}
            </div>
            <div class="clear"></div>
            <div class="form-group">
                <div class='extra_info'>
                    {phrase var='donation.if_no_currency_is_chosen_usd_will_be_used_as_default_currency'}
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <input type="hidden" name="aVals[bIsEditForm]" value="1"/>
            <input type="submit" value="{phrase var='donation.save'}" class="btn btn-primary" />
        </div>
    </div>
</form>