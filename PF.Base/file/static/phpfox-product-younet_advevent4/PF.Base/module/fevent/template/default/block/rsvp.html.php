
<form method="post" id="fevent_rsvp" action="{url link='current'}" onsubmit="">
{if isset($aCallback) && $aCallback !== false}
	<div><input type="hidden" name="module" value="{$aCallback.module}" /></div>
	<div><input type="hidden" name="item" value="{$aCallback.item}" /></div>
{/if}
	<div>
		<div><input type="hidden" id="fevent_rsvp_eventid" name="fevent_rsvp_eventid" value="{$aEvent.event_id}" /></div>

		<div class="item-event-option mb-1 attending {if $aEvent.rsvp_id == 1} active{/if}">
			<label class="item-event-radio">
				<input type="radio" name="rsvp" value="1" class="checkbox v_middle js_event_rsvp" {if $aEvent.rsvp_id == 1}checked {/if}/>
				<span class="btn btn-sm btn-default btn-icon js_rsvp_title">{if $aEvent.rsvp_id == 1}<span class="ico ico-check js_checked"></span>{/if}{_p var='fevent.attending'}</span>
			</label>
		</div>

		<div class="item-event-option mb-1 maybe_attending {if $aEvent.rsvp_id == 2} active{/if}">
			<label class="item-event-radio">
				<input type="radio" name="rsvp" value="2" class="checkbox v_middle js_event_rsvp" {if $aEvent.rsvp_id == 2}checked {/if}/>
				<span class="btn btn-sm btn-default btn-icon js_rsvp_title">{if $aEvent.rsvp_id == 2}<span class="ico ico-check js_checked"></span>{/if}{_p var='fevent.maybe_attending'}</span>
			</label>
		</div>

		<div class="item-event-option mb-1 not_attending {if $aEvent.rsvp_id == 3} active{/if}">
			<label class="item-event-radio">
				<input type="radio" name="rsvp" value="3" class="checkbox v_middle js_event_rsvp" {if $aEvent.rsvp_id == 3}checked {/if}/>
				<span class="btn btn-sm btn-default btn-icon js_rsvp_title">{if $aEvent.rsvp_id == 3}<span class="ico ico-check js_checked"></span>{/if}{_p var='fevent.not_attending'}</span>
			</label>
		</div>
	</div>
	<div>
	    {if ($aEvent.rsvp_id == 1 || $aEvent.rsvp_id == 2)}
		    <div id="js_event_gcalendar_button">
		        {if $bIsGapi}
		        <input type="button" value="{_p var='fevent.add_to_google_calendar'}" class="btn btn-sm btn-primary" onclick="show_glogin()" />
		        {/if}
			</div>
	    {/if}
    </div>
</form>

<script type="text/javascript">
    function show_glogin()
    {l}
        tb_remove();
        tb_show("{_p var='fevent.google_calendar'}",$.ajaxBox("fevent.glogin","height=300;width=350&id="+{$aEvent.event_id}));
    {r}
</script>