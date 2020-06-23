<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php



 if (isset ( $this->_aVars['bCaptchaPopup'] ) && $this->_aVars['bCaptchaPopup']): ?>
    <div id="js_captcha_load_for_check" data-type="<?php echo $this->_aVars['sCaptchaType']; ?>">
        <form method="post" action="#" id="js_captcha_load_for_check_submit" class="form">
<?php endif; ?>
            <div class="form-group">
<?php if ($this->_aVars['sCaptchaType'] == 'default'): ?>
                    <div class="captcha_title"><?php echo _p('captcha_challenge'); ?></div>
                    <div class="go_left">
                        <a href="#" onclick="$('#js_captcha_process').html($.ajaxProcess('<?php echo _p('refreshing_image', array('phpfox_squote' => true)); ?>')); $('#js_captcha_image').ajaxCall('captcha.reload', 'sId=js_captcha_image&amp;sInput=image_verification'); return false;"><img src="<?php echo $this->_aVars['sImage']; ?>" alt="<?php echo _p('reload_image'); ?>" id="js_captcha_image" class="captcha" title="<?php echo _p('click_refresh_image'); ?>" /></a>
                    </div>
                    <a href="#" onclick="$('#js_captcha_process').html($.ajaxProcess('<?php echo _p('refreshing_image', array('phpfox_squote' => true)); ?>')); $('#js_captcha_image').ajaxCall('captcha.reload', 'sId=js_captcha_image&amp;sInput=image_verification'); return false;" title="<?php echo _p('click_refresh_image', array('phpfox_squote' => true)); ?>"><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'misc/reload.gif','alt' => 'Reload')); ?></a>
                    <span id="js_captcha_process"></span>
                    <div class="clear"></div>
                    <div class="captcha_form">
                        <input class="form-control" type="text" name="val[image_verification]" size="10" id="image_verification" />
                        <div class="help-block">
<?php echo _p('type_verification_code_above'); ?>
                        </div>
                    </div>
                    <script type="text/javascript">
                      $Behavior.loadImageVerification = function(){
                      $('#image_verification').attr('autocomplete', 'off');
                      }
                    </script>
<?php elseif ($this->_aVars['sCaptchaType'] == 'qrcode'): ?>
                    <div class="captcha_title"><?php echo _p('captcha_challenge'); ?></div>
                    <div class="">
                        <a type="button">
                            <img src="<?php echo $this->_aVars['sImage']; ?>" class="captcha"/>
                        </a>
                    </div>
                    <div class="pt-1">
                        <div class="captcha_extra_info pb-1"><?php echo _p('captcha_qrcode_challenge'); ?></div>
                        <input class="form-control" type="text" name="val[image_verification]" size="10" id="image_verification" />
                        <div class="help-block">
<?php echo _p('type_verification_code_above'); ?>
                        </div>
                    </div>
<?php elseif ($this->_aVars['sCaptchaType'] == 'recaptcha'): ?>
                    <?php echo '
                        <script type="text/javascript">
                          $Behavior.onLoadEvents = function () {
                            $Core.captcha.loadRecaptchaApi();
                          }
                        </script>
                    '; ?>

                    <div class="g-recaptcha" data-sitekey="<?php echo $this->_aVars['sRecaptchaPublicKey']; ?>" data-type="<?php echo $this->_aVars['sRecaptchaType']; ?>"></div>
<?php if ($this->_aVars['sRecaptchaType'] == 3): ?>
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
<?php if (isset ( $this->_aVars['bCaptchaPopup'] ) && $this->_aVars['bCaptchaPopup']): ?>
                            <img src="<?php echo $this->_aVars['sRecaptchaV3Img']; ?>"/>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
            </div>
<?php if (isset ( $this->_aVars['bCaptchaPopup'] ) && $this->_aVars['bCaptchaPopup']): ?>
            <div class="form-group">
                <input type="submit" value="<?php echo _p('submit'); ?>" class="btn btn-primary" />
                <input type="button" value="<?php echo _p('cancel'); ?>" class="btn btn-default" onclick="$('#js_captcha_load_for_check').hide();isAddingComment = false;" />
            </div>
        
</form>

    </div>
<?php endif; ?>
