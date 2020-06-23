<div class="clear"></div>
<input type="hidden" id='init_once_music' value="0">
{if !empty($sImageUrl)}
<img src="{$sImageUrl}" alt="" style="max-height: 200px">
{/if}
<div class="younet_html5_player init not-unbind">
    <div class="yncontest-music">
        <audio class="yncontest-audio-skin" class="mejs" width="493" src="{$aMusicEntry.song_path}" type="audio/mp3" controls="controls" autoplay="true" preload="none"></audio>
    </div>
</div>
{literal}
<style type="text/css">
	audio{
		width: 100%;
	}
</style>
{/literal}
{if $bIsPreview}
<script type="text/javascript">
</script>
{/if}
