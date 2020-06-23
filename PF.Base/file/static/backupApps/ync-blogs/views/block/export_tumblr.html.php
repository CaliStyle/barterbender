<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 14/02/2017
 * Time: 00:17
 */
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<rss version="2.0">
    <channel>
        <atom:link rel="hub" href="http://tumblr.superfeedr.com/" xmlns:atom="http://www.w3.org/2005/Atom"/>
        <description></description>
        <title>{$aItems.0.title|clean}</title>
        <generator>Tumblr (3.0; @{$aItems.0|user})</generator>
        <link>{permalink module='ynblog' id=$aItems.0.blog_id title=$aItems.0.title|clean}</link>
        {foreach from=$aItems item=aItem}
            <item>
                <title>{$aItem.title|clean}</title>
                <description>&lt;p&gt;{$aItem.text}&lt;/p&gt;</description>
                <link>{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title|clean}</link>
                <guid>{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title|clean}</guid>
                <pubDate>{$aItem.time_stamp|date}</pubDate>
            </item>
        {/foreach}
    </channel>
</rss>
