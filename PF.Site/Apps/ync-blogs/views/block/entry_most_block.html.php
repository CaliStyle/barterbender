<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 10:47
 */
defined('PHPFOX') or exit('NO DICE!');
	
?>

<div class="ynadvblog_avatar">
	<a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" class="item_image{if empty($aItem.text)} full{/if}" style="background-image: url(<?php echo Phpfox::getService('ynblog.helper')->getImagePath($this->_aVars['aItem']['image_path'], $this->_aVars['aItem']['server_id'], '_grid'); ?>)">
	</a>
	{if isset($sTypeBlock)}
	    <div class="ynadvblog-{$sTypeBlock}">
	        <?php $this->_aVars['iBlockValue'] = $this->_aVars['aItem'][$this->_aVars['sTypeBlock']]; ?>
	            {if $iBlockValue == 1}
	                <div>{$iBlockValue}</div>
	                <div>{_p var=$sTypeUnit.singular}</div>
	            {else}
	                <div>{$iBlockValue}</div>
	                <div>{_p var=$sTypeUnit.plural}</div>
	            {/if}
	    </div>
	{/if}
	{if !empty($aItem.is_featured)}<i title="{_p var='Feature'}" class="fa fa-diamond ynadvblog_feature_icon" aria-hidden="true"></i>{/if}
</div>
{$aItem|user:'':'':50:'':'author'}
<a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link fw-bold ynadvblog_post_title" itemprop="url">{$aItem.title|clean}</a>