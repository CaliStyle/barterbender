<script src="{$core_path}module/fevent/static/jscript/jquery.ui.core.js"></script>
  
<div class="fevent_form_repeat">
	<div class="table_repeat">
		<div class="table_left_repeat">{_p var='fevent.recurrent_period'}:</div>
		<div class="table_right">
			<select id="selrepeat" onchange="ynfeAddPage.onchangeSelrepeat();" {if ($isEdit == true && $canEditDuration == false)}disabled="disabled"{/if}>
			<option value="0" {if $txtrepeat==0}selected{/if}>{_p var='fevent.daily'}</option>
			<option value="1" {if $txtrepeat==1}selected{/if}>{_p var='fevent.weekly'}</option>
			<option value="2" {if $txtrepeat==2}selected{/if}>{_p var='fevent.monthly'}</option>
			</select>
		</div>
	</div>

	<div class="table_repeat">
		<div class="table_left_repeat">{_p var='fevent.repeat_until'}:</div>
		<div class="table_right">
			{if ($isEdit == true && $canEditEndTime == false)}
				<input type="text" id="end_on" readonly="true" value="{$daterepeat}" onchange="ynfeAddPage.onchangeEndOn();"/>
			{else}
				<input type="text" id="end_on" readonly="true" onchange="ynfeAddPage.onchangeEndOn();"/>
			{/if}			
			<span>{_p var='fevent.at'}</span>
			<select id="end_on_hour" {if ($isEdit == true && $canEditEndTime == false)}disabled="disabled"{/if}>
				{foreach from=$aHours item=iHour}
					{if strlen($iHour) < 2}
						<option value="0{$iHour}" {if isset($daterepeat_hour) && intval($iHour) == $daterepeat_hour} selected {/if} >0{$iHour}</option>
					{else}
						<option value="{$iHour}" {if isset($daterepeat_hour) && intval($iHour) == $daterepeat_hour} selected {/if} >{$iHour}</option>
					{/if}
				{/foreach}
			</select>	
			<span>:</span>
			<select id="end_on_min" {if ($isEdit == true && $canEditEndTime == false)}disabled="disabled"{/if}>
				{foreach from=$aMinutes item=iMin}
					{if strlen($iMin) < 2}
						<option value="0{$iMin}" {if isset($daterepeat_min) && intval($iMin) == $daterepeat_min} selected {/if} >0{$iMin}</option>
					{else}
						<option value="{$iMin}" {if isset($daterepeat_min) && intval($iMin) == $daterepeat_min} selected {/if} >{$iMin}</option>
					{/if}
				{/foreach}
			</select>	
		</div>
		<div id="error_end_on" class="repeat_details_error clear"></div>
		<div class="clear"></div>
	</div>

	<div class="separate"></div>

	<div id="durationDays" class="table_repeat" {if $txtrepeat!=1 && $txtrepeat!=2}style="display: none;"{/if}>
		<div class="table_left_repeat">{_p var='fevent.duration'}:
			<div style="position: absolute; line-height: normal; width: 80px; color: grey; font-size: 10px;">{_p var='fevent.txt_hint_total'}</div>
		</div>
		<div class="table_right">
			<input type="text" id="duration_days" maxlength="10" value="{if isset($daterepeat_dur_day)}{$daterepeat_dur_day}{/if}" {if ($isEdit == true && $canEditDuration == false)}disabled="disabled"{/if} />
			<span>&nbsp;&nbsp;{_p var='fevent.day_s'}</span>
		</div>
		<div id="hint_duration_days" class="clear" style="margin-left: 105px; margin-top: 5px;">{if $txtrepeat==1}{_p var='fevent.from_0_to_6'}{/if}{if $txtrepeat==2}{_p var='fevent.h_from_0_to_30'}{/if}</div>
		<div id="error_duration_days" class="repeat_details_error clear"></div>
		<div class="clear"></div>
	</div>
	<div id="duraitonHours" class="table_repeat">
		<div id="duraitonHoursLabel" class="table_left_repeat">{if $txtrepeat!=1 && $txtrepeat!=2}{_p var='fevent.duration'}:{else}&nbsp;{/if}</div>
		<div class="table_right">
			<input type="text" id="duration_hours" maxlength="10" value="{if isset($daterepeat_dur_hour)}{$daterepeat_dur_hour}{/if}" {if ($isEdit == true && $canEditDuration == false)}disabled="disabled"{/if} />
			<span>&nbsp;&nbsp;{_p var='fevent.hour_s'}</span>
		</div>
		<div id="hint_duration_hours" class="clear" style="margin-left: 105px; margin-top: 5px;">{if $txtrepeat!=1 && $txtrepeat!=2}{_p var='fevent.from_1_to_23'}{else}{_p var='fevent.from_0_to_23'}{/if}</div>
		<div id="error_duration_hours" class="repeat_details_error clear"></div>
		<div class="clear"></div>
	</div>

	<div class="table_repeat">
		<div class="table_left_repeat">&nbsp;</div>
		<div class="table_right">
			{if ($isEdit == true && $canEditEndTime == false && $canEditDuration == false)}
			{else}
			<input class="button" id="btnDone" type="submit" value="{_p var='fevent.a_done'}" onclick="donerepeat();"/>
			{/if}
			<input class="button" id="btnCancel" type="submit" value="{_p var='fevent.cancel'}" onclick="cancelrepeat({$value});"/>
		</div>
	</div>
</div>

{if $isEdit == false || ($isEdit == true && $canEditEndTime == true)}
	<script type="text/javascript">	
		$(function() {l}	
			$("#end_on").datepicker().val("{if $daterepeat!=""}{$daterepeat}{/if}")
		{r});

		$('#end_on').click(function() {l}
		  return false;
		{r});		
	</script>
{/if}

<script type="text/javascript">
	
	function donerepeat()
	{l}
		$('#btnDone').attr('disabled', true);
		$('#btnCancel').attr('disabled', true);

		document.getElementById("error_end_on").innerHTML	= "";
		document.getElementById("error_duration_days").innerHTML	= "";
		document.getElementById("error_duration_hours").innerHTML	= "";

		var selrepeat=$('#selrepeat').val();
		var txtdisable=$('#end_on').attr("disabled");
		var bIsEdit=$('#bIsEdit').val();

		if(!txtdisable)
		{l}
			txtdisable=$('#end_on').val();

			var end_on_hour = $('#end_on_hour').val();
			var end_on_min = $('#end_on_min').val();
			var duration_days = $('#duration_days').val();
			var duration_hours = $('#duration_hours').val();

			var start_month = $('#start_month').val();
			var start_day = $('#start_day').val();
			var start_year = $('#start_year').val();
			var start_hour = $('#start_hour').val();
			var start_minute = $('#start_minute').val();
		{r}
		else
		{l}
			txtdisable="";

			var end_on_hour="";
			var end_on_min="";
			var duration_days="";
			var duration_hours="";

			var start_month = "";
			var start_day = "";
			var start_year = "";
			var start_hour = "";
			var start_minute = "";
		{r}

		$.ajaxCall('fevent.donerepeat','relrepeat='+selrepeat+'&txtdisable='+txtdisable 
				+ '&bIsEdit=' + bIsEdit 
				+ '&end_on_hour=' + end_on_hour 
				+ '&end_on_min=' + end_on_min 
				+ '&duration_days=' + duration_days 
				+ '&duration_hours=' + duration_hours				
				+ '&start_month=' + start_month
				+ '&start_day=' + start_day
				+ '&start_year=' + start_year
				+ '&start_hour=' + start_hour
				+ '&start_minute=' + start_minute
		);
		
		// tb_remove();
	{r}
	
	function cancelrepeat(value)
	{l}
		document.getElementById("error_end_on").innerHTML	= "";
		document.getElementById("error_duration_days").innerHTML	= "";
		document.getElementById("error_duration_hours").innerHTML	= "";

		var bIsEdit=$('#bIsEdit').val();
		var txtdisable=$('#end_on').val();

		if(value!=2)
		{l}			
			$('#cbrepeat').removeAttr("checked");
			$('#deleteAllAttendeesBox').css('display','none');
		{r}

		if(!bIsEdit && txtdisable=="")
		{l}
			$('.extra_info').css('display','block');
			$('#extra_info_date').css('display','block');
			$('#js_event_add_end_time').css('display','none');
		{r} else {l}
			if($('#extra_info_date').length > 1){l}
				$('#extra_info_date').css('display','block');
				$('#js_event_add_end_time').css('display','none');
			{r} else {l}
				if($('#chooserepeat').html().length > 0){l}
					$('#js_event_add_end_time').css('display','none');
				{r} else {l}
					$('#js_event_add_end_time').css('display','block');
				{r}						
			{r}		
		{r}
			
		tb_remove();
	{r}
	
	{literal}
	setTimeout(function(){
		$('.js_box_close').css("display","none");
	},10);
	{/literal}
</script>