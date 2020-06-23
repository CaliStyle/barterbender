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
<script src="//maps.googleapis.com/maps/api/js?v=3.exp&key={$apiKey}&sensor=false&language=en&libraries=places"></script>

{plugin call='fevent.template_default_controller_view_extra_info'}

{if $gcalendar == 1}
{literal}
<script type="text/javascript">
    var flag = false
    $Behavior.feLoadDetailEvent = function(){       
        if(flag == false)
        {
            $.ajaxCall('fevent.gnotif','type=success');
            flag = true;
        }
    }
</script>
{/literal}
{/if}
{if $aEvent.d_type == 'past'}
<style type="text/css">
    #js_block_border_fevent_rsvp {l}
        display: none;
    {r}
</style>
{/if}
