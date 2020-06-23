<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 21, 2020, 10:23 am */ ?>
<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          3.01
 */


//$current_page > 1
 if (PHPFOX_IS_AJAX): ?>
<?php if (( isset ( $this->_aVars['articlecategories'] ) )): ?>
<?php if (count((array)$this->_aVars['articlecategories'])):  foreach ((array) $this->_aVars['articlecategories'] as $this->_aVars['articlecategory']): ?>
<?php if (count ( $this->_aVars['articlecategory']['article'] ) > 0): ?>
<?php if (count((array)$this->_aVars['articlecategory']['article'])):  foreach ((array) $this->_aVars['articlecategory']['article'] as $this->_aVars['article']): ?>
					<li class="row">
						<a class="item_title item_view_content" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('gettingstarted.article'); ?>article_<?php echo $this->_aVars['article']['article_id']; ?>"><?php echo $this->_aVars['article']['title']; ?></a>
						<div class="extra_info"><?php echo _p('gettingstarted.posted_on_post_time', array('post_time' => $this->_aVars['article']['post_time']));  if ($this->_aVars['article']['article_category_id'] != -1): ?> - <?php echo _p('gettingstarted.in'); ?> <a class="level_label" href="<?php echo Phpfox::permalink('gettingstarted.categories', $this->_aVars['article']['article_category_id'], Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['article']['article_category_name'])), false, null, (array) array (
)); ?>view_/"><?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['article']['article_category_name'])); ?></a><?php endif; ?></div>
					</li>
<?php endforeach; endif; ?>
<?php if ($this->_aVars['flag'] == 1): ?>
<?php if (!isset($this->_aVars['aPager'])): Phpfox::getLib('pager')->set(array('page' => Phpfox::getLib('request')->getInt('page'), 'size' => Phpfox::getLib('search')->getDisplay(), 'count' => Phpfox::getLib('search')->getCount())); endif;  $this->getLayout('pager'); ?>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php endif; ?>

<?php else: ?>

<?php if ($this->_aVars['bIsSearch'] == true): ?>
<?php if ($this->_aVars['iCnt'] > 0): ?>
<?php if (count((array)$this->_aVars['articlecategories'])):  foreach ((array) $this->_aVars['articlecategories'] as $this->_aVars['articlecategory']): ?>
         <div class="block kblist_block">
<?php if (count ( $this->_aVars['articlecategory']['article'] ) > 0 && $this->_aVars['bIsCategory'] == false): ?>
   			<div class="title">
                   <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('gettingstarted.categories');  echo $this->_aVars['articlecategory']['article_category_id']; ?>/<?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['articlecategory']['name_url'])); ?>"><?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['articlecategory']['article_category_name'])); ?></a>
           	</div>
<?php endif; ?>
<?php if (count ( $this->_aVars['articlecategory']['article'] ) > 0): ?>
   			<div class="content kb_listing_holder">
       			<ul>
       				
<?php if (count((array)$this->_aVars['articlecategory']['article'])):  foreach ((array) $this->_aVars['articlecategory']['article'] as $this->_aVars['article']): ?>
   							<li class="row">
   								<a class="item_title item_view_content" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('gettingstarted.article'); ?>article_<?php echo $this->_aVars['article']['article_id']; ?>"><?php echo $this->_aVars['article']['title']; ?></a>
   								<div class="extra_info"><?php echo _p('gettingstarted.posted_on_post_time', array('post_time' => $this->_aVars['article']['post_time']));  if ($this->_aVars['article']['article_category_id'] != -1): ?> - <?php echo _p('gettingstarted.in'); ?> <a class="level_label" href="<?php echo Phpfox::permalink('gettingstarted.categories', $this->_aVars['article']['article_category_id'], Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['article']['article_category_name'])), false, null, (array) array (
)); ?>view_/"><?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['article']['article_category_name'])); ?></a><?php endif; ?></div>
       						</li>
<?php endforeach; endif; ?>
<?php if ($this->_aVars['flag'] == 1): ?>
<?php if (!isset($this->_aVars['aPager'])): Phpfox::getLib('pager')->set(array('page' => Phpfox::getLib('request')->getInt('page'), 'size' => Phpfox::getLib('search')->getDisplay(), 'count' => Phpfox::getLib('search')->getCount())); endif;  $this->getLayout('pager'); ?>
<?php endif; ?>
       				
<?php if ($this->_aVars['articlecategory']['pagination'] == 1 && $this->_aVars['bIsCategory'] == false): ?>
       					<li class="t_right">
             					<a href="<?php echo Phpfox::permalink('gettingstarted.categories', $this->_aVars['articlecategory']['article_category_id'], Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['articlecategory']['article_category_name'])), false, null, (array) array (
)); ?>view_/search-id_<?php echo $this->_aVars['search_id']; ?>/"><?php echo _p('gettingstarted.search_more_in'); ?>: <?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['articlecategory']['article_category_name'])); ?></a>
   						</li>
<?php endif; ?>
       				
       			</ul>
   			</div>
<?php endif; ?>
      </div>
<?php endforeach; endif; ?>
		
<?php else: ?>

<?php if ($this->_aVars['iPage'] < 2): ?>
			<div class="extra_info noresult_msg">
<?php echo _p('gettingstarted.no_articles_found'); ?>
			</div>
<?php endif; ?>
<?php endif;  else: ?>

<?php if (count ( $this->_aVars['articlecategories'] ) > 0): ?>
<?php if (count((array)$this->_aVars['articlecategories'])):  foreach ((array) $this->_aVars['articlecategories'] as $this->_aVars['articlecategory']): ?>
       <div class="block kblist_block">
       	
<?php if ($this->_aVars['bIsCategory'] == false): ?>

			<div  class="title">
                <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('gettingstarted.categories');  echo $this->_aVars['articlecategory']['article_category_id']; ?>/<?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['articlecategory']['name_url'])); ?>"><?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['articlecategory']['article_category_name'])); ?></a>
        	</div>
<?php endif; ?>
			<div class="content kb_listing_holder">
    			<ul>
<?php if (count ( $this->_aVars['articlecategory']['article'] ) > 0): ?>
<?php if (count((array)$this->_aVars['articlecategory']['article'])):  foreach ((array) $this->_aVars['articlecategory']['article'] as $this->_aVars['article']): ?>
							<li class="row">
								<a class="item_title item_view_content" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('gettingstarted.article'); ?>article_<?php echo $this->_aVars['article']['article_id']; ?>"><?php echo $this->_aVars['article']['title']; ?></a>
								<div class="extra_info"><?php echo _p('gettingstarted.posted_on_post_time', array('post_time' => $this->_aVars['article']['post_time']));  if ($this->_aVars['article']['article_category_id'] != -1): ?> - <?php echo _p('gettingstarted.in'); ?> <a class="level_label" href="<?php echo Phpfox::permalink('gettingstarted.categories', $this->_aVars['article']['article_category_id'], Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['article']['article_category_name'])), false, null, (array) array (
)); ?>view_/"><?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['article']['article_category_name'])); ?></a><?php endif; ?></div>
    						</li>
<?php endforeach; endif; ?>
<?php if ($this->_aVars['flag'] == 1): ?>
<?php if (!isset($this->_aVars['aPager'])): Phpfox::getLib('pager')->set(array('page' => Phpfox::getLib('request')->getInt('page'), 'size' => Phpfox::getLib('search')->getDisplay(), 'count' => Phpfox::getLib('search')->getCount())); endif;  $this->getLayout('pager'); ?>
<?php endif; ?>
<?php else: ?>
<?php if ($this->_aVars['iPage'] < 2): ?>
							<li class="row">
<?php echo _p('gettingstarted.no_articles_have_been_added_yet'); ?>
							</li>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->_aVars['articlecategory']['pagination'] == 1 && $this->_aVars['bIsCategory'] == false): ?>
    					<li class="t_right">
          					<a href="<?php echo Phpfox::permalink('gettingstarted.categories', $this->_aVars['articlecategory']['article_category_id'], Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['articlecategory']['article_category_name'])), false, null, (array) array (
)); ?>view_/"><?php echo _p('gettingstarted.view_more'); ?>: <?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['articlecategory']['article_category_name'])); ?></a>
						</li>
<?php endif; ?>
    			</ul>
			</div>
      </div>
<?php endforeach; endif; ?>

<?php else: ?>

<?php if ($this->_aVars['iPage'] < 2): ?>
			<li class="row">
<?php echo _p('gettingstarted.no_articles_have_been_added_yet'); ?>
			</li>
<?php endif; ?>

<?php endif;  endif; ?>

<?php echo '
<style type="text/css">
.header_filter_holder
{
    display:none;
}
.emoticon_preview, .emoticon_preview:hover {
    width: auto!important;
}
</style>
'; ?>




<?php endif; ?>
