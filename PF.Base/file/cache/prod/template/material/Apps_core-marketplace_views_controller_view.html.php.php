<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php



?>

<?php if (( Phpfox ::getParam('marketplace.days_to_expire_listing') > 0 ) && ( $this->_aVars['aListing']['time_stamp'] < ( PHPFOX_TIME - ( Phpfox ::getParam('marketplace.days_to_expire_listing') * 86400 ) ) )): ?>
    <div class="error_message">
<?php echo _p('listing_expired_and_not_available_main_section'); ?>
    </div>
<?php endif;  if ($this->_aVars['aListing']['view_id'] == '1'): ?>
    <?php
						Phpfox::getLib('template')->getBuiltFile('core.block.pending-item-action');
						 endif; ?>
<div class="item_view market-app market-view-detail">
    <div class="market-view-detail-main-content">
        <div class="market-detail-not-right">
<?php if ($this->_aVars['aImages']): ?>
                <div class="market-detail-photo-block">
                    <div class="ms-marketplace-detail-showcase dont-unbind market-app">
                        <div class="ms-vertical-template ms-tabs-vertical-template dont-unbind" id="marketplace_slider-detail">
<?php if (count((array)$this->_aVars['aImages'])):  $this->_aPhpfoxVars['iteration']['images'] = 0;  foreach ((array) $this->_aVars['aImages'] as $this->_aVars['aImage']):  $this->_aPhpfoxVars['iteration']['images']++; ?>

                            <div class="ms-slide ms-skin-default dont-unbind">
                                <img src="<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'misc/blank.gif','return_url' => true)); ?>" data-src="<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aImage']['server_id'],'path' => 'marketplace.url_image','file' => $this->_aVars['aImage']['image_path'],'return_url' => true)); ?>"/>
                                <div class="ms-thumb">
                                    <img class="dont-unbind" src="<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aImage']['server_id'],'path' => 'marketplace.url_image','file' => $this->_aVars['aImage']['image_path'],'suffix' => '_120_square','return_url' => true)); ?>" alt="thumb" />
                                </div>
                            </div>
<?php endforeach; endif; ?>
                        </div>
                    </div>
                    <div class="item-slider-count">
                        <div class="item-count js_market_toggle_thumb dont-unbind">
                            <i class="ico ico-th-large"></i>
                            <div class="item-number">
                                <span class="item-current js_market_current_slide">1</span>/<span class="item-total"><?php echo count($this->_aVars['aImages']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
<?php endif; ?>

<?php Phpfox::getBlock('marketplace.info', array()); ?>
        </div>
<?php if (! empty ( $this->_aVars['aListing']['description'] )): ?>
            <div class="item-info-long-desc item_view_content">
                <div class="item-label "><?php echo _p('product_information'); ?></div>
                <div class="item-text" itemprop="description">
<?php echo Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->parse($this->_aVars['aListing']['description']), 200, 'feed.view_more', true), 70); ?>
<?php if ($this->_aVars['aListing']['total_attachment']): ?>
<?php Phpfox::getBlock('attachment.list', array('sType' => 'marketplace','iItemId' => $this->_aVars['aListing']['listing_id'])); ?>
<?php endif; ?>
                </div>
            </div>
<?php endif; ?>
<?php (($sPlugin = Phpfox_Plugin::get('marketplace.template_default_controller_view_extra_info')) ? eval($sPlugin) : false); ?>

        <div <?php if ($this->_aVars['aListing']['view_id'] != 0): ?>style="display:none;" class="js_moderation_on market-addthis"<?php endif; ?> class="market-addthis">
            
<?php Phpfox::getBlock('share.addthis', array('url' => $this->_aVars['aListing']['bookmark_url'],'title' => $this->_aVars['aListing']['title'],'description' => $this->_aVars['sShareDescription'])); ?>
            <div class="item-detail-feedcomment">
<?php Phpfox::getBlock('feed.comment', array()); ?>
            </div>
        </div>
    </div>
<?php if (( count ( $this->_aVars['aListings'] ) )): ?>
        <div class="item-block-detail-listing">
            <div class="item-header">
<?php echo _p("in_this_category"); ?>
            </div>
            <div class="item-listing">
                <div class="item-container market-app listing">
<?php if (count((array)$this->_aVars['aListings'])):  $this->_aPhpfoxVars['iteration']['listings'] = 0;  foreach ((array) $this->_aVars['aListings'] as $this->_aVars['aListing']):  $this->_aPhpfoxVars['iteration']['listings']++; ?>

                        <?php
						Phpfox::getLib('template')->getBuiltFile('marketplace.block.rows');
						?>
<?php endforeach; endif; ?>
                </div>
            </div>
        </div>
<?php endif; ?>
</div>

<?php echo '
<script type="text/javascript">
    $Behavior.initDetailSlide = function() {
        var ele = $(\'#marketplace_slider-detail\');
        if (ele.prop(\'built\') || !ele.length) return false;
        ele.prop(\'built\', true).addClass(\'dont-unbind-children\');
            var slider = new MasterSlider();

        var mp_direction = \'h\';
        var toggle_thumb = $(".js_market_toggle_thumb");

        slider.setup(\'marketplace_slider-detail\' , {
            width: ele.width(),
            height: ele.width(),
            space:5,
            view:\'basic\',
            dir:mp_direction,
            speed:50,
        });

        slider.control(\'arrows\');
        slider.control(\'scrollbar\' , {dir:\'v\'});
        slider.control(\'thumblist\' , {autohide:false ,dir:mp_direction});

        if(toggle_thumb.length){
            toggle_thumb.on("click",function(){
                $(this).closest(".market-detail-photo-block").toggleClass("hide-thumb");
            });
        }
        if (window.matchMedia(\'(max-width: 767px)\').matches) {
            $(".market-detail-photo-block").addClass("hide-thumb");
        }

        slider.api.addEventListener(MSSliderEvent.CHANGE_END , function(){
            if ($(\'.ms-thumbs-cont .ms-thumb-frame\').length < 2){
                $(\'.ms-thumbs-cont\').closest(\'.market-detail-photo-block\').addClass(\'one-slide\');
            }
            var width_list_thumb = $(\'.ms-thumb-list\').outerWidth(),
                width_item_thumb = $(\'.ms-thumb-frame\').outerWidth() + 5,
                max_item_thumb = parseInt(width_list_thumb/width_item_thumb),
                count_item_thumb = $(\'.ms-thumbs-cont .ms-thumb-frame\').length ;
            var current_slide = slider.api.index() + 1;
            $(".js_market_current_slide").text(current_slide);
            if(count_item_thumb <= max_item_thumb){
                $(\'.ms-thumb-list\').addClass(\'not-nav-btn\');
            }
        });

      $Behavior.initDetailSlide = function() {}
    };
</script>
'; ?>

