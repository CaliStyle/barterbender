{if count($aFeaturedContests)>0}
	<div class="wrap_slider">
		<div class="owl-carousel yns_contest_carousel" id="yns_contest_carousel">
				{foreach from=$aFeaturedContests item=aContest}
					<div class="item" onclick="window.location.href='{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name}'">
						<span class="entype {$aContest.style_type}"></span> <!-- enblog // enphoto // envideo // enmusic -->
						{img server_id=$aContest.server_id path='core.url_pic' file="contest/".$aContest.image_path class='js_mp_fix_width'}
						<div class="slider_des">
                     <a class="title" href="{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name}">{$aContest.contest_name|clean}</a>
							<div class="slider_info">
								<!-- <p class="extra_info">
									{phrase var='contest.created_by'} {$aContest|user}
								</p> -->
								<p class="extra_info">
									{phrase var='contest.end_contest_on'}: <strong class="contest_end_date">{$aContest.end_time_parsed}</strong>
								</p>
							</div>
							<ul class="slider_date">
								<li>
									<span>{phrase var='contest.entries'}</span>
									<b>{$aContest.total_entry}</b>
								</li>
								<li>
									<span>{phrase var='contest.participants'}</span>
									<b>{$aContest.total_participant}</b>
								</li>
								<li class="yc_last">
									<span>{phrase var='contest.submit_entries'}</span>
									<b>
										{if $aContest.submit_timeline == 'opening'}
                                            {phrase var='contest.opening'}
                                        {elseif $aContest.submit_timeline == 'on_going'}
                                            {$aContest.submit_countdown}
                                        {elseif $aContest.submit_timeline == 'end'}
                                            {phrase var='contest.end'}
                                        {/if}
									</b>
								</li>
							</ul>
						</div>
					</div>
				{/foreach}
		</div>
	</div>
{literal}
<script>
   $Behavior.ynloadFeaturedContest = function(){
      $("#yns_contest_carousel").owlCarousel({
      navigation : true,
      slideSpeed : 300,
      paginationSpeed : 500,
      singleItem : true,
      pagination : false,
      autoPlay : {/literal}{$autoPlay}{literal}
      });
      $('.owl-buttons').addClass('dont-unbind');
      $('.owl-buttons .owl-prev').addClass('dont-unbind');
      $('.owl-buttons .owl-prev').addClass('dont-unbind');
   }
</script>
{/literal}
{/if}