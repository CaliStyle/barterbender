<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>


{if count($aFeaturedJobs)>0}
	<div id="ynjp_featured-items" class="owl-carousel owl-theme">
		{foreach from=$aFeaturedJobs item=item}
		<div class="item ynjp_featured-item">
			{template file='jobposting.block.job.entry_slider'}
     	</div> 
	    {/foreach}  
 	</div>
{/if}
{literal}
<script type="text/javascript">
(function(){
    var _stage = '#ynjp_featured-items',
        _options = {
            navigation : true,
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true,
            autoPlay: true
        },
        _required = function(){
            return !/function/i.test(typeof jQuery.owlCarousel);
        },
        _initFeaturedSlideshow_flag = false,
        initFeaturedSlideshow = function (){
            var stage = $(_stage);
            if(!stage.length) return;
            if(_initFeaturedSlideshow_flag) return;
            if(!_required()) return;
            _initFeaturedSlideshow_flag = true;
            $(_stage).owlCarousel(_options);
            $(".owl-prev").addClass("dont-unbind");
            $(".owl-next").addClass("dont-unbind");
            $(".owl-buttons").addClass("dont-unbind");
        }

    $Behavior.ynjpSlideJob = function() {
        if(!$(_stage).length) return;
        function checkCondition(){
            var stage = $(_stage);
            if(!stage.length) return;
            if(_initFeaturedSlideshow_flag) return;
            if(!_required()){
                window.setTimeout(checkCondition, 400);
            }else{
                initFeaturedSlideshow();
            }
        }
        window.setTimeout(checkCondition, 400);
    }
})();
</script>
{/literal}