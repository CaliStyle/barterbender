<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 22, 2020, 5:14 pm */ ?>
<?php 

 

 if ($this->_aVars['sBookmarkDisplay'] == 'menu' && ! empty ( $this->_aVars['sShareModuleId'] )): ?>
<li class=""><a href="#" title="<?php echo _p('share'); ?>" onclick="tb_show('<?php echo _p('share', array('phpfox_squote' => true)); ?>', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type=<?php echo $this->_aVars['sBookmarkType']; ?>&amp;url=<?php echo $this->_aVars['sBookmarkUrl']; ?>&amp;title=<?php echo $this->_aVars['sBookmarkTitle'];  if (isset ( $this->_aVars['sFeedShareId'] ) && $this->_aVars['sFeedShareId'] > 0): ?>&amp;feed_id=<?php echo $this->_aVars['sFeedShareId'];  endif;  if (isset ( $this->_aVars['sFeedType'] )): ?>&amp;is_feed_view=1<?php endif; ?>&amp;sharemodule=<?php echo $this->_aVars['sShareModuleId']; ?>')); return false;"<?php if ($this->_aVars['bIsFirstLink']): ?> class="first"<?php endif; ?>><?php echo $this->_aVars['sExtraContent'];  echo _p('share'); ?></a></li>
<?php elseif ($this->_aVars['sBookmarkDisplay'] == 'menu_btn' && ! empty ( $this->_aVars['sShareModuleId'] )): ?>
<a href="#" title="<?php echo _p('share'); ?>" onclick="tb_show('<?php echo _p('share', array('phpfox_squote' => true)); ?>', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type=<?php echo $this->_aVars['sBookmarkType']; ?>&amp;url=<?php echo $this->_aVars['sBookmarkUrl']; ?>&amp;title=<?php echo $this->_aVars['sBookmarkTitle'];  if (isset ( $this->_aVars['sFeedShareId'] ) && $this->_aVars['sFeedShareId'] > 0): ?>&amp;feed_id=<?php echo $this->_aVars['sFeedShareId'];  endif;  if (isset ( $this->_aVars['sFeedType'] )): ?>&amp;is_feed_view=1<?php endif; ?>&amp;sharemodule=<?php echo $this->_aVars['sShareModuleId']; ?>')); return false;"<?php if ($this->_aVars['bIsFirstLink']): ?> class="first"<?php endif; ?>>
<i class="fa fa-share"></i></a>
<?php elseif ($this->_aVars['sBookmarkDisplay'] == 'menu_link' && ! empty ( $this->_aVars['sShareModuleId'] )): ?>
<li><a href="#" title="<?php echo _p('share'); ?>" onclick="tb_show('<?php echo _p('share', array('phpfox_squote' => true)); ?>', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type=<?php echo $this->_aVars['sBookmarkType']; ?>&amp;url=<?php echo $this->_aVars['sBookmarkUrl']; ?>&amp;title=<?php echo $this->_aVars['sBookmarkTitle']; ?>')); return false;"<?php if ($this->_aVars['bIsFirstLink']): ?> class="first"<?php endif; ?>><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'icon/share.png','class' => 'item_bar_image')); ?> <?php echo _p('share'); ?></a></li>
<?php elseif ($this->_aVars['sBookmarkDisplay'] == 'image' && ! empty ( $this->_aVars['sShareModuleId'] )): ?>
<a href="#" title="<?php echo _p('share'); ?>" onclick="tb_show('<?php echo _p('share', array('phpfox_squote' => true)); ?>', $.ajaxBox('share.popup', 'height=300&amp;width=350&amp;type=<?php echo $this->_aVars['sBookmarkType']; ?>&amp;url=<?php echo $this->_aVars['sBookmarkUrl']; ?>&amp;title=<?php echo $this->_aVars['sBookmarkTitle']; ?>')); return false;"><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'misc/icn_share.png','class' => 'v_middle')); ?> <?php echo _p('share'); ?></a>
<?php elseif (! empty ( $this->_aVars['sShareModuleId'] )): ?>
<a href="#"><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'misc/add.png','alt' => '','style' => 'vertical-align:middle;')); ?></a> <a href="#" title="<?php echo _p('share'); ?>" onclick="tb_show('<?php echo _p('share', array('phpfox_squote' => true)); ?>', $.ajaxBox('share.popup', 'height=300&amp;width=350&amp;type=<?php echo $this->_aVars['sBookmarkType']; ?>&amp;url=<?php echo $this->_aVars['sBookmarkUrl']; ?>&amp;title=<?php echo $this->_aVars['sBookmarkTitle']; ?>')); return false;"><?php echo _p('share'); ?></a>
<?php endif; ?>
