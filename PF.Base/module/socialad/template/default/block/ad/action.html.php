
<div class="ynsaActionList">
<a href="#" class="js_ynsa_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
<div class="link_menu">
	<ul {if isset($isAdmin) && $isAdmin}class="dropdown-menu"{/if}>
		{if $aActionAd.can_delete_ad}
		<li>
			{if isset($isAdmin) && $isAdmin}
				<a href="#" onclick="$Core.jsConfirm({l}message: $(this).data('message'){r}, function () {l}window.location.href = '{url link='socialad.ad.action' actionname='delete' id=$aActionAd.ad_id inadmin=1}';{r}, function () {l}{r});return false;" data-message="{_p var='are_you_sure_you_want_to_delete_this_ad'}" > {_p var='delete'} </a>
			{else}
				<a href="#" onclick="$Core.jsConfirm({l}message: $(this).data('message'){r}, function () {l}window.location.href = '{url link='socialad.ad.action' actionname='delete' id=$aActionAd.ad_id}';{r}, function () {l}{r});return false;" data-message="{_p var='are_you_sure_you_want_to_delete_this_ad'}" > {_p var='delete'} </a>
			{/if}
		</li>
		{/if}

		{if $aActionAd.can_place_order}
		<li>
			<a href="{url link='socialad.payment.choosemethod' id=$aActionAd.ad_id}" > {if isset($aSaAd.action_placeorder)}{$aSaAd.action_placeorder}{else}{$aSaDetailAd.action_placeorder}{/if} </a>
		</li>
		{/if}

		{if $aActionAd.can_edit_ad}
		<li>
			<a href="{url link='socialad.ad.add' id=$aActionAd.ad_id}" > {_p var='edit'} </a>
		</li>
		{/if}
		
		{if $aActionAd.can_deny_approve_ad}
		<li>
			<a href="javascript:void(0)" onclick="socialadConfirm('{_p var='are_you_sure_you_want_to_approve_this_ad'}', 'approve', {$aActionAd.ad_id});"> {_p var='approve'} </a>
		</li>

		<li>
			<a href="javascript:void(0)" onclick="socialadConfirm('{_p var='are_you_sure_you_want_to_deny_this_ad'}', 'deny', {$aActionAd.ad_id});"> {_p var='deny'} </a>
		</li>
		{/if}

		{if $aActionAd.can_pause_ad}
		<li>
			<a href="javascript:void(0)" onclick="socialadConfirm('{_p var='are_you_sure_you_want_to_pause_this_ad'}', 'pause', {$aActionAd.ad_id});" > {_p var='pause'} </a>
		</li>

		{/if}

		{if $aActionAd.can_resume_ad}
		<li>
			<a href="javascript:void(0)" onclick="socialadConfirm('{_p var='are_you_sure_you_want_to_resume_this_ad'}', 'resume', {$aActionAd.ad_id});"> {_p var='resume'} </a>
		</li>

		{/if}
	</ul>
</div>		

</div>
<script>
if(typeof jQuery != 'undefined') {l}
	$Behavior.ynsaInitDropDownMenu = function() {l} 
		ynsocialad.helper.initDropdownMenu();
	{r}


{r} else {l}
	$Behavior.ynsaInitDropDownMenu = function() {l} 
		ynsocialad.helper.initDropdownMenu();
	{r}
{r}


function socialadConfirm(message, action, id) {l}
    $Core.jsConfirm({l}message: message{r}, function () {l}
        $.ajaxCall('socialad.actionAd', 'action='+action+'&ad_id='+id);
    {r}, function () {l}
    {r});
    return false;

{r}
</script>
