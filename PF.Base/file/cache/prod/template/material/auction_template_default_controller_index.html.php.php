<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:46 pm */ ?>
<?php



 if ($this->_aVars['iPage'] == 0): ?>
<div id="ynauction_index">
	<div class="ynauction-hiddenblock">
		<input type="hidden" value="index" id="ynauction_pagename" name="ynauction_pagename">
	</div>
	<div>
		<input type="hidden" id='ynauction_condition' name="ynauction_condition" value="<?php if (isset ( $this->_aVars['sCondition'] )):  echo $this->_aVars['sCondition'];  endif; ?>">
	</div>
	<div>
<?php endif; ?>
<?php if ($this->_aVars['bIsHomepage']): ?>
        <div id="yndirectory_homepage">
<?php Phpfox::getBlock('auction.featured-auctions', array()); ?>
<?php Phpfox::getBlock('auction.weekly-hot-auctions', array()); ?>
<?php Phpfox::getBlock('auction.new-auctions', array('page' => $this->_aVars['iPage'],'viewType' => $this->_aVars['sViewType'],'aItem' => $this->_aVars['aItems'],'iCnt' => $this->_aVars['iCnt'])); ?>
        </div>

<?php else: ?>
<?php if (! count ( $this->_aVars['aItems'] )): ?>
<?php if ($this->_aVars['iPage'] <= 1): ?>
            <div class="extra_info">
<?php echo _p('no_auctions_found'); ?>
            </div>
<?php endif; ?>
<?php else: ?>
<?php if (( $this->_aVars['sView'] != 'myauctions' ) && $this->_aVars['bIsProfile'] == false): ?>
<?php if ($this->_aVars['sView'] == 'friend'): ?>
                    <div class="ynauction-content">
                        <div id="ynauction_listview" class="ynauction_listview homepage-view ynauction-clearfix">
<?php if (count((array)$this->_aVars['aItems'])):  $this->_aPhpfoxVars['iteration']['auction'] = 0;  foreach ((array) $this->_aVars['aItems'] as $this->_aVars['aProduct']):  $this->_aPhpfoxVars['iteration']['auction']++; ?>

<?php if ($this->_aVars['aProduct']['user_group_id'] != 5): ?>
                                    <?php
						Phpfox::getLib('template')->getBuiltFile('auction.block.listing-product-item-listview');
						?>
<?php endif; ?>
<?php endforeach; endif; ?>
                        </div>
                    </div>
<?php elseif ($this->_aVars['sView'] == 'bidden-by-my-friends'): ?>
                    <div class="ynauction-content">
                        <div id="ynauction_listview" class="ynauction_listview homepage-view ynauction-clearfix">
<?php if (count((array)$this->_aVars['aItems'])):  $this->_aPhpfoxVars['iteration']['auction'] = 0;  foreach ((array) $this->_aVars['aItems'] as $this->_aVars['aProduct']):  $this->_aPhpfoxVars['iteration']['auction']++; ?>

<?php if ($this->_aVars['aProduct']['usergroupId'] != 5): ?>
                                    <?php
						Phpfox::getLib('template')->getBuiltFile('auction.block.listing-product-item-listview');
						?>
<?php endif; ?>
<?php endforeach; endif; ?>
                        </div>
                    </div>
<?php else: ?>
<?php if ($this->_aVars['iPage'] == 0): ?>
<?php if (( isset ( $this->_aVars['sortTitle'] ) && ( $this->_aVars['sortTitle'] != "" ) )): ?>
                            <h1><a href="<?php echo $this->_aVars['sortUrl']; ?>"><?php echo $this->_aVars['sortTitle']; ?></a></h1>
<?php endif; ?>
<?php endif; ?>
                    <div class="ynauction-content">
                        <div id="ynauction_listview" class="ynauction_listview homepage-view ynauction-clearfix">
<?php if (count((array)$this->_aVars['aItems'])):  $this->_aPhpfoxVars['iteration']['auction'] = 0;  foreach ((array) $this->_aVars['aItems'] as $this->_aVars['aProduct']):  $this->_aPhpfoxVars['iteration']['auction']++; ?>

                                <?php
						Phpfox::getLib('template')->getBuiltFile('auction.block.listing-product-item-listview');
						?>
<?php endforeach; endif; ?>
                        </div>
                    </div>
<?php endif; ?>
<?php elseif ($this->_aVars['sView'] == 'myauctions' || $this->_aVars['bIsProfile']): ?>
                <div class="ynauction-content">
                    <div id="ynauction_gridview" class="ynauction_gridview">
<?php if (count((array)$this->_aVars['aItems'])):  $this->_aPhpfoxVars['iteration']['auction'] = 0;  foreach ((array) $this->_aVars['aItems'] as $this->_aVars['aProduct']):  $this->_aPhpfoxVars['iteration']['auction']++; ?>

                            <?php
						Phpfox::getLib('template')->getBuiltFile('auction.block.listing-product-item-gridview');
						?>
<?php endforeach; endif; ?>
                    </div>
                </div>
<?php endif; ?>
<?php if (!isset($this->_aVars['aPager'])): Phpfox::getLib('pager')->set(array('page' => Phpfox::getLib('request')->getInt('page'), 'size' => Phpfox::getLib('search')->getDisplay(), 'count' => Phpfox::getLib('search')->getCount())); endif;  $this->getLayout('pager'); ?>
<?php if ($this->_aVars['sView'] == 'myauctions' || $this->_aVars['sView'] == 'pending'): ?>
<?php if (! PHPFOX_IS_AJAX && $this->_aVars['bShowModerator']): ?>
<?php Phpfox::getBlock('core.moderation'); ?>
<?php endif; ?>
<?php endif; ?>
                    <div class="clear"></div>
<?php endif; ?>
<?php endif;  if ($this->_aVars['iPage'] == 0): ?>
    </div>
</div>
<?php endif;  echo '
<script>
var first = 0;
    $Behavior.initAuctionIndexPage = function(){
        ynauction.changeViewHomePage(first);
        if(first == 0){
            first = 1;
        }
        ynauction.initAdvancedSearch();
    }
</script>
'; ?>

