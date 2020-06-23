<div>
	<div>{phrase var='create_a_business_step_1_select_package'}</div>
	<div>
		<div class="content row clearfix">	            
            <div class="">
                <div class="">
                    <ul>
                    	slide show
                    	{*
	                    {foreach from=$aThemes key=Id item=aTheme}
	                        <li class="">
	        	                <a href="#" class="item-image">
	                                <img style="width: 50px; height: 50px;" src="http://4.bp.blogspot.com/-RMls1jfohYQ/TlnPyj-7KzI/AAAAAAAACvk/VuBlt_Rd850/s1600/Cool+wallpaper1.jpg" />
	        	                </a>
	        	                <input type="checkbox" name="val[theme][]" value="{$aTheme.theme_id}"
	        	                	{if isset($aForms.themes) && count($aForms.themes) > 0}
		        	                	{foreach from=$aForms.themes key=Id item=theme}
		        	                		{if $theme.theme_id == $aTheme.theme_id}
		        	                			checked="checked"
	        	                			{/if}
		        	                	{/foreach}
	        	                	{/if}
	        	                />
	                        </li>
	            		{/foreach}
            			*}
                    </ul>
                </div>  
            </div>  	               
		</div>
	</div>
	<div>{phrase var='all_packages'}</div>
	<div>
		{foreach from=$aPackages key=Id item=aPackage}
			<div>
				<div>{$aPackage.name}</div>
				<div>
					<div>{phrase var='price'}: {$aPackage.fee_display}</div>
					<div>{phrase var='billing_cycle'}: </div>
					<div>{phrase var='duration'}: </div>
				</div>
				<div>
					<div>{phrase var='features_available'}: </div>
					<div></div>					
				</div>
				<div>
					<div>{phrase var='modules_available'}: </div>
					<div></div>					
				</div>
			</div>
		{/foreach}
	</div>
	<div><a href="{$sBackUrl}">{phrase var='back'}</a></div>
</div>