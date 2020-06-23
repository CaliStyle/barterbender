<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox LLC
 * @package  		Module_Core
 * @version 		$Id: index-member.html.php 2817 2011-08-08 16:59:43Z phpFox LLC $
 */
 
 

 if (count ( $this->_aVars['aPhpfoxNews'] )): ?>
<?php if ($this->_aVars['isSlide']): ?>
        <div id="carousel-fox-news" class="carousel slide block" data-ride="carousel">
            <ol class="carousel-indicators">
<?php if (count((array)$this->_aVars['aPhpfoxNews'])):  $this->_aPhpfoxVars['iteration']['news'] = 0;  foreach ((array) $this->_aVars['aPhpfoxNews'] as $this->_aVars['iKey'] => $this->_aVars['aNewsSlide']):  $this->_aPhpfoxVars['iteration']['news']++; ?>

                    <li data-target="#carousel-fox-news" data-slide-to="<?php echo $this->_aVars['iKey']; ?>" class="<?php if ($this->_aVars['iKey'] == 0): ?> active <?php endif; ?>"></li>
<?php endforeach; endif; ?>
            </ol>
            <div class="carousel-inner slider-fox-news-container">
<?php if (count((array)$this->_aVars['aPhpfoxNews'])):  $this->_aPhpfoxVars['iteration']['news'] = 0;  foreach ((array) $this->_aVars['aPhpfoxNews'] as $this->_aVars['iKey'] => $this->_aVars['aNewsSlide']):  $this->_aPhpfoxVars['iteration']['news']++; ?>

                <div class="item <?php if ($this->_aVars['iKey'] == 0): ?> active <?php endif; ?>">
                    <div class="item-outer">
<?php if ($this->_aVars['aNewsSlide']['image']): ?>
                        <div class="item-media">
                            <span style="background-image: url(<?php echo $this->_aVars['aNewsSlide']['image']; ?>)"></span>
                        </div>
<?php endif; ?>
                        <div class="item-inner">
                            <div class="carousel-caption">
                                <div class="item-title">
                                    <a href="<?php echo $this->_aVars['aNewsSlide']['link']; ?>" target="_blank"><?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aNewsSlide']['title']); ?></a>
                                </div>
                                <div class="item-info">
                                    <span><?php echo _p('by'); ?> <?php echo $this->_aVars['aNewsSlide']['creator']; ?></span>
                                    <span><?php echo $this->_aVars['aNewsSlide']['time_stamp']; ?></span>
                                </div>
                                <div class="item-desc">
<?php echo Phpfox::getLib('phpfox.parse.bbcode')->stripCode(strip_tags($this->_aVars['aNewsSlide']['description'])); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
<?php endforeach; endif; ?>

            </div>
            <div class="controllers">
                <!-- Controls -->
                <a class="left carousel-control" href="#carousel-fox-news" data-slide="prev">
                    <span class="ico ico-angle-left"></span>
                </a>
                <a class="right carousel-control" href="#carousel-fox-news" data-slide="next">
                    <span class="ico ico-angle-right"></span>
                </a>
            </div>
        </div>
<?php else: ?>
        <div class="block news-updates-container">
            <div class="title">
<?php echo _p('more_news_and_update'); ?>
            </div>
            <div class="content">
<?php if (count((array)$this->_aVars['aPhpfoxNews'])):  $this->_aPhpfoxVars['iteration']['news'] = 0;  foreach ((array) $this->_aVars['aPhpfoxNews'] as $this->_aVars['aNews']):  $this->_aPhpfoxVars['iteration']['news']++; ?>

                <div class="item-separated">
                    <a href="<?php echo $this->_aVars['aNews']['link']; ?>" target="_blank"><?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aNews']['title']); ?></a>
                    <div class="text-muted">
                        <span><?php echo _p('posted_by'); ?> <?php echo $this->_aVars['aNews']['creator']; ?></span>
                        <span><?php echo $this->_aVars['aNews']['time_stamp']; ?></span>
                    </div>
                </div>
<?php endforeach; endif; ?>
            </div>
            <div class="bottom">
                <ul>
                    <li id="js_block_bottom_1" class="first">
                        <a href="https://www.phpfox.com/blog/category/community-roundup/" target="_blank" id="js_block_bottom_link_1">
<?php echo _p('view_all'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
<?php endif;  endif; ?>
