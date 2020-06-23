<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.auction.globalsettings'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='global_settings'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='setting_max_number_cover_photos'}</label>
                <div class="content clearfix">
                    <input class="form-control" type='text' value="{value type='input' id='max_number_cover_photos' default=8}" name="val[max_number_cover_photos]">
                </div>
            </div>
            <div class="form-group">
                <label for="">{phrase var='setting_max_upload_size_cover_photos'}</label>
                <div class="content clearfix">
                    <input class="form-control" type='text' value="{value type='input' id='max_upload_size_cover_photos' default=500}" name="val[max_upload_size_cover_photos]">
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>

