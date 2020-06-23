function addToFavourite(iVideoId, sType) {
    $.ajaxCall('videochannel.addToFavourite', 'video_id=' + iVideoId + '&type=' + sType);
}