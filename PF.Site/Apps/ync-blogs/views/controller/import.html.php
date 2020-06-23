<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 17/01/2017
 * Time: 11:58
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="p-advblog-importblog-page">
    <div class="block p-block">
        <div class="mb-2 mt-1">{_p var="ynblog_import_description"}</div>
        <div class="p-listing-container p-advblog-importblog-container col-3 " data-mode-view="grid">

            <div class="p-item p-advblog-importblog-item js_advblog_importblog_item">
                <div class="item-outer">
                    <div class="item-inner">
                        <div class="item-inner-content">
                            <div class="item-avatar">
                                <span class="item-media-src"
                                      style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-blogs/assets/image/icon_wordpress.png)"></span>
                            </div>
                            <div class="item-wrapper-content">
                                <div class="item-title">
                                    {_p var="wordpress"}
                                </div>
                                <div class="item-desc">
                                    {_p var="Import posts, pages and media from a WordPress export (.xml) file"}
                                </div>

                            </div>
                        </div>
                        <div class="item-inner-submit">

                            <div class="item-action-expand">
                                <a href="javascript:void(0)" data-type="1"
                                   onclick="ynadvancedblog.switchImportBlogType(this)"
                                   class="btn btn-primary">{_p var="start_import"}</a>
                            </div>
                            <div class="ynadvblog_import_choosefile" id="js_ynadvblog_import_choosefile"
                                 style="display: none">
                                <form action="" enctype="multipart/form-data" method="POST">
                                    <input type="hidden" name="val[import_type]" value="1">

                                    <div class=" form-group">
                                        <label>{_p var='Category'} <span class="p-text-danger">{required}</span></label>
                                        {$aCategories}
                                    </div>
                                    <div class="form-group" id="ynblog_file_import-wrapper">
                                        <label>{_p var='file_xml'} <span class="p-text-danger">{required}</span></label>
                                        <input class="form-control" type="file" name="file" accept="text/xml"
                                               value="{value type='file' id='ynblog_file_import'}"
                                               id="ynblog_file_import" onchange="return isXml(this)"/>
                                        <div class="help-block">{_p var='choose_a_file_xml_to_import_maximum_file_size_number' number=10}</div>
                                    </div>
                                    <div class="p-form-group-btn-container">
                                        <input type="submit" name="submit" class="btn btn-primary"
                                               value="{_p var='start_import'}">
                                        <a href="javascript:void(0)" onclick="ynadvancedblog.cancelImportBlogType(this); return false;" class="btn btn-default">
                                            {_p var="cancel"}
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-item p-advblog-importblog-item js_advblog_importblog_item">
                <div class="item-outer">
                    <div class="item-inner">
                        <div class="item-inner-content">
                            <div class="item-avatar">
                                <span class="item-media-src"
                                      style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-blogs/assets/image/icon_blogger.png)"></span>
                            </div>
                            <div class="item-wrapper-content">
                                <div class="item-title">
                                    {_p var="blogger"}
                                </div>
                                <div class="item-desc">
                                    {_p var="Import posts, pages and media from a Blogger export (.xml) file."}
                                </div>

                            </div>
                        </div>
                        <div class="item-inner-submit">

                            <div class="item-action-expand">
                                <a href="javascript:void(0)" data-type="1"
                                   onclick="ynadvancedblog.switchImportBlogType(this)"
                                   class="btn btn-primary">{_p var="start_import"}</a>
                            </div>
                            <div class="ynadvblog_import_choosefile" style="display: none">
                                <form action="" enctype="multipart/form-data" method="POST">
                                    <input type="hidden" name="val[import_type]" value="2">
                                    <div class="form-group">
                                        <label>{_p var='Category'} <span class="p-text-danger">{required}</span></label>
                                        {$aCategories}
                                    </div>
                                    <div class="form-group" id="ynblog_file_import-wrapper">
                                        <label>{_p var='file_xml'} <span class="p-text-danger">{required}</span></label>
                                        <input class="form-control" type="file" name="file" accept="text/xml"
                                               value="{value type='file' id='ynblog_file_import'}"
                                               id="ynblog_file_import" onchange="return isXml(this)"/>
                                        <div class="help-block">{_p var='choose_a_file_xml_to_import_maximum_file_size_number' number=10}</div>
                                    </div>
                                    <div class="p-form-group-btn-container">
                                        <input type="submit" name="submit" class="btn btn-primary"
                                               value="{_p var='start_import'}">
                                        <a href="javascript:void(0)" onclick="ynadvancedblog.cancelImportBlogType(this); return false;" class="btn btn-default">
                                            {_p var="cancel"}
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-item p-advblog-importblog-item js_advblog_importblog_item">
                <div class="item-outer">
                    <div class="item-inner">
                        <div class="item-inner-content">
                            <div class="item-avatar">
                                <span class="item-media-src"
                                      style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-blogs/assets/image/icon_tumblr.png)"></span>
                            </div>
                            <div class="item-wrapper-content">
                                <div class="item-title">
                                    {_p var="tumblr"}
                                </div>
                                <div class="item-desc">
                                    {_p var="import posts, pages and media from a Tumblr username"}
                                </div>

                            </div>
                        </div>
                        <div class="item-inner-submit">

                            <div class="item-action-expand">
                                <a href="javascript:void(0)" data-type="1"
                                   onclick="ynadvancedblog.switchImportBlogType(this)"
                                   class="btn btn-primary">{_p var="start_import"}</a>
                            </div>
                            <div class="ynadvblog_import_choosefile" id="js_ynadvblog_import_choosefile"
                                 style="display: none">
                                <form action="" enctype="multipart/form-data" method="POST">
                                    <input type="hidden" name="val[import_type]" value="3">

                                    <div class="form-group">
                                        <label>{_p var='Category'} <span class="p-text-danger">{required}</span></label>
                                        {$aCategories}
                                    </div>

                                    <div class="form-group" id="txt_tumblr_username-wrapper">
                                        <label for="title">{_p var='username'} <span class="p-text-danger">{required}</span></label>
                                        <input maxlength="255" class="form-control close_warning" type="text"
                                               name="val[txt_tumblr_username]"
                                               value="{value type='input' id='txt_tumblr_username'}"
                                               id="txt_tumblr_username" size="40"/>
                                        <div class="help-block">{_p var='ynblog_user_name_tumblr_description'}</div>
                                    </div>

                                    <div class="p-form-group-btn-container">
                                        <input type="submit" name="submit" class="btn btn-primary"
                                               value="{_p var='start_import'}">
                                        <a href="javascript:void(0)" onclick="ynadvancedblog.cancelImportBlogType(this); return false;" class="btn btn-default">
                                            {_p var="cancel"}
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>
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
                if (file_size / (1024 * 1024) > 10) {
                    input.value = "";
                }
            }

            return res;
        }
    </script>
{/literal}
