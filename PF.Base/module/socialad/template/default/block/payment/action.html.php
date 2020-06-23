
<div class="ynsaActionList">
	<a href="#" class="js_ynsa_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
	<div class="link_menu">
		<ul class="dropdown-menu">
				{if $aTransaction.can_confirm_pay_later_transaction} 
				<li>
					<a href="#" onclick="$Core.jsConfirm({l}message: '{_p var="are_you_sure_confirm_request"}'{r}, function () {l}$.ajaxCall('socialad.confirmPayLaterTransaction', 'transaction_id={$aTransaction.transaction_id}');{r}, function () {l}{r});return false;">
							{_p var="confirm"}
					 </a>
				</li>
				{/if}

				{if $aTransaction.can_cancel_pay_later_request} 
					<li>
                        <a href="#" onclick="$Core.jsConfirm({l}message: '{_p var="are_you_sure_cancel_request"}'{r}, function () {l}$.ajaxCall('socialad.cancelPayLaterRequest', 'transaction_id={$aTransaction.transaction_id}');{r}, function () {l}{r});return false;">
							{_p var="cancel"}
					    </a>
					</li>	
			{/if}
		</ul>
	</div>		
<div>
<script>
if(typeof jQuery != 'undefined') {l}
		ynsocialad.helper.initDropdownMenu();
{r} else {l}
	$Behavior.ynsaInitDropDownMenu = function() {l} 
		ynsocialad.helper.initDropdownMenu();
	{r}
{r}
</script>
