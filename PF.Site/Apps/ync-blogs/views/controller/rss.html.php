<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 23/01/2017
 * Time: 00:12
 */
?>

<rss version="2.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <channel>
        <title>
            <?php echo _p('Blogs'); ?>
            &#187;

        </title>
        <description>
            <?php echo _p('rss_blogs');?>
        </description>
        <link>
        {$sLink}
        </link>
        {foreach from=$aItems item=aItem}
            <item>
                <title><![CDATA[
                    {$aItem.title|clean}
                    ]]></title>
                <link>
                    {permalink module='ynblog' id=$aItem.blog_id title=$aItem.title|clean}
                </link>
                <description><![CDATA[
                    {$aItem.text|striptag|shorten:200:'...'}
                    ]]></description>
                <pubDate> {$aItem.time_stamp} </pubDate>
            </item>
        {/foreach}
    </channel>
</rss>


