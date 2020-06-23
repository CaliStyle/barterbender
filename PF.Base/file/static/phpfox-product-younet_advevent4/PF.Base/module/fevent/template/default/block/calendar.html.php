<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>

<input type="hidden" name="calendar" id="calendar" value="{$sDate}" />
{literal}
<script type="text/javascript">
$Behavior.initCalendar = function()
{
	$('#tooltip').find('div').css('font-size','12px');
	$('.date_selector').remove();
	$('#calendar').jdPicker();
	{/literal}
	{literal}
}
</script>
{/literal}
<script type="text/javascript">
	POOL = {l}
		"phrase_events":"{$sPhraseEvents}",
		"events":[],
		"search":{l}{r}
	{r};
	{foreach from=$aJsEvents item="aEvent"}
	{foreach from=$aEvent.calendar item="calendar"}
    {if isset($aEvent.event_id)}
	POOL.events.push({l}
		"event_id":{$aEvent.event_id},
		"event_name": "{$aEvent.title}",
		"start_time":"{$calendar}",
		"from": "{$aEvent.d_start_time_hour}",
		"to": "{$aEvent.detail_end_time} {$aEvent.d_end_time_hour}",
        "isrepeat":{$aEvent.isrepeat}
	{r});
    {elseif isset($aEvent.bday)}
    POOL.events.push({l}
    "full_name": "{$aEvent.full_name}",
    "start_time":"{$calendar}",
    {r});
    {/if}
	{/foreach}
	{/foreach}
	{literal}
	for(var i=0; i<POOL.events.length; i++){
		if(typeof(POOL.search[POOL.events[i].start_time])=='undefined'){
			POOL.search[POOL.events[i].start_time] = new Array();
		}
		POOL.search[POOL.events[i].start_time].push(POOL.events[i]);
	}
	{/literal}
</script>