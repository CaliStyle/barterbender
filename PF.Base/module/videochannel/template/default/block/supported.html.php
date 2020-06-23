<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="label_flow" style="height:300px;">
	<ul>
	{foreach from=$aSites item=sSite}
		<li>{$sSite|clean}</li>
	{/foreach}
	</ul>
</div>