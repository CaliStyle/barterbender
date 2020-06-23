{if count($aFeaturedBusinesses)}
	<div id="yndirectory-featured" class="yndirectory-featured dont-unbind-children owl-carousel owl-theme homepage-feature">
    	{foreach from=$aFeaturedBusinesses item=aBusiness name=business}
			<div class="item">
				<div class="yndirectory-featured__item">
					<div class="yndirectory-featured__photo" style="background-image: url(
						{if $aBusiness.default_cover}
							{$aBusiness.cover_photo}
						{else}
							{img return_url=true server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.cover_photo suffix=''}
						{/if}
					);"></div>
					<div class="yndirectory-featured__info mt-2 ml--1 mr--1">
						<div class="yndirectory-featured__inner pl-1 pr-1">
							<div class="yndirectory-featured__media mr-1">
								<span class="yndirectory-featured__thumb" style="background-image:url(
								{if $aBusiness.logo_path}
                                    {img return_url=true server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_400'}
                                {else}
                                    {$aBusiness.default_logo_path}
                                {/if}
								);"></span>
							</div>
							<div class="yndirectory-featured__body">
								<h2 class="fw-bold mt-0	mb-0 yndirectory-featured__title"><a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" id="js_business_edit_inner_title{$aBusiness.business_id}">{$aBusiness.name|clean}</a></h2>
								<div class="yndirectory-featured__description text-gray-dark mt-h1"><span class="yndirectory-featured__category text-gray-darker">{if Phpfox::isPhrase($this->_aVars['aBusiness']['category_title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aBusiness']['category_title']) ?>
                                        {else}
                                            {assign var='value_name' value=$aBusiness.category_title|convert}
                                        {/if}
                                         <a href="{permalink module='directory.category' id=$aBusiness.category_id title=$value_name}">{$value_name}</a></span> - {$aBusiness.short_description_parsed|clean}
                                </div>
							</div>
						</div>
						<div class="yndirectory-featured__review pl-1 pr-1">
                            {if (int)$aBusiness.total_review < 1 }
								<div class="yndirectory-featured__review-inner none-review text-right">
									<a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews" class="fw-bold text-primary-light mb-0 whs-nw d-block">{_p var='no_review'}</a>
									<a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews" class="fw-bold text-primary-light mb-0 whs-nw d-block text">{_p var='be_the_first'}</a>
									<p class="mb-0 text-gray mt-1 start-wapper whs-nw"><i class="ico ico-star-o"></i><i class="ico ico-star-o"></i><i class="ico ico-star-o"></i><i class="ico ico-star-o"></i><i class="ico ico-star-o"></i></p>
								</div>
                            {else}
								<div class="yndirectory-featured__review-inner">
									{if $aBusiness.total_rating != 0}
										<a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews" class="ync-outer-rating ync-outer-rating-column full text-right">
								            <div class="ync-outer-rating-column">
								                <div class="ync-rating-count-star">{$aBusiness.total_score}</div>
								                <div class="ync-rating-star">
				                                    {for $i = 0; $i < 5; $i++}
				                                        {if $i < (int)$aBusiness.total_score}
				                                            <i class="ico ico-star" aria-hidden="true"></i>
				                                        {elseif ((round($aBusiness.total_score) - $aBusiness.total_score) > 0) && ($aBusiness.total_score - $i) > 0}
				                                            <i class="ico ico-star half-star" aria-hidden="true"></i>
				                                        {else}
				                                            <i class="ico ico-star disable" aria-hidden="true"></i>
				                                        {/if}
				                                    {/for}
				                                </div>
								            </div>
								            <span class="ync-rating-count-review">
			                                    <span class="item-number">{$aBusiness.total_review}</span>
			                                    {if $aBusiness.total_review > 1 }
			                                        <span class="item-text">{_p var = 'directory.reviews'}</span>
			                                    {else}
			                                        <span class="item-text">{_p var = 'directory.review'}</span>
			                                    {/if}
			                                </span>
								        </a>
							        {/if}
								</div>
                            {/if}
						</div>
					</div>
				</div>
			</div>
    	{/foreach}
	</div>
{/if}