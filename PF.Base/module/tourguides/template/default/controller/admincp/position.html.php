<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<form id="yntour_admin_position_form" method="post" action="{url link='admincp.tourguides.position'}">
    <div id="yntour_admin_draggable_wrapper">
        <div id="yntour_admin_draggable_button">
            {_p var='start_the_tour'}
        </div>
    </div>
    <input type="hidden" name="val[position_right]" id="yntour_admin_position_right" value="{$fRight}">
    <input type="hidden" name="val[position_top]" id="yntour_admin_position_top" value="{$fTop}">
    <div class="panel-footer">
        <input type="submit" value="Save" class="btn btn-primary">
        <input type="button" value="Reset" class="btn btn-default" onclick="resetPosition(true);">
    </div>
</form>