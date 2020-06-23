<link href="{$appPath}/assets/css/owl.theme.default.min.css" rel='stylesheet' type='text/css'>
<link href="{$appPath}/assets/css/owl.carousel.css" rel='stylesheet' type='text/css'>

<div class="ynadvblog_feature_blog">
    <ul class="ynadvblog_main_item owl-carousel owl-theme" id="ynadvblog_feature_main_item">
        {foreach from=$aItems item=aItem}
            <li class="item">
                <div class="ynadvblog_avatar">
                    <span href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" class="item_image{if empty($aItem.text)} full{/if} ynadvblog_avatar_inner" style="background-image: url(<?php echo Phpfox::getService('ynblog.helper')->getImagePath($this->_aVars['aItem']['image_path'], $this->_aVars['aItem']['server_id'], '_big'); ?>)">
                    </span>
                </div>
                <div class="ynadvblog_info">
                    <div class="ynadvblog_author">
                        {$aItem|user:'':'':50:'':'author'}
                        <i class="yn_dots overflow-hi">-</i>
                        <?php $this->_aVars['aItem']['time_stamp_display'] = Phpfox::getTime('M j, Y',$this->_aVars['aItem']['time_stamp']); ?>
                        <span class="overflow-hi">{$aItem.time_stamp_display}<span>{plugin call='ynblog.template_block_entry_date_end'}</span></span>
                    </div>
                    <a href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link fw-bold ynadvblog_post_title" itemprop="url">{$aItem.title|clean}</a>
                    {if !empty($aItem.text)}
                        <div class="ynadvblog_desc item_content">{$aItem.text|striptag|stripbb|highlight:'search'|shorten:500:'...'}</div>
                    {/if}
                    <div class="ynadvblog_favorite">
                        <div>
                            {if $aItem.total_view == 1}
                                {$aItem.total_view}<span class="text-lowercase">{_p('view')}</span>
                            {else}
                                {$aItem.total_view}<span class="text-lowercase">{_p('views')}</span>
                            {/if}
                        </div>
                        <div>
                            {if $aItem.total_favorite == 1}
                                {$aItem.total_favorite}<span class="text-lowercase">{_p('favorite')}</span>
                            {else}
                                {$aItem.total_favorite}<span class="text-lowercase">{_p('favorites')}</span>
                            {/if}
                        </div>
                    </div>
                </div>
            </li>
        {/foreach}
    </ul>

    <ul class="ynadvblog_sub_item owl-carousel owl-theme" id="ynadvblog_feature_sub_item">
        {foreach from=$aItems item=aItem}
            <li class="item">
                <div class="ynadvblog_avatar">
                    <span class="item_image{if empty($aItem.text)} full{/if} ynadvblog_avatar_inner" style="background-image: url(<?php echo Phpfox::getService('ynblog.helper')->getImagePath($this->_aVars['aItem']['image_path'], $this->_aVars['aItem']['server_id'], '_list'); ?>)">
                    </span>
                </div>
            </li>
        {/foreach}
    </ul>
</div>




{literal}
    <script type="text/javascript">
        $Behavior.initFeaturedBlog = function(){
            var initSlider = function() {
                var sync1 = $("#ynadvblog_feature_main_item");
                var sync2 = $("#ynadvblog_feature_sub_item");
                var item_amount = parseInt(sync1.find('.item').length);
                var syncedSecondary = true;
                var true_false = 0;
                flag = false,
                duration = 300;

                if (item_amount > 1) {
                    true_false = true;
                } else{
                    true_false = false;
                }
                var rtl = '';
                if(jQuery("html").attr("dir") == "rtl") {
                    rtl = 'rtl';
                }

                sync2.owlCarousel_ynv1({
                    direction:rtl,
                    pagination: false,
                    navigation: false,
                    slideSpeed : 300,
                    loop: false,
                    smartSpeed: 500,
                    nav: false,
                    items: 4,
                    responsive: true,
                    slideBy: 4,
                    autoPlay: false,
                    responsiveRefreshRate : 100,
                    itemsDesktop      : [1199,4],
                    itemsDesktopSmall     : [979,4],
                    itemsTablet       : [768,4],
                    itemsTabletSmall       : [480,3],
                    itemsMobile       : [479,2],
                    afterInit : function(el){
                        el.find(".owl-item").eq(0).addClass("synced");
                        sync1.owlCarousel_ynv1({
                            direction:rtl,
                            singleItem : true,
                            smartSpeed: 750,
                            navigation: true_false,
                            autoPlay: true,
                            pagination: false,
                            responsive: true,
                            responsiveRefreshRate : 200,
                            autoplayTimeout: 1000,
                            autoplayHoverPause: true,
                            slideSpeed : 300,
                            navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                            afterAction : syncPosition
                        });
                    }
                });

                function syncPosition(el){
                    this
                    .$owlItems
                    .removeClass('active')

                    //add class active
                    this
                    .$owlItems //owl internal $ object containing items
                    .eq(this.currentItem)
                    .addClass('active')

                    var current = this.currentItem;
                    $("#ynadvblog_feature_sub_item")
                    .find(".owl-item")
                    .removeClass("synced")
                    .eq(current)
                    .addClass("synced")
                    if($("#ynadvblog_feature_sub_item").data("owlCarousel_ynv1") !== undefined){
                        center(current)
                    }
                }

                $('.owl-buttons').addClass('dont-unbind');
                $('.owl-buttons .owl-prev').addClass('dont-unbind');

                $("#ynadvblog_feature_sub_item").on("click", ".owl-item", function(e){
                    e.preventDefault();
                    var number = $(this).data("owlItem");
                    sync1.trigger("owl.goTo",number);
                });

                function center(number){
                    var sync2 = $("#ynadvblog_feature_sub_item");
                    var sync2visible = sync2.data("owlCarousel_ynv1").owl.visibleItems;
                    var num = number;
                    var found = false;
                    for(var i in sync2visible){
                        if(num === sync2visible[i]){
                            var found = true;
                        }
                    }

                    if(found===false){
                        if(num>sync2visible[sync2visible.length-1]){
                            sync2.trigger("owl.goTo", num - sync2visible.length+2)
                            } else{
                                if(num - 1 === -1){
                                    num = 0;
                                }
                            sync2.trigger("owl.goTo", num);
                            }   
                        } else if(num === sync2visible[sync2visible.length-1]){
                            sync2.trigger("owl.goTo", sync2visible[1])
                        } else if(num === sync2visible[0]){
                            sync2.trigger("owl.goTo", num-1)
                        }
                    }
                };

           if (typeof($.fn.owlCarousel_ynv1) == 'undefined') {
                var script = document.createElement('script');
                script.src = '{/literal}{$appPath}{literal}/assets/jscript/Owl-slider/owl.carousel.js';
                script.onload = initSlider;
                document.getElementsByTagName("head")[0].appendChild(script);
            } else {
                initSlider();
            }
        }
    </script>
{/literal}