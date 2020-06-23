<article class="jobposting-job-item">
    <div class="item-outer">
        <!-- image -->
        <a class="item-media" href="{$sLink}">
            <span style="background-image: url({$sImageSrc})"></span>
        </a>

        <div class="item-inner">
            <!-- title -->

            <div class="item-title">
                <a href="{$sLink}" class="link" itemprop="url">{$aJob.title|clean}</a>
            </div>

            <!-- location -->
            {if !empty($aJob.working_place)}
            <div class="item-location">
                <span class="ico ico-checkin-o"></span>
                <span class="item-info">{$aJob.working_place}</span>
            </div>
            {/if}

            <div class="item-time-date">
                <span class="ico ico-calendar-o"></span>
                <span class="item-info">{_p var='expired_on'}: {$aJob.time_expire_micro}</span>
            </div>

            <div class="item-description">
                <span class="item-info">{$aJob.description_parsed|stripbb|feed_strip|split:55|max_line}</span>
            </div>
        </div>
    </div>
</article>