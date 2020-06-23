<div class="menu-edit-aucion-link">
	{module name='auction.menu-edit-auction-link'}
</div>
<h3 class="">
    <ul class="">
        <li>{phrase var='theme'}</li>
    </ul>
 </h3>
	<form method="post" action="{url link='auction.theme.id_'.$iProductId}" id="js_manage_auction_theme" onsubmit="" enctype="multipart/form-data">

			<input type="hidden" name="val[auction_id]" value="{$iAuctionId}" >
			<input type="hidden" name="val[product_id]" value="{$iProductId}" >

			<div id="ynauction_theme">

						<div id="ynauction_theme">
								<div class="ynauction-theme-item">
									<div>
										<a href="{$core_path}module/auction/static/image/theme_1.png" target="_blank">
											<img src="{$core_path}module/auction/static/image/theme_1.png" />
										</a>
									</div>
									<div>
			        	                <input type="radio" name="val[theme]" value="1"  {if isset($aForms.theme_id) } {if ($aForms.theme_id == 1)}checked{/if} {else}checked{/if}/>
									</div>
								</div>

								<div class="ynauction-theme-item">
									<div>
										<a href="{$core_path}module/auction/static/image/theme_2.png" target="_blank">
											<img src="{$core_path}module/auction/static/image/theme_2.png" />
										</a>
									</div>
									<div>
			        	                <input type="radio" name="val[theme]" value="2" {if isset($aForms.theme_id) && ($aForms.theme_id == 2)}checked{/if}/>
									</div>
								</div>

						</div>
					<div class='ynauction-separator'></div>

			</div>

			<div class="ynauction-button">
				<input type="submit" name="val[apply_theme]" id="apply_theme" value="{phrase var='apply_theme'}">
			</div>
	</form>
</div>