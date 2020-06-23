<div id="js_pages_like_join_holder">
    <div class="global_like_number">
        {$iTotalLike}
    </div>
    <div class="global_like_link">	
        <a style="cursor:pointer" onclick="return $Core.box('auction.browselike', 400, 'type_id=auction&amp;item_id={$aAuction.product_id}&amp;force_like=1');" >{phrase var='people_like_this'}</a>
    </div>
</div>