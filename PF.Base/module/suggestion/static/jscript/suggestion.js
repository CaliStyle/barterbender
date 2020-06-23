

function doProcess(target, iApprove, iFriendId, iItemId, iProcessId, sModule, sUrl){
    console.log($(target).parent().parent().find('span[class*="ajaxLoader"]'));
    $(target).parent().find('input[class="button"]').hide();
    $(target).parent().parent().find('span[class*="ajaxLoader"]').show();        
    $.ajaxCall('suggestion.approve','iApprove='+iApprove+'&iFriendId='+iFriendId+'&iItemId='+iItemId+'&sModule='+sModule+'&iProcessId='+iProcessId+'&sUrl='+sUrl);
}    

function doProcessDelete(target,iSuggestId){
    
    $(target).parent().find('input[class="button"]').hide();
    $(target).parent().parent().find('span[class*="ajaxLoader"]').show();        
    $.ajaxCall('suggestion.delete','&iSuggestId='+iSuggestId);
}
function suggestion_viewmorephoto()
{
    $('.image_deferred:not(.built)').each(function() {
        var t = $(this),
            src = t.data('src'),
            i = new Image();

        t.addClass('built');
        if (!src) {
            t.addClass('no_image');
            return;
        }

        t.addClass('has_image');
        i.onerror = function(e, u) {
            t.replaceWith('');
        };
        i.onload = function(e) {
            t.attr('src', src);
        };
        i.src = src;
    });
} 