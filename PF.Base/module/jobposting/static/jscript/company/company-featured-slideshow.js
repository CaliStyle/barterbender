
(function(){
    var _stage = '#ync_featured-items',
        _options = {
            navigation : true, // Show next and prev buttons
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true,
            //autoPlay: true
        },
        _required = function(){

            return !/function/i.test(typeof jQuery.owlCarousel);
        },
        _initFeaturedSlideshow_flag = false,
        initFeaturedSlideshow = function (){
            console.log("_initFeaturedSlideshow_flag " + _initFeaturedSlideshow_flag);
            var stage = $(_stage);
            //if(!stage.length) return;
            if(_initFeaturedSlideshow_flag) return;
            if(!_required()) return;
            _initFeaturedSlideshow_flag = true;
            $(_stage).owlCarousel(_options);

            $(".owl-prev").addClass("dont-unbind");
            $(".owl-next").addClass("dont-unbind");
            $(".owl-buttons").addClass("dont-unbind");

        }

        $Behavior.ynjpSlideCompany = function() {
        if(!$(_stage).length) return;

        function checkCondition(){
            console.log("checkCondition ");
            var stage = $(_stage);
            if(!stage.length) return;
            if(_initFeaturedSlideshow_flag) return;
            console.log("_initFeaturedSlideshow_flag=false ");
            if(!_required()){
                window.setTimeout(checkCondition, 400);
            }else{
                initFeaturedSlideshow();
            }
        }

        window.setTimeout(checkCondition, 500);

    }
})();
/*
var mLoad = setInterval(function() {
    if (typeof(jQuery().owlCarousel) == 'function') {
        $("#ync_featured-items").owlCarousel({
            navigation : true, // Show next and prev buttons
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true,
            autoPlay: true,
        });
        clearInterval(mLoad);
    }
}, 500);

/*
$Behavior.ynjpSlideCompany  = function() {



        try {
            $("#ync_featured-items").owlCarousel({
                navigation : true, // Show next and prev buttons
                slideSpeed : 300,
                paginationSpeed : 400,
                singleItem:true,
                autoPlay: true,
            });
            console.log("init Company slider");
        }
        catch(err) {
            console.log(err.message);
            var mLoad = setInterval(function() {
                if (typeof(jQuery().owlCarousel) == 'function') {
                    $("#ync_featured-items").owlCarousel({
                        navigation : true, // Show next and prev buttons
                        slideSpeed : 300,
                        paginationSpeed : 400,
                        singleItem:true,
                        autoPlay: true,
                    });
                    clearInterval(mLoad);
                }
            }, 500);

        }



};
    */