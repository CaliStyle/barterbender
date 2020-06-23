<!-- category middle -->
<div class="p-advmarketplace-category-container p-listing-container" >
    {section name=foo loop=20} 
    <div class="p-item p-advmarketplace-category-item">
        <div class="item-outer">
            <div class="item-outer-custom">
                <a href="" class="p-advmarketplace-category-item-bg-link p-hidden-side-block"></a>
                <div class="p-item-media-wrapper p-advmarketplace-category-item-media p-hidden-side-block">
                    <div class="item-media-link">
                       <span class="item-media-src" style="background-image: url(https://product-dev.younetco.com/sonhh/fox472/PF.Base/file/pic/advancedmarketplace/2019/01/3587b3b4b2a8085aadd8e966e7f66318.jpg)"></span>
                    </div>
                </div>
                <div class="item-inner">
                    <div class="item-name p-item-title">
                        <a href="{permalink module='advancedmarketplace.search.category' id=$aCategory.category_id title=$aCategory.name}">
                            {_p var='test'}
                        </a>
                    </div>
                    <div class="item-number-post p-item-minor-info">
                        50 <span class="p-text-lowercase">{_p var='test'}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/section}
</div>

<!-- category side block -->
<div class="p-advmarketplace-category-container p-listing-container" data-mode-view="list">
    {section name=foo loop=20} 
    <div class="p-item p-advmarketplace-category-item">
        <div class="item-outer">
            <div class="item-outer-custom">
                <a href="" class="p-advmarketplace-category-item-bg-link p-hidden-side-block"></a>
                <div class="p-item-media-wrapper p-advmarketplace-category-item-media p-hidden-side-block">
                    <div class="item-media-link">
                       <span class="item-media-src" style="background-image: url(https://product-dev.younetco.com/sonhh/fox472/PF.Base/file/pic/advancedmarketplace/2019/01/3587b3b4b2a8085aadd8e966e7f66318.jpg)"></span>
                    </div>
                </div>
                <div class="item-inner">
                    <div class="item-name p-item-title">
                        <a href="{permalink module='advancedmarketplace.search.category' id=$aCategory.category_id title=$aCategory.name}">
                            {_p var='test'}
                        </a>
                    </div>
                    <div class="item-number-post p-item-minor-info">
                        50 <span class="p-text-lowercase">{_p var='test'}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/section}
</div>