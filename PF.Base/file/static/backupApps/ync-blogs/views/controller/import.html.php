<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 17/01/2017
 * Time: 11:58
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ynadvblog_import" id="js_ynadvblog_import">
	<p class="extra_info">{_p var="ynblog_import_description"}</p>
	<ul>
		<li class="clearfix">
			<a href="javascript:void(0)" data-type="1" onclick="ynadvancedblog.switchImportBlogType(this)" class="uppercase">{_p var="wordpress"}</a>
			<span>{_p var="import_posts_from_a_wordpress_export_file"}</span>
		</li>
		<li class="clearfix">
			<a href="javascript:void(0)" data-type="2" onclick="ynadvancedblog.switchImportBlogType(this)" class="uppercase">{_p var="blogger"}</a>
			<span>{_p var="import_posts_from_a_blogger_export_file"}</span>
		</li>
		<li class="clearfix">
			<a href="javascript:void(0)" data-type="3" onclick="ynadvancedblog.switchImportBlogType(this)" class="uppercase">{_p var="tumblr"}</a>
			<span>{_p var="import_posts_from_a_tumblr_username"}</span>
		</li>
	</ul>
	<a href="javascript:void(0)" onclick="history.go(-1); return false;" class="btn btn-default">{_p var="Back"}</a>
</div>

<div class="ynadvblog_import_choosefile" id="js_ynadvblog_import_choosefile" style="display: none">
    <form action="" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="val[import_type]" value="" id="import_type">

        <div class="table form-group">
            <div class="table_left">
                <label for="category">{required}{_p var='Category'}:</label>
            </div>
            <div class="table_right">
                {$aCategories}
            </div>
            <div class="clear"></div>
        </div>

        <div class="table form-group" id="txt_tumblr_username-wrapper">
            <div class="table_left">
                <label for="title">{required}{_p var='username'}:</label>
            </div>
            <div class="table_right">
                <input maxlength="255" class="form-control close_warning" type="text" name="val[txt_tumblr_username]" value="{value type='input' id='txt_tumblr_username'}" id="txt_tumblr_username" size="40" />
                <div class="help-block">{_p var='ynblog_user_name_tumblr_description'}</div>
            </div>
        </div>

        <div class="form-group" id="ynblog_file_import-wrapper">
            <label>{required}{_p var='file_xml'}:</label>
            <input class="form-control" type="file" name="file" accept="text/xml" value="{value type='file' id='ynblog_file_import'}" id="ynblog_file_import" onchange="return isXml(this)" />
            <div class="help-block">{_p var='choose_a_file_xml_to_import_maximum_file_size_number' number=10}</div>
        </div>

        <input type="submit" name="submit" class="btn btn-primary" value="{_p var='Upload and Import'}">
        <a href="javascript:void(0)" onclick="$('#js_ynadvblog_import').show(); $('#js_ynadvblog_import_choosefile').hide(); return false;" class="btn btn-default">{_p var="Back"}</a>
    </form>
</div>

{literal}
<script type="text/javascript">
    function isXml(input) {
        var value = input.value;
        var res = value.substr(value.lastIndexOf('.')) == '.xml';
        if (!res) {
            input.value = "";
        } else {
            var file_size = input.files[0].size;
            if (file_size/(1024 * 1024) > 10) {
                input.value = "";
            }
        }

        return res;
    }
</script>
{/literal}
