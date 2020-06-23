<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 15:18
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Term of Service')}
        </div>
    </div>
    <form method="post" action="">
        <div class="panel-body">
            <div class="form-group">
                <label for="title">
                    {required}{_p('Title')}:
                </label>
                <input class="form-control" type="text" name="val[title]" id="title" value="{value type='input' id='title'}" maxlength="200">
            </div>
            <div class="form-group">
                <label for="content">
                    {required}{_p('Content')}:
                </label>
                <textarea class="form-control" name="val[content]" id="content" cols="30" rows="10">{value id='content' type='textarea'}</textarea>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
        </div>
    </form>
</div>
