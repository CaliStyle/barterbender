<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
	{if isset($bRedirect)}
		{literal}
			<script type="text/javascript">
				if(opener == null || opener == undefined){
					window.location.href = '{/literal}{$sUrlRedirect}{literal}';
				}else{
					opener.location='{/literal}{$sUrlRedirect}{literal}';
					self.close();
				}
			</script>
		{/literal}
    {/if}
<div class="clear"></div>

