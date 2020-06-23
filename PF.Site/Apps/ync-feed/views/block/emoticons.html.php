<div class="dropdown ynfeed_select_emojis">
    <span class="dropdown-toggle ynfeed_btn_emojis" data-toggle="dropdown" title="{_p var='insert_emojis'}">
        <i class="ico ico-smile" aria-hidden="true"></i>
    </span>
    <div class="dropdown-menu ynfeed_emojis_popup">
        <span class="ynfeed_embox_title">
            <span class="ynfeed_emoji_title"></span>
            <span class="ynfeed_emoji_code pull-right"></span>
        </span>
        <br>
        {foreach from=$aEmojis item=aEmoji}
        <span class="ynfeed_embox_icon"
              onmouseover="$Core.ynfeedEmoticon.showEmojiTitle('{_p var=$aEmoji.title}', '{$aEmoji.code}')"
              onclick="$Core.ynfeedEmoticon.selectEmoji(this);" title="{_p($aEmoji.title)} {$aEmoji.code}"
              data-code="{$aEmoji.code}">
            <img src="{$corePath}/assets/images/emoticons/{$aEmoji.image}"
                 border="0"
                 data-code="{$aEmoji.code}"
                 alt="{$aEmoji.image}">
        </span>
        {/foreach}
    </div>
</div>