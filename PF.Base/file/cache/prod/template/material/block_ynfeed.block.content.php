<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: content.html.php 7160 2014-02-26 17:20:13Z Fern $
 */



 if (! isset ( $this->_aVars['aFeed']['feed_mini'] )): ?>
<div class="activity_feed_header">
	<div class="activity_feed_header_info">

<?php echo '<span class="user_profile_link_span" id="js_user_name_link_' . $this->_aVars['aFeed']['user_name'] . '">' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aFeed']['user_id']) ? '' : '<a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aFeed']['user_name'], ((empty($this->_aVars['aFeed']['user_name']) && isset($this->_aVars['aFeed']['profile_page_id'])) ? $this->_aVars['aFeed']['profile_page_id'] : null))) . '">') . '' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getService('user')->getCurrentName($this->_aVars['aFeed']['user_id'], $this->_aVars['aFeed']['full_name'])), 50, '...') . '' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aFeed']['user_id']) ? '' : '</a>') . '</span>';  if (( ! empty ( $this->_aVars['aFeed']['parent_module_id'] ) || isset ( $this->_aVars['aFeed']['parent_is_app'] ) )): ?> <?php echo _p('shared');  else:  if (isset ( $this->_aVars['aFeed']['parent_user'] )): ?> <span class="ico ico-caret-right"></span> <?php echo '<span class="user_profile_link_span" id="js_user_name_link_' . $this->_aVars['aFeed']['parent_user']['parent_user_name'] . '">' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aFeed']['parent_user']['parent_user_id']) ? '' : '<a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aFeed']['parent_user']['parent_user_name'], ((empty($this->_aVars['aFeed']['parent_user']['parent_user_name']) && isset($this->_aVars['aFeed']['parent_user']['parent_profile_page_id'])) ? $this->_aVars['aFeed']['parent_user']['parent_profile_page_id'] : null))) . '">') . '' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getService('user')->getCurrentName($this->_aVars['aFeed']['parent_user']['parent_user_id'], $this->_aVars['aFeed']['parent_user']['parent_full_name'])), 50, '...') . '' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aFeed']['parent_user']['parent_user_id']) ? '' : '</a>') . '</span>'; ?> <?php endif;  if (! empty ( $this->_aVars['aFeed']['feed_info'] )): ?><span class="feed_info"> <?php echo $this->_aVars['aFeed']['feed_info']; ?> </span><?php endif;  endif; ?>

        <!--Extra info-->
<?php if (( isset ( $this->_aVars['aFeed']['aFeeling'] ) && ! empty ( $this->_aVars['aFeed']['aFeeling'] ) ) || ( isset ( $this->_aVars['aFeed']['sTagInfo'] ) && $this->_aVars['aFeed']['sTagInfo'] ) || ( Phpfox ::getParam('feed.enable_check_in') && Phpfox ::getParam('core.google_api_key') != '' && isset ( $this->_aVars['aFeed']['location_name'] ) && isset ( $this->_aVars['aFeed']['location_latlng']['latitude'] ) ) || ( isset ( $this->_aVars['aFeed']['aBusiness'] ) && $this->_aVars['aFeed']['aBusiness']['business_id'] )): ?>
<?php if (! empty ( $this->_aVars['aFeed']['feed_info'] ) || isset ( $this->_aVars['aFeed']['parent_user'] )): ?> - <?php else:  echo _p('was');  endif; ?>

            <!--Feeling-->
<?php if (isset ( $this->_aVars['aFeed']['aFeeling'] ) && ! empty ( $this->_aVars['aFeed']['aFeeling'] )): ?>
            <span>
                <img src="<?php echo $this->_aVars['aFeed']['aFeeling']['image']; ?>" class="ynfeed_feeling_icon"> <?php echo _p('feeling'); ?>
                <span><?php echo $this->_aVars['aFeed']['aFeeling']['title_translated']; ?></span>
            </span>
<?php endif; ?>

<?php if (isset ( $this->_aVars['aFeed']['sTagInfo'] ) && $this->_aVars['aFeed']['sTagInfo'] != ''): ?>
            <span>
<?php echo $this->_aVars['aFeed']['sTagInfo']; ?>
            </span>
<?php endif; ?>

            <!--Checkin location-->
<?php if (Phpfox ::getParam('feed.enable_check_in') && Phpfox ::getParam('core.google_api_key') != '' && isset ( $this->_aVars['aFeed']['location_name'] ) && isset ( $this->_aVars['aFeed']['location_latlng']['latitude'] ) && isset ( $this->_aVars['aFeed']['location_latlng']['longitude'] )): ?>
            <span>
                 <span class="js_location_name_hover" <?php if (isset ( $this->_aVars['aFeed']['location_latlng'] ) && isset ( $this->_aVars['aFeed']['location_latlng']['latitude'] )): ?>onmouseover="" <?php endif; ?>>
<?php echo _p("at_lowercase"); ?>
                        <a href="<?php if (Phpfox ::getParam('core.force_https_secure_pages')): ?>https://<?php else: ?>http://<?php endif; ?>maps.google.com/maps?daddr=<?php echo $this->_aVars['aFeed']['location_latlng']['latitude']; ?>,<?php echo $this->_aVars['aFeed']['location_latlng']['longitude']; ?>"
                           target="_blank"><?php echo $this->_aVars['aFeed']['location_name']; ?></a>
                 </span>
            </span>
<?php endif; ?>
            <!-- Map here, only show map with user status, other types will display it's main content, ex: photo-->

<?php if (isset ( $this->_aVars['aFeed']['business_id'] ) && isset ( $this->_aVars['aFeed']['aBusiness'] ) && $this->_aVars['aFeed']['aBusiness']['business_id']): ?>
            <span>
<?php echo _p("at_lowercase"); ?>
                 <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aFeed']['aBusiness']['url']); ?>" target="_blank"><?php echo $this->_aVars['aFeed']['aBusiness']['name']; ?></a>
            </span>
<?php endif; ?>
<?php endif; ?>
        <!--End extra info-->
        <div class="activity-feed-time-privacy-block">
           <time>
                <a href="<?php echo $this->_aVars['aFeed']['feed_link']; ?>" class="feed_permalink"><?php echo Phpfox::getLib('date')->convertTime($this->_aVars['aFeed']['time_stamp'], 'feed.feed_display_time_stamp'); ?></a>
<?php if (( isset ( $this->_aVars['sponsor'] ) && $this->_aVars['sponsor'] ) || ( isset ( $this->_aVars['aFeed']['sponsored_feed'] ) && $this->_aVars['aFeed']['sponsored_feed'] )): ?>
            <span>
                <b><?php echo _p('sponsored'); ?></b>
            </span>
<?php endif; ?>
            </time>
<?php if (! empty ( $this->_aVars['aFeed']['privacy_icon_class'] )): ?>
            <span class="<?php echo $this->_aVars['aFeed']['privacy_icon_class']; ?>"></span>
<?php endif; ?>
        </div>
	</div>
</div>
<?php endif; ?>

<div class="activity_feed_content">
<?php if (( isset ( $this->_aVars['aFeed']['focus'] ) )): ?>
	<div data-is-focus="1">
<?php echo $this->_aVars['aFeed']['focus']['html']; ?>
	</div>
<?php else: ?>
		<?php
						Phpfox::getLib('template')->getBuiltFile('ynfeed.block.focus');
						?>
<?php endif; ?>

<?php if (isset ( $this->_aVars['aFeed']['feed_view_comment'] )): ?>
<?php Phpfox::getBlock('feed.comment', array()); ?>
<?php else: ?>
		<?php
						Phpfox::getLib('template')->getBuiltFile('feed.block.comment');
						?>
<?php endif; ?>

<?php if ($this->_aVars['aFeed']['type_id'] != 'friend'): ?>
<?php if (isset ( $this->_aVars['aFeed']['more_feed_rows'] ) && is_array ( $this->_aVars['aFeed']['more_feed_rows'] ) && count ( $this->_aVars['aFeed']['more_feed_rows'] )): ?>
<?php if ($this->_aVars['iTotalExtraFeedsToShow'] = count ( $this->_aVars['aFeed']['more_feed_rows'] )):  endif; ?>
			<a href="#" class="activity_feed_content_view_more" onclick="$(this).parents('.js_feed_view_more_entry_holder:first').find('.js_feed_view_more_entry').show(); $(this).remove(); return false;"><?php echo _p('see_total_more_posts_from_full_name', array('total' => $this->_aVars['iTotalExtraFeedsToShow'],'full_name' => Phpfox::getLib('phpfox.parse.output')->shorten($this->_aVars['aFeed']['full_name'], 40, '...'))); ?></a>
<?php endif; ?>
<?php endif; ?>

	<?php
						Phpfox::getLib('template')->getBuiltFile('ynfeed.block.share.external');
						?>
</div>
