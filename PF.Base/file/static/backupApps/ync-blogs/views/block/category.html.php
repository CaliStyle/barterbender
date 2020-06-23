<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 17:50
 */
?>

<div class="ynadvancedblog_categories">
    <ul>
        {foreach from=$aCategories item=aCategory}
        <li>
	            <a href="{permalink module='ynblog.category' id=$aCategory.category_id title=$aCategory.name}" class="category_text">{_p var=$aCategory.name}</a>
	            <a href="{permalink module='ynblog.rss.category' id=$aCategory.category_id title=$aCategory.name}" class="rss_icon no_ajax" target="_blank"><i class="fa fa-rss-square" aria-hidden="true"></i></a>
        </li>
        {/foreach}
    </ul>
</div>
