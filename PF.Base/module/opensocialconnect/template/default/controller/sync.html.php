<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($step)}
	{if $step == "checksignon"}
		{literal}
		<script type="text/javascript">
			if(typeof opener !== 'undefined' && opener != null &&  opener != undefined){
			{/literal} {if isset($sUrlRedirect)} {literal}
			opener.location = '{/literal}{$sUrlRedirect}{literal}';
			{/literal} {else} {literal}
			opener.location = opener.location;
			{/literal} {/if} {literal}
			self.close();
			}else{
				window.location.href = '{/literal}{$sUrlRedirect}{literal}';	
			}
		</script>
		{/literal}
	{/if}
	{if $step =="checksignup"}
		{literal}
			<script type="text/javascript">
				if(typeof opener !== 'undefined' && opener != null &&  opener != undefined){
					opener.location='{/literal}{$sUrlRedirect}{literal}';
					self.close();
				}else{
					window.location.href = '{/literal}{$sUrlRedirect}{literal}';
				}
			</script>
		{/literal}
	{/if}
{/if}
<div class="clear"></div>
