;
function log(obj){if(!console)return false; console.log(obj);};
(function ($) {
    $.fn.extend({
        JHSlide : function (/* PARAMS */slideTime, transition) {
            var setupDivs = function(parent){
                var wdiv = $("<div>").addClass("jhslider-window");
                var slider = $("<div>").addClass("jhslider-slider");
                var thumbnail = $("<div>").addClass("jhslider-thumbnail");
                var tContent = $("<div>").addClass("jhslider-content");
                
                slider["iCount"] = 0;
                
                parent.empty();
                wdiv.append(slider);
                
                parent.append(wdiv);
                parent.append(thumbnail);
                parent.append(tContent);
                
                return {
                    "window": wdiv,
                    "slider": slider,
                    "thumbnail": thumbnail,
                    "content": tContent
                };
            };
            
            var setupSliderItemImage = function(/*&*/slider, childGroup) {
                var ul = $("<ul>").addClass("jhslider-slider-ul");
                var width = 0;
                var bimg = childGroup.find(".big-image");
                var cimg = bimg.size();
                bimg.each(function(/*index*/){
                    var $this = $(this);
                    var li = $("<li>").addClass("jhslider-slider-li");
                    li.append($this);
                    ul.append(li);
					
					$this.css({
						"cursor": "pointer"
					}).click(function(evt){
						evt.preventDefault();
						
						window.location = $this.attr("ref");
						
						return false;
					});
                });
                if(cimg > 0){
                    ul.append(ul.children("li").first().clone());
                }
                slider.append(ul);
                
                return cimg;
            };
            
            var setupSliderItemContent = function(/*&*/divs, childGroup, tWH) {
                thumbnailDiv = divs["thumbnail"];
                contentDiv = divs["content"];
                slider = divs["slider"];
                var bItem = childGroup.find(".jhslider-info-quick");
                var cItem = bItem.size();
                var dItem = childGroup.find(".jhslider-info-detail");
                dItem.each(function(index){
                    var $this = $(this);
                    var div = $("<div>").addClass("jhslider-content-div lsco_cont_" + index);
                    div.attr({
                        "ref": index
                    });
                    div.append(($this.html()));
                    if(index != 0) {
                        div.hide();
                    }
                    contentDiv.append(div);
                });
                bItem.each(function(index){
                    var $this = $(this);
                    var div = $("<div>").addClass("jhslider-thumbnail-div lsco_thumb_" + index);
                    div.attr({
                        "ref": index
                    }).css({
                        "cursor": "pointer"
                    });
                    if(index == cItem - 1) {
                        div.addClass("last")
                    }else if(index == 0) {
                        div.addClass("active")
                    }
                    div.append($("<div class=\"jhslider-buff\">").html($this.html()));
                    thumbnailDiv.append(div);
                    
                    div.click(function(evt){
                        evt.preventDefault();
                        if(eval(div.attr("ref")) == eval(slider["iCount"])){return false;}
                        thumbnailDiv.find(".active").removeClass("active");
                        $(this).addClass("active");
                        
                        divs["slider"]["iCount"] = eval($(this).attr("ref"));
                        divs["slider"].stop(false, false).animate({
                            "left": ("-" + (divs["slider"]["iCount"] * tWH["width"]) + "px")
                        }, transition);
                        
                        contentDiv.find(".jhslider-content-div").hide().css("opacity", "1");
                        contentDiv.find(".lsco_cont_" + (slider["iCount"])).stop(false, false).fadeIn(transition);
                        
                        return false;
                    });
                });
                if(cItem > 0){
                    contentDiv.append(contentDiv.children("div").first().clone());
                }
                
                return cItem;
            };
            
            var setupAllSize = function(divsColl, itemCount) {
                window.a = divsColl;
                window.b = itemCount;
                var itemWidth = eval(divsColl["window"].css("width").replace("px", ""));
                var itemHeight = eval(divsColl["window"].css("height").replace("px", ""));
				
                divsColl["slider"].width((1 + itemCount) * 360/* itemWidth */);
				if(itemHeight > 0) {
					divsColl["slider"].height(itemHeight);
				}
                
                return {
                    "width": 360/* itemWidth */, 
                    "height": 220/* itemHeight */
                };
            };
            
            var setupAnimate = function(timeToSlide, tWH, iC, divs) {
                slider = divs["slider"];
                thumbnail = divs["thumbnail"];
                tcontent = divs["content"];
                slider.timerID = null;
				if(!slider["iCount"])slider["iCount"] = 0;
                var callback = function(){
                    slider.stop(false, false).animate({
                        "left": ("-=" + tWH["width"] + "px")
                    }, transition, function(){
                        if(slider["iCount"] >= iC) {
                            slider.css({
                                "left": "0px"
                            });
                            slider["iCount"] = 0;
                        }
                            
                        thumbnail.find(".active").removeClass("active");
                        thumbnail.find(".lsco_thumb_" + (slider["iCount"])).addClass("active");
                        
                        tcontent.find(".jhslider-content-div").stop(false, false).fadeOut(transition);
                        tcontent.find(".lsco_cont_" + (slider["iCount"])).stop(false, false).fadeIn(transition);
                        
                        slider.timerID = setTimeout(callback, timeToSlide);
                    });
                    slider["iCount"] += 1;
                };
                slider.timerID = setTimeout(callback, timeToSlide);
                return slider.timerID;
            }
            
            var uninstallAnimate = function(timer) {
                clearTimeout(timer);
                return null;
            }
            
            return this.each(function () {
                var $this = $(this);
                var $chilInfo = $this.children();
                var divs = setupDivs($this);
                var itemCount = setupSliderItemImage(divs["slider"], $chilInfo);
                
                var itemWH = setupAllSize(divs, itemCount);
                setupSliderItemContent(divs, $chilInfo, itemWH);
                
                setupAnimate(slideTime, itemWH, itemCount, divs);
                
                $this.mouseover(function(evt){
                    uninstallAnimate((divs["slider"]).timerID);
                    (divs["slider"]).timerID = null;
                });
                
                $this.mouseout(function(evt){
                    if((divs["slider"]).timerID == null) {
                        setupAnimate(slideTime, itemWH, itemCount, divs);
                    }
                });
            });
        }
    });
})(jQuery);