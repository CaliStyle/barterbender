<?php 
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
?>

<!-- Add Level Form Layout -->
<form method="post" action="{url link='admincp.resume.addlevel'}" id="resume_add_level_form" enctype="multipart/form-data">
	<div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='level_details'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {required}{_p var='level_title'}:
                </label>
                <input class="form-control" type="text" name="val[title]" value="" id="title" size="40" maxlength="150" />
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='add_level'}" class="btn btn-primary" />
        </div>
    </div>
</form>