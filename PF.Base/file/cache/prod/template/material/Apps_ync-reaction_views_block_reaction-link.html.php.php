<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond_Benc
 * @package          Phpfox
 * @version          $Id: link.html.php 6671 2013-09-25 10:06:46Z Fern $
 */



 if ($this->_aVars['aLike']['like_type_id'] == 'feed_mini'): ?>
<?php endif; ?>
    <div class="ync-reaction-container ync-reaction-container-js">
        <a role="button"
         data-toggle="ync_reaction_toggle_cmd"
         data-label1="<?php echo _p('like'); ?>"
         data-label2="<?php echo _p('unlike'); ?>"
         data-liked="<?php if ($this->_aVars['aLike']['like_is_liked']): ?>1<?php else: ?>0<?php endif; ?>"
         data-type_id="<?php echo $this->_aVars['aLike']['like_type_id']; ?>"
         data-item_id="<?php echo $this->_aVars['aLike']['like_item_id']; ?>"
         data-reaction_color="<?php echo $this->_aVars['aYncLike']['color']; ?>"
         data-reaction_id="<?php echo $this->_aVars['aYncLike']['id']; ?>"
         data-reaction_title="<?php echo _p(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aYncLike']['title'])); ?>"
         data-full_path="<?php echo $this->_aVars['aYncLike']['full_path']; ?>"
         data-feed_id="<?php if (isset ( $this->_aVars['aFeed']['feed_id'] )):  echo $this->_aVars['aFeed']['feed_id'];  else: ?>0<?php endif; ?>"
         data-is_custom="<?php if ($this->_aVars['aLike']['like_is_custom']): ?>1<?php else: ?>0<?php endif; ?>"
         data-table_prefix="<?php if (isset ( $this->_aVars['aFeed']['feed_table_prefix'] )):  echo $this->_aVars['aFeed']['feed_table_prefix'];  elseif (defined ( 'PHPFOX_IS_PAGES_VIEW' ) && defined ( 'PHPFOX_PAGES_ITEM_TYPE' )): ?>pages_<?php endif; ?>"
         class="js_like_link_toggle <?php if ($this->_aVars['aLike']['like_is_liked']): ?>liked<?php else: ?>unlike<?php endif; ?> ync-reaction-link" style="-webkit-user-select: none; -webkit-touch-callout: none;">
<?php if ($this->_aVars['aLike']['like_is_liked'] && ! empty ( $this->_aVars['aUserReacted'] )): ?>
                <div class="ync-reacted-icon-outer"><img src="<?php echo $this->_aVars['aUserReacted']['full_path']; ?>" alt="" class="ync-reacted-icon" oncontextmenu="return false;"> </div><?php echo yncreaction_color_title($this->_aVars['aUserReacted']); ?>
<?php else: ?>
                <div class="ync-reacted-icon-outer"></div>
                <strong class="ync-reaction-title"></strong>
<?php endif; ?>
        </a>
<?php if (! empty ( $this->_aVars['aYncReactions'] ) && count ( $this->_aVars['aYncReactions'] ) > 1): ?>
            <div class="ync-reaction-list">
<?php if (count((array)$this->_aVars['aYncReactions'])):  foreach ((array) $this->_aVars['aYncReactions'] as $this->_aVars['aYncReaction']): ?>
                <div class="ync-reaction-item dont-unbind " data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _p(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aYncReaction']['title'])); ?>">

                    <a class="item-outer"
                       data-toggle="ync_reaction_toggle_cmd"
                       data-label1="<?php echo _p('like'); ?>"
                       data-label2="<?php echo _p('unlike'); ?>"
                       data-liked="<?php if ($this->_aVars['aLike']['like_is_liked']): ?>1<?php else: ?>0<?php endif; ?>"
                       data-type_id="<?php echo $this->_aVars['aLike']['like_type_id']; ?>"
                       data-reaction_color="<?php echo $this->_aVars['aYncReaction']['color']; ?>"
                       data-reaction_id="<?php echo $this->_aVars['aYncReaction']['id']; ?>"
                       data-reaction_title="<?php echo _p(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aYncReaction']['title'])); ?>"
                       data-full_path="<?php echo $this->_aVars['aYncReaction']['full_path']; ?>"
                       data-item_id="<?php echo $this->_aVars['aLike']['like_item_id']; ?>"
                       data-feed_id="<?php if (isset ( $this->_aVars['aFeed']['feed_id'] )):  echo $this->_aVars['aFeed']['feed_id'];  else: ?>0<?php endif; ?>"
                       data-is_custom="<?php if ($this->_aVars['aLike']['like_is_custom']): ?>1<?php else: ?>0<?php endif; ?>"
                       data-table_prefix="<?php if (isset ( $this->_aVars['aFeed']['feed_table_prefix'] )):  echo $this->_aVars['aFeed']['feed_table_prefix'];  elseif (defined ( 'PHPFOX_IS_PAGES_VIEW' ) && defined ( 'PHPFOX_PAGES_ITEM_TYPE' )): ?>pages_<?php endif; ?>"
                       style="-webkit-user-select: none;"
                       title="<?php echo _p(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aYncReaction']['title'])); ?>"
                    >
                        <img src="<?php echo $this->_aVars['aYncReaction']['full_path']; ?>" alt="">
                    </a>
                </div>
<?php endforeach; endif; ?>
            </div>
<?php endif; ?>
    </div>

<?php if ($this->_aVars['aLike']['like_type_id'] == 'feed_mini' && ! empty ( $this->_aVars['aLike']['like_is_custom'] )): ?>
<?php if (isset ( $this->_aVars['aFeed']['feed_table_prefix'] )): ?>
<?php $this->assign('sPrefixTable', $this->_aVars['aFeed']['feed_table_prefix']); ?>
<?php elseif (defined ( 'PHPFOX_IS_PAGES_VIEW' ) && defined ( 'PHPFOX_PAGES_ITEM_TYPE' )): ?>
<?php $this->assign('sPrefixTable', 'pages_'); ?>
<?php endif; ?>
<?php Phpfox::getBlock('yncreaction.reaction-list-mini', array('type_id' => 'feed_mini','item_id' => $this->_aVars['aLike']['like_item_id'],'table_prefix' => $this->_aVars['sPrefixTable']));  endif; ?>
