<script type="text/javascript" src="{$sCorePath}module/socialad/static/jscript/ynsocialad.js"></script>
{module name='socialad.sub-menu'}
{module name='socialad.ad.ad-filter'}

<div class="clear"></div>
<div id="js_ynsa_ad_list" style="overflow-x: auto;" class="ynsaLFloat ">
{module name='socialad.ad.ad-list'}
</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    if (typeof google != 'undefined' && typeof google.load != 'undefined') {l}
	    google.load('visualization', '1.0', {l}'packages':['corechart']{r});
    {r}
</script>
	
<script>
	$Behavior.ynsaInitSorting = function() {l}
		$('#js_ynsa_ad_list').clickableTable();
	{r}
</script>