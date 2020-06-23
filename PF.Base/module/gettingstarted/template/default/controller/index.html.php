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
//$current_page > 1
?>
{if PHPFOX_IS_AJAX}
	{if (isset($articlecategories))}
		{foreach from=$articlecategories item=articlecategory}
			{if count($articlecategory.article)>0}
				{foreach from=$articlecategory.article item=article}
					<li class="row">
						<a class="item_title item_view_content" href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title}</a>
						<div class="extra_info">{phrase var='gettingstarted.posted_on_post_time' post_time=$article.post_time}{if $article.article_category_id!=-1} - {phrase var='gettingstarted.in'} <a class="level_label" href="{permalink module='gettingstarted.categories' id=$article.article_category_id title=$article.article_category_name|convert|clean}view_/">{$article.article_category_name|convert|clean}</a>{/if}</div>
					</li>
				{/foreach}
				{if $flag == 1}
					{pager}
				{/if}
   			{/if}
		{/foreach}
	{/if}			

{else}

{if $bIsSearch == true}
	{if $iCnt>0}
		{foreach from=$articlecategories item=articlecategory}
         <div class="block kblist_block">
            {if count($articlecategory.article)>0 && $bIsCategory==false}
   			<div class="title">
                   <a href="{url link='gettingstarted.categories'}{$articlecategory.article_category_id}/{$articlecategory.name_url|convert|clean}">{$articlecategory.article_category_name|convert|clean}</a>
           	</div>
            {/if}
            {if count($articlecategory.article)>0}
   			<div class="content kb_listing_holder">
       			<ul>
       				
       					{foreach from=$articlecategory.article item=article}
   							<li class="row">
   								<a class="item_title item_view_content" href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title}</a>
   								<div class="extra_info">{phrase var='gettingstarted.posted_on_post_time' post_time=$article.post_time}{if $article.article_category_id!=-1} - {phrase var='gettingstarted.in'} <a class="level_label" href="{permalink module='gettingstarted.categories' id=$article.article_category_id title=$article.article_category_name|convert|clean}view_/">{$article.article_category_name|convert|clean}</a>{/if}</div>
       						</li>
       					{/foreach}
       					{if $flag == 1}
        					{pager}
        				{/if}
       				
       				{if $articlecategory.pagination==1 && $bIsCategory==false}
       					<li class="t_right">
             					<a href="{permalink module='gettingstarted.categories' id=$articlecategory.article_category_id title=$articlecategory.article_category_name|convert|clean}view_/search-id_{$search_id}/">{phrase var='gettingstarted.search_more_in'}: {$articlecategory.article_category_name|convert|clean}</a>
   						</li>
       				{/if}
       				
       			</ul>
   			</div>
   			{/if}
      </div>
		{/foreach}
		
	{else}

		{if $iPage < 2}
			<div class="extra_info noresult_msg">
				{phrase var='gettingstarted.no_articles_found'}
			</div>
		{/if}
	{/if}
{else}

	{if count($articlecategories)>0}
		{foreach from=$articlecategories item=articlecategory}
       <div class="block kblist_block">
       	
            {if $bIsCategory==false}

			<div  class="title">
                <a href="{url link='gettingstarted.categories'}{$articlecategory.article_category_id}/{$articlecategory.name_url|convert|clean}">{$articlecategory.article_category_name|convert|clean}</a>
        	</div>
            {/if}
			<div class="content kb_listing_holder">
    			<ul>
    				{if count($articlecategory.article)>0}
    					{foreach from=$articlecategory.article item=article}
							<li class="row">
								<a class="item_title item_view_content" href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title}</a>
								<div class="extra_info">{phrase var='gettingstarted.posted_on_post_time' post_time=$article.post_time}{if $article.article_category_id!=-1} - {phrase var='gettingstarted.in'} <a class="level_label" href="{permalink module='gettingstarted.categories' id=$article.article_category_id title=$article.article_category_name|convert|clean}view_/">{$article.article_category_name|convert|clean}</a>{/if}</div>
    						</li>
    					{/foreach}
    					{if $flag == 1}
        					{pager}
        				{/if}
    				{else}
						{if $iPage < 2}
							<li class="row">
								{phrase var='gettingstarted.no_articles_have_been_added_yet'}
							</li>
						{/if}
    				{/if}
    				{if $articlecategory.pagination==1 && $bIsCategory==false}
    					<li class="t_right">
          					<a href="{permalink module='gettingstarted.categories' id=$articlecategory.article_category_id title=$articlecategory.article_category_name|convert|clean}view_/">{phrase var='gettingstarted.view_more'}: {$articlecategory.article_category_name|convert|clean}</a>
						</li>
    				{/if}
    			</ul>
			</div>
      </div>
		{/foreach}

	{else}

	 	{if $iPage < 2 }
			<li class="row">
				{phrase var='gettingstarted.no_articles_have_been_added_yet'}
			</li>
		{/if}

	{/if}
{/if}

{literal}
<style type="text/css">
.header_filter_holder
{
    display:none;
}
.emoticon_preview, .emoticon_preview:hover {
    width: auto!important;
}
</style>
{/literal}



{/if}