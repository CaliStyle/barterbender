function slider(sliderContainer, sliderInner, slideAmount, itemsVisible, currentSet, budge, looping, automate, interval) {
    // set up
	var links_s = sliderInner.find("a.more");
    var sliderContainer = sliderContainer || false; // the container to all this
    var sliderInner = sliderInner || false; // what you actually want to slide
    var slideAmount = slideAmount || false; // how much to slide the slider by
    var itemsVisible = itemsVisible || false; // how many items you can see at one go
    var currentSet = currentSet || 1; // start at the begining or half way through (not full tested 28/06/10)
    var budge = budge || 0; // budge one way or another, useful for lining up with paddings/margins/spacings
    var looping = looping || false; // at the end of the sequence, stop? or go back ground
    var automate = automate || false; // auto page? if true, will set looping to true
    var interval = interval || 5; // seconds

    if (sliderContainer && sliderInner && slideAmount && itemsVisible) {

        // house keeping
        var currentSliderPos = 0;
        var paginator = sliderContainer.parent().find("ul[class*=paginator]");
        var nextBtn = paginator.find("li.next a");
        var prevBtn = paginator.find("li.prev a");
        var maxSet = Math.ceil(sliderInner.children().length/itemsVisible);
        var reset = false; // controlled by the function for resetting the loop
        var timer = "";
        var hovering = false;
        // bubble
        var sliderMoving = false;

        // set up
        if (automate) {
            looping = true;
        }
        interval = interval * 1000;
        if (looping) {
            sliderInner.find("li:first").clone().appendTo(sliderInner);
            sliderInner.find("li:last").prev().clone().prependTo(sliderInner);
            budge = budge - slideAmount;
        }
        currentSliderPos = budge + ((currentSet - 1) * slideAmount * -1);
        sliderInner.css({ 
            left: currentSliderPos 
        });

        // the slider functions
        function moveSlider(direction) {
            if (!sliderMoving) {
                calculateNextPosition(direction);
                sliderMoving = true;
                sliderInner.animate({
                    left: currentSliderPos
                }, 500, function(){
                    if (reset) {
                        currentSliderPos = budge + ((currentSet-1) * slideAmount * -1);
                        sliderInner.css({ 
                            left: currentSliderPos 
                        });
                    }
                    sliderMoving = false;
                    if (!hovering) {
                        auto();
                    }
                });
                updatePaginator();
            }
        }
        // calculate the next position of the slider
        function calculateNextPosition(direction) {
            if(direction == 'right') {
                if (looping && currentSet == maxSet) {
                    currentSliderPos -= slideAmount;
                    reset = true;
                    currentSet = 1;
                } else {
                    currentSliderPos -= slideAmount;
                    currentSet++;
                }
            } else if (direction == 'left') {
                if (looping && currentSet == 1) {
                    currentSliderPos += slideAmount;
                    reset = true;
                    currentSet = maxSet;
                } else {
                    currentSliderPos += slideAmount;
                    currentSet--;
                }
            } else {
                currentSliderPos = budge + ((direction-1) * slideAmount * -1);
                currentSet = direction;
            }
        }

        // update paginator links
        function updatePaginator() {
            if (!looping) {
                if(currentSet == 1) {
                    prevBtn.addClass("disabled");
                    nextBtn.removeClass("disabled");
                } else if (currentSet == maxSet) {
                    nextBtn.addClass("disabled");
                    prevBtn.removeClass("disabled");
                } else {
                    nextBtn.removeClass("disabled");
                    prevBtn.removeClass("disabled");
                }
            }
            paginator.find("li.current").removeClass("current");
            paginator.find("li").eq(currentSet-1).addClass("current");
			$("#stypidlnk").attr("href", $(links_s.get(currentSet-1)).attr("href"));
        }

        function auto() {
            if (automate) {
                // if we're automating..
                clearTimeout(timer); // clear the timeout var, so it doesn't bubble itself into a mess
                timer = setTimeout(openNext, interval); // set the timer again
            }
        }

        function openNext() {
            moveSlider('right');
        }

        function setUpPaginator() {
            if (currentSet == 1 && maxSet == 1) {
                nextBtn.hide();
                prevBtn.hide();
                paginator.hide();
            } else {
                var pages = "";
                for (i=0;i<maxSet;i++) {
                    pages = pages + '<li><a href="#">'+(i+1)+'</a></li>';
                }
                paginator.prepend(pages);
                updatePaginator();
                timer = setTimeout(auto, 50); // set the automatic swap happening

                // add some events to the slider buttons
                paginator.find("li a").click(function(e){
                    e.preventDefault();
                    var index = paginator.find("li a").index(this);
                    if (index < maxSet && !$(this).parent().hasClass("current")) {
                        moveSlider(index+1);
                    }
                });
                prevBtn.click(function(e){
                    e.preventDefault();
                    if (looping) {
                        moveSlider('left');
                    } else {
                        if(!$(this).hasClass("disabled")) {
                            moveSlider('left');
                        }
                    }
                });
                nextBtn.click(function(e){
                    e.preventDefault();
                    if (looping) {
                        moveSlider('right');
                    } else {
                        if(!$(this).hasClass("disabled")) {
                            moveSlider('right');
                        }
                    }
                });
                sliderContainer.hover(function() {
                    // if you hover over an Li, say your reading the content, it stops the automatic transition
                    clearTimeout(timer);
                    hovering = true;
                }, function() {
                    // and restarts it when you roll out
                    auto();
                    hovering = false;
                });
            }
        }

        setUpPaginator();
    }
}
