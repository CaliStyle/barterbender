<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php



 if (! empty ( $this->_aVars['sCreateJs'] )): ?>
<?php echo $this->_aVars['sCreateJs'];  endif;  (($sPlugin = Phpfox_Plugin::get('user.template_controller_login_block__start')) ? eval($sPlugin) : false); ?>
<form method="post" action="<?php echo Phpfox::getLib('phpfox.url')->makeUrl("user.login"); ?>" id="js_login_form" <?php if (! empty ( $this->_aVars['sGetJsForm'] )): ?>onsubmit="<?php echo $this->_aVars['sGetJsForm']; ?>"<?php endif; ?>>
    <div class="form-group">
        <input class="form-control" placeholder="<?php if (Phpfox ::getParam('user.login_type') == 'user_name'):  echo _p('user_name');  elseif (Phpfox ::getParam('user.login_type') == 'email'):  echo _p('email');  else:  echo _p('email_or_user_name');  endif; ?>" type="<?php if (Phpfox ::getParam('user.login_type') == 'email'): ?>email<?php else: ?>text<?php endif; ?>" name="val[login]" id="login" value="<?php if (! empty ( $this->_aVars['sDefaultEmailInfo'] )):  echo $this->_aVars['sDefaultEmailInfo'];  endif; ?>" size="40" autofocus/>
    </div>

    <div class="form-group">
        <input class="form-control" placeholder="<?php echo _p('password'); ?>" type="password" name="val[password]" id="login_password" value="" size="40" autocomplete="off" />
    </div>

<?php if ($this->_aVars['bEnable2StepVerification']): ?>
        <div class="form-group">
            <input class="form-control" placeholder="<?php echo _p('passcode'); ?>" type="text" name="val[passcode]" id="passcode" value="" size="40" />
            <p class="help-block">
                <a class="no_ajax" target="_blank" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('user.passcode'); ?>"><?php echo _p('dont_receive_passcode_how_to_get_it'); ?></a>
            </p>
        </div>
<?php endif; ?>

<?php if (Phpfox ::isModule('captcha') && Phpfox ::getParam('user.captcha_on_login') && ( $this->_aVars['sCaptchaType'] = Phpfox ::getParam('captcha.captcha_type'))): ?>
        <div id="js_register_capthca_image" class="<?php echo $this->_aVars['sCaptchaType']; ?>">
<?php Phpfox::getBlock('captcha.form', array()); ?>
        </div>
<?php endif; ?>

<?php (($sPlugin = Phpfox_Plugin::get('user.template_controller_login_end')) ? eval($sPlugin) : false); ?>

    <div class="form-group remember-box">
        <div class="checkbox">
            <label>
                <input type="checkbox" class="checkbox" name="val[remember_me]" value="" />
<?php echo _p('remember'); ?>
            </label>
        </div>

        <div>
            <a class="no_ajax" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('user.password.request'); ?>"><?php echo _p('forgot_your_password'); ?></a>
        </div>
    </div>

    <div class="form-button-group">
        <button id="_submit" type="submit" class="btn btn-primary">
<?php echo _p('sign_in'); ?>
        </button>

<?php (($sPlugin = Phpfox_Plugin::get('user.template.login_header_set_var')) ? eval($sPlugin) : false); ?>

<?php if (Phpfox ::getParam('user.allow_user_registration')): ?>
            <div class="form-group new-member">
<?php echo _p('need_an_account'); ?>
<?php if (! empty ( $this->_aVars['bSlideForm'] )): ?>
                    <a href="javascript:void(0);" class="js-slide-btn"><?php echo _p('sign_up_now'); ?></a>
<?php else: ?>
                    <a class="keepPopup" rel="hide_box_title visitor_form" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('user.register'); ?>"><?php echo _p('sign_up'); ?></a>
<?php endif; ?>
            </div>
<?php endif; ?>

        <input type="hidden" name="val[parent_refresh]" value="1" />
    </div>

<?php if (isset ( $this->_aVars['bCustomLogin'] )): ?>
        <div class="form-button-group form-login-custom-fb">
            <div class="custom-fb-or"><span><?php echo _p('or'); ?></span></div>
            <div class="custom_fb">
<?php (($sPlugin = Phpfox_Plugin::get('user.template_controller_login_block__end')) ? eval($sPlugin) : false); ?>
            </div>
        </div>
<?php endif; ?>

</form>


