{literal}
<style>
	.feed_sort_order{
		display: none !important;
	}
	#js_main_feed_holder{

	}
	#js_feed_content{
		display: none;
	}
	#js_block_border_feed_display .title{
		display: none;
	}
</style>
{/literal}

<h1 class="ynjp_jobDetail_title"><a href="{permalink module='jobposting.company' id=$aCompany.company_id title=$aCompany.name}">{$aCompany.name}</a></h1>

<div class="item_view ">
	<div class="item_info">
		{phrase var='created_by'} <a href="{url link=''}{$aCompany.user_name}">{$aCompany.full_name}</a>
	</div>
	{if $aCompany.action}
	<div class="item_bar"  >
		<div class="dropdown">
            <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="ico ico-gear-o"></i>
            </a>
			<ul class="dropdown-menu dropdown-menu-right">
				{template file='jobposting.block.company.action-link'}
			</ul>
		</div>
	</div>
	{/if}
	
	<div class="addthis_block mt-1">
	    <!-- AddThis Button BEGIN -->
	    {addthis url=$aCompany.bookmark_url title=$aCompany.name}
    </div>

	<div class="yns job_detail_information">
		<h4><span> {phrase var='company_description'} </span></h4>
		<div class="job_description">
			{$aCompany.description_parsed|parse}
		</div>
			{if count($aFields)}
	        {foreach from=$aFields item=aField}
	            {template file='jobposting.block.custom.view'}
	        {/foreach}
	    	{/if}
		<div id="tabs_view" class = "yc_view_tab ync_code_txt ynjp_tabInformation">	
			<ul class="ync_tabs">
				<li id="tabs1"><a href="#tabs-1">{phrase var='general_information'}</a></li>
				<div class="onlymaxwidth768" id="divshowmore">
					<li>
						<a href="#" onclick="ynjobposting.showmoretab_company();return false">
							{phrase var='view_more'}&nbsp;<i class="fa fa-angle-down"></i>
						</a>
					</li>
				</div>
				<li id="tabs3" class="onlyminwidth768"><a href="#tabs-3">{phrase var='jobs'}</a></li>
				<li id="tabs4"  class="onlyminwidth768"><a href="#tabs-4"><span style="text-transform: capitalize;">{phrase var='employees'}</span></a></li>

			</ul>

			<ul class="ync_tabs_content">
				<li id="tabs-1">
					<h4><span> General Infomation </span></h4>
					<ul>
						<li>
							<span> {phrase var='headquarters'}: </span> <span> {$aCompany.location} </span>
						</li>
						<li>
							<span> {phrase var='website'}: </span> <span> <a target="_blankchange_owner" href="{$aCompany.website}"> {$aCompany.website} </a> </span>
						</li>
						{if isset($aCompany.size_from) && (int)$aCompany.size_from > 0
							&& isset($aCompany.size_to) && (int)$aCompany.size_to > 0
						}
							<li>
								<span> {phrase var='company_size'}: </span> <span> {$aCompany.size_from}-{$aCompany.size_to} </span>
							</li>
						{/if}
						<li>
							<span> {phrase var='industry'}: </span> <span> {$aCompany.industrial_phrase} </span>
						</li>
					</ul>			
					<h4><span> {phrase var='contact_information'} </span></h4>
					<ul>
						<li>
							<span> {phrase var='name'}: </span> <span class="ynjp_companyNameCo"> {$aCompany.contact_name} </span>
						</li>
						<li>
							<span> {phrase var='phone'}: </span> <span> {$aCompany.contact_phone} </span>
						</li>
						<li>
							<span> {phrase var='email'}: </span> <span> <a href="mailto:{$aCompany.contact_email}">{$aCompany.contact_email}</a> </span>
						</li>
						<li>
							<span> {phrase var='fax'}: </span> <span> {$aCompany.contact_fax} </span>
						</li>
					</ul>
					<h4 style="margin-bottom:20px;" class="ynjp_h4_location"><span>Location: <b> {$aCompany.city_country_phrase}</b></span></h4>
					 
					<iframe width="510"
							height="430"
							frameborder="0"
							scrolling="no"
							marginheight="0"
							marginwidth="0"
							src="//maps.google.com/maps?f=q&source=s_q&geocode=&q={$aCompany.encode_location_city_country_phrase}+&ll={$aCompany.latitude},{$aCompany.longitude}8&spn=0,0&t=m&z=12&output=embed">
					</iframe>

				</li>
				<li id="tabs-3">
				</li>
				<li id="tabs-4">
					{module name='jobposting.company.participant_company'}
				</li>
			</ul>
		</div>		
	</div>	
</div>
<div id="tabs3_viewcompany" class="ynjb_tabs3_viewcompany" style="display:none;">
		{template file="jobposting.block.job.mini_job_viewmore"}
						<span id="view_more_jobs"></span>
						{if $ViewMoreJob}
							<div id="href_view_more">
								<a href="#" onclick="$.ajaxCall('jobposting.view_more_jobs','iPage={$iPage}&company_id={$aCompany.company_id}');return false;">{phrase var='view_more'}</a>
							</div>
						{/if}
					{if count($aJobsSearch)==0}
						<div>{phrase var='no_jobs_found'}</div>
					{/if}
</div>

<script type="text/javascript">
var first = false
$Behavior.jobpostingInitilizeTabView = function() {l}
   if(first == false){l}
    $('._block.location_4').hide();
    first = true;
    {r}
    $( "#tabs_view" ).tabs();
    $Core.loadInit = ynjobposting.overridedLoadInitForTabView;

    $('#tabs1>a').click(function(){l}
        $('#js_feed_content').hide();
        $('.ync_tabs_content').show();
        $('#tabs3_viewcompany').hide();
        $('._block.location_4').hide();
    {r});
    $('#tabs3>a').click(function(){l}
        $('#js_feed_content').hide();
        $('.ync_tabs_content').hide();
        $('#tabs3_viewcompany').show();
        $('._block.location_4').hide();
    {r});
    $('#tabs4>a').click(function(){l}
        $('#js_feed_content').hide();
        $('.ync_tabs_content').show();
        $('#tabs3_viewcompany').hide();
        $('._block.location_4').hide();
    {r});
{r};
</script>
