<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="contest-music-listing">
    <article class="contest-music-item music_row" id="js_controller_music_track_{$aSong.song_id}" data-songid="{$aSong.song_id}" xmlns="http://www.w3.org/1999/html">
        <div class="item-outer {if !isset($aSong.is_in_feed) && $aSong.hasPermission}item-manage{/if}">
            <div class="item-title">
                <a class="fw-bold" href="{$aSong.url}">{$aSong.title|clean}</a>
            </div>
            <div class="item-media">
                <span class="button-play" onclick="$Core.music.playSongRow(this)"><i class="ico ico-play"></i></span>
            </div>
        </div>

        <div class="contest-item-player music_player">
            <div class="audio-player dont-unbind-children js_player_holder  {if !Phpfox::getUserParam('music.can_download_songs')}disable-download{/if}">
                <div class="js_music_controls">
                    <a href="javascript:void(0)" class="js_music_repeat ml-1" title="{_p('repeat')}">
                        <i class="ico ico-play-repeat-o"></i>
                    </a>
                </div>
                <audio class="js_song_player" src="{$aSong.song_path}" type="audio/mp3" controls="controls"></audio>
            </div>
        </div>
    </article>
</div>