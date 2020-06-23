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

<form method="post" action="{url link='admincp.fevent.birthdayphoto'}" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='birthday_block_photo'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group" id="image_select">
                <div style="max-width: 500px;max-height: 500px;">
                    <img src="{$currentImageUrl}" style="max-width: 100%;max-height: 100%">
                </div>

                <div style="clear: both;"></div>
                <br>
                <p class="help-block">
                    {_p var='recommended_dimension_birthday_photo'}
                </p>
                <input type="file" name="file" id="file" class="form-control">
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary"/>
        </div>
    </div>
</form>