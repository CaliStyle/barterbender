<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_NewsFeed
 * @version          2.04
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<style type="text/css">
.header_bar_menu_modified
{
    background:none;
    border:none;
    margin-bottom: 10px;
}
.header_filter_holder_modified
{
    left: 0px;
    top: 0px;
}
.header_bar_search_input
{
    background: url("{/literal}{$core_path}{literal}module/foxfeedspro/static/image/header_bar_search_input.png") no-repeat scroll 0 0 transparent;
    height: 25px;
    line-height: 25px;
    width: 160px;
    margin-left:60px;
}
.keyword_search
{
    color: #8F8F8F;
    float: left;
    padding-top: 4px;
    font-size:12px;
}
.header_bar_search_holder input{
    margin-left: 10px;
}
@media screen and (-webkit-min-device-pixel-ratio:0) { 
.header_bar_search_holder input { margin-left: 65px; }
}
.header_bar_search .focus
{
     background: url("{/literal}{$core_path}{literal}module/foxfeedspro/static/image/header_bar_search_input.png") no-repeat scroll 0 -25px transparent;
}
.nf_left_title
{
    width: 56px;
    float:left;
    padding: 4px 4px 4px;
    font-weight:bold;
}
.nf_submit_align
{
    padding-left: 68px;
}
.nf_form_bottom
{
    padding-bottom: 0px;
}
.foxfeedspro_search input 
{
	width:60%;
}
.foxfeedspro_search input.button{
	width:auto;
}
</style>
{/literal}



<form method="post" accept-charset="utf-8"  action="{url link='foxfeedspro'}" >
<div class="p_bottom_15 nf_form_bottom foxfeedspro_search">

<div class="p_4">
<div class="nf_left_title">{phrase var='foxfeedspro.keywords'}:</div>
{$aFilters.title}
</div>

<div class="p_4">
<div class="nf_left_title">{phrase var='foxfeedspro.headline_feed_name'}:</div>
{$aFilters.type}
</div>
<div class="p_4">
	<div class="nf_left_title"></div>
    <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary btn-sm" />
</div>

</div>
</form> 