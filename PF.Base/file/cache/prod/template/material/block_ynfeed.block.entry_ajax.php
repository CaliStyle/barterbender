<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 22, 2020, 5:14 pm */ ?>
<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: entry.html.php 5840 2013-05-09 06:14:35Z Raymond_Benc $
 */
 
 

 $this->assign('feed_entry_be', true); ?>
<div data-feed-id="<?php echo $this->_aVars['aFeed']['feed_id']; ?>"
	 data-user-id="<?php echo $this->_aVars['aFeed']['user_id']; ?>"
	 data-user-fullname="<?php echo $this->_aVars['aFeed']['full_name']; ?>"
	 data-user-profile="<?php echo $this->_aVars['aFeed']['user_profile']; ?>"
	 data-feed-update="<?php echo $this->_aVars['aFeed']['time_update']; ?>"
	 class="yncfeed-feed-item <?php if (( isset ( $this->_aVars['sponsor'] ) && $this->_aVars['sponsor'] ) || ( isset ( $this->_aVars['aFeed']['sponsored_feed'] ) && $this->_aVars['aFeed']['sponsored_feed'] )): ?>sponsor<?php endif; ?> _app_<?php echo $this->_aVars['aFeed']['type_id']; ?> js_parent_feed_entry js_user_feed feed-stream-content" id="js_item_feed_<?php echo $this->_aVars['aFeed']['feed_id']; ?>">
<?php (($sPlugin = Phpfox_Plugin::get('feed.template_block_entry_1')) ? eval($sPlugin) : false); ?>
	<div class="activity_feed_image">
<?php if (! isset ( $this->_aVars['aFeed']['feed_mini'] )): ?>
<?php if (isset ( $this->_aVars['aFeed']['is_custom_app'] ) && $this->_aVars['aFeed']['is_custom_app'] && ( ( isset ( $this->_aVars['aFeed']['view_id'] ) && $this->_aVars['aFeed']['view_id'] == 7 ) || ( isset ( $this->_aVars['aFeed']['gender'] ) && $this->_aVars['aFeed']['gender'] < 1 ) )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => 0,'path' => 'app.url_image','file' => $this->_aVars['aFeed']['app_image_path'],'suffix' => '_square','max_width' => 50,'max_height' => 50)); ?>
<?php else: ?>
<?php if (isset ( $this->_aVars['aFeed']['user_name'] ) && ! empty ( $this->_aVars['aFeed']['user_name'] )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aFeed'],'suffix' => '_50_square','max_width' => 50,'max_height' => 50)); ?>
<?php else: ?>
<?php if (! empty ( $this->_aVars['aFeed']['parent_user_name'] )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aFeed'],'suffix' => '_50_square','max_width' => 50,'max_height' => 50,'href' => $this->_aVars['aFeed']['parent_user_name'])); ?>
<?php else: ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aFeed'],'suffix' => '_50_square','max_width' => 50,'max_height' => 50,'href' => '')); ?>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
	</div>

	<?php
						Phpfox::getLib('template')->getBuiltFile('ynfeed.block.content');
						?>

<?php (($sPlugin = Phpfox_Plugin::get('feed.template_block_entry_3')) ? eval($sPlugin) : false); ?>
</div>
