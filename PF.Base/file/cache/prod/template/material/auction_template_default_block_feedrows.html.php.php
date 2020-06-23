<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<div class="auction-app feed <?php if (empty ( $this->_aVars['aAuction']['image_path'] )): ?>no-photo<?php endif; ?>">
    <div class="auction-media">
        <a class="item-media-bg" href="<?php echo Phpfox::permalink('auction.detail', $this->_aVars['aAuction']['product_id'], $this->_aVars['aAuction']['name'], false, null, (array) array (
)); ?>"
           style="background-image: url(
<?php if (isset ( $this->_aVars['aAuction']['image_path'] )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aAuction']['server_id'],'path' => 'core.url_pic','file' => $this->_aVars['aAuction']['image_path'],'suffix' => '_1024','return_url' => true)); ?>
<?php else: ?>
<?php echo $this->_aVars['aAuction']['default_logo_path']; ?>
<?php endif; ?>
           )">
        </a>
    </div>
    <div class="auction-inner pl-2 pr-2">
        <a href="<?php echo Phpfox::permalink('auction.detail', $this->_aVars['aAuction']['product_id'], $this->_aVars['aAuction']['name'], false, null, (array) array (
)); ?>" class="auction-title fw-bold"><?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aAuction']['name']); ?></a>
        <div class="auction-description item_view_content"><?php echo Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('parse.output')->feedStrip(Phpfox::getLib('phpfox.parse.bbcode')->stripCode($this->_aVars['aAuction']['description_parsed'])), 55); ?></div>
    </div>
</div>
