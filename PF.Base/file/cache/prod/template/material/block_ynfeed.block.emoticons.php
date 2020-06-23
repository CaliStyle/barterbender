<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<div class="dropdown ynfeed_select_emojis">
    <span class="dropdown-toggle ynfeed_btn_emojis" data-toggle="dropdown" title="<?php echo _p('insert_emojis'); ?>">
        <i class="ico ico-smile" aria-hidden="true"></i>
    </span>
    <div class="dropdown-menu ynfeed_emojis_popup">
        <span class="ynfeed_embox_title">
            <span class="ynfeed_emoji_title"></span>
            <span class="ynfeed_emoji_code pull-right"></span>
        </span>
        <br>
<?php if (count((array)$this->_aVars['aEmojis'])):  foreach ((array) $this->_aVars['aEmojis'] as $this->_aVars['aEmoji']): ?>
        <span class="ynfeed_embox_icon"
              onmouseover="$Core.ynfeedEmoticon.showEmojiTitle('<?php echo _p($this->_aVars['aEmoji']['title']); ?>', '<?php echo $this->_aVars['aEmoji']['code']; ?>')"
              onclick="$Core.ynfeedEmoticon.selectEmoji(this);" title=" <?php echo $this->_aVars['aEmoji']['code']; ?>"
              data-code="<?php echo $this->_aVars['aEmoji']['code']; ?>">
            <img src="<?php echo $this->_aVars['corePath']; ?>/assets/images/emoticons/<?php echo $this->_aVars['aEmoji']['image']; ?>"
                 border="0"
                 data-code="<?php echo $this->_aVars['aEmoji']['code']; ?>"
                 alt="<?php echo $this->_aVars['aEmoji']['image']; ?>">
        </span>
<?php endforeach; endif; ?>
    </div>
</div>
