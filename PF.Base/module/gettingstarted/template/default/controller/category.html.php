<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          3.01
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<div>
    {if count($aArticle)>0}
    <div style="padding-bottom: 15px;color: #195B85;font-size: 16px; font-weight: bold;">{$ArticleCategory.article_category_name|convert|clean}</div>
    <div class="kb_listing_holder">

        <ul class="action">
{foreach from=$aArticle item=article}
 <div class="row1" id="js_article_row_227">
	<a class="link" href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title}</a>
	<div class="extra_info">

		Posted {$article.time_stamp|convert_time:'gettingstarted.display_time_stamp'}

	</div>
	<div class="t_right">
		<ul class="item_menu">
		</ul>
	</div>
        </div>
{/foreach}
        </ul>

    </div>
    {else}
    	{if count($ArticleCategory)>0}
    		<div style="padding-bottom: 15px;color: #195B85;font-size: 16px; font-weight: bold;">{$ArticleCategory.article_category_name|convert|clean}</div>
    			<div class="extra_info">
        			{phrase var='gettingstarted.no_articles_have_been_added_yet'}
    			</div>
    	{else}
    		<div class="extra_info">
       			{phrase var='gettingstarted.no_articles_have_been_added_yet'}
       		</div>
    	{/if}
    {/if}
</div>
{pager}
