<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 14/02/2017
 * Time: 00:17
 */
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="https://www.blogger.com/styles/atom.css" type="text/css"?>'; ?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:gd="http://schemas.google.com/g/2005" xmlns:georss="http://www.georss.org/georss" xmlns:openSearch="http://a9.com/-/spec/opensearchrss/1.0/" xmlns:thr="http://purl.org/syndication/thread/1.0">
    <id>tag:blogger.com,1999:blog-7284001387436912727.archive</id>
    <updated>2017-02-16T18:28:43.921-08:00</updated>
    <title type="text">The Impact of Design Thinking on your Business</title>
    <link rel="http://schemas.google.com/g/2005#feed" type="application/atom+xml" href="https://www.blogger.com/feeds/7284001387436912727/archive" />
    <link rel="self" type="application/atom+xml" href="https://www.blogger.com/feeds/7284001387436912727/archive" />
    <link rel="http://schemas.google.com/g/2005#post" type="application/atom+xml" href="https://www.blogger.com/feeds/7284001387436912727/archive" />
    <link rel="alternate" type="text/html" href="http://thanhnc4794.blogspot.com/" />
    <author>
        <name>{$aUser.full_name}</name>
        <uri>https://www.blogger.com/profile/16946155389916431505</uri>
        <email>noreply@blogger.com</email>
        <gd:image rel="http://schemas.google.com/g/2005#thumbnail" width="35" height="35" src="//lh3.googleusercontent.com/zFdxGE77vvD2w5xHy6jkVuElKv-U9_9qLkRYK8OnbDeJPtjSZ82UPq5w6hJ-SA=s35" />
    </author>
    <generator version="7.00" uri="https://www.blogger.com">Blogger</generator>
    {foreach from=$aItems item=aItem}
    <entry>
        <id>tag:blogger.com,1999:blog-9196383144888789832.post-2146612236581662092</id>
        <published>{$aItem.time_stamp|date}</published>
        <updated>{$aItem.time_update|date}</updated>
        <category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/blogger/2008/kind#post" />
        <title type="text">{$aItem.title|clean}</title>
        <content type="html"><![CDATA[{$aItem.text}]]></content>
        <link rel="edit" type="application/atom+xml" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title|clean}" />
        <link rel="self" type="application/atom+xml" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title|clean}" />
        <author>
            <name>{$aItem.full_name}</name>
            <uri>{url link=$aItem.user_name}</uri>
        </author>
    </entry>
    {/foreach}
</feed>