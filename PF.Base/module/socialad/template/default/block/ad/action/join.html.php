
 <a href="#" onclick="{$aSaData.sOnclickEvent}" >
	{$aSaData.sIconHtml}&nbsp;
	{$aSaData.sActionPhrase}
</a>

{if isset($aSaData.sPhrase) && $aSaData.sPhrase}
	 &middot {$aSaData.sPhrase}
{/if}

