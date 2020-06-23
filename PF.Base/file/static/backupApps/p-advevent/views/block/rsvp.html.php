<div class="dont-unbind-children p-fevent-action-attend-group-detail-outer">
    <form method="post" id="fevent_rsvp" action="{url link='current'}" onsubmit="">
        {if isset($aCallback) && $aCallback !== false}
        <input type="hidden" name="module" value="{$aCallback.module}" />
        <input type="hidden" name="item" value="{$aCallback.item}" />
        {/if}
        <input type="hidden" id="fevent_rsvp_eventid" name="fevent_rsvp_eventid" value="{$aEvent.event_id}" />

        <div class=" js_rsvp_action_{$aEvent.event_id}">
            {template file='fevent.block.rsvp-action'}
        </div>
    </form>
</div>

<script type="text/javascript">
    function show_glogin()
    {l}
        tb_remove();
        tb_show("{_p var='fevent.google_calendar'}",$.ajaxBox("fevent.glogin","height=300;width=350&id="+{$aEvent.event_id}));
    {r}
</script>