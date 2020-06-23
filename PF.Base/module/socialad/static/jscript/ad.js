(function(window, undefined) {
    if (typeof $Behavior == "undefined") {
        console.log(" there's something wrong with Phpfox behavior");
    }

    if (!window.jQuery) {
        var script = document.createElement('script');
        script.type = "text/javascript";
        script.src = "//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js";
        document.getElementByTagName('head')[0].appendChild(script);
    }
})(window, undefined);
