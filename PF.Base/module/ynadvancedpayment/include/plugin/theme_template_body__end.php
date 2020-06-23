<?php
;

$sFullControllerName = Phpfox::getLib('module')->getFullControllerName();
if (PHpfox::isUser() && Phpfox::isModule('ynadvancedpayment') && $sFullControllerName == 'api.admincp/gateway/add') {
    $configuration = _p('ynadvancedpayment.configuration');
    $silentpost_url = Phpfox::getParam('core.path_file') . 'module/ynadvancedpayment/static/php/silentpost.php';
    $content = _p('ynadvancedpayment.enable_a_href_https_account_authorize_net_target_blank_silent_post_url_a_via_account_settings_choose_silent_post_url_the_silent_post_url_feature_should_be_enabled_and_the_silent_post_url_should_be_set_to_silentpost_url', array('silentpost_url' => $silentpost_url));

    $ccbill_guideline_value = _p('ynadvancedpayment.ynadvancedpayment_ccbill_guideline'
        , array(
            'success_url' => Phpfox::getParam('core.path') . 'api/gateway/callback/ccbill/status_ccbill-success/',
            'failure_url' => Phpfox::getParam('core.path') . 'api/gateway/callback/ccbill/status_ccbill-fail/',
        )
    );
    $itransact_guideline_value = _p('ynadvancedpayment_itransact_guideline', [
        'postback_url' => Phpfox::getParam('core.path_file') . 'module/ynadvancedpayment/static/php/postback.php'
    ]);
    $stripe_guideline_value = _p('ynadvancedpayment_stripe_guideline', [
        'callback_url' => Phpfox::getLib('gateway')->url('stripe')
    ]);
    $braintree_guideline_value = _p('ynadvancedpayment_braintree_guideline', [
        'callback_url' => Phpfox::getLib('gateway')->url('braintree')
    ]);
    $skrill_guideline_value = _p('ynadvancedpayment_skrill_guideline');
    $bitpay_guideline_value = _p('ynadvancedpayment_bitpay_guideline');
    $webmoney_guideline_value = _p('ynadvancedpayment_webmoney_guideline');
    $ccbill_guideline_value = str_replace("'", '&#039;', $ccbill_guideline_value);
    if (preg_match("/\n/i", $ccbill_guideline_value)) {
        $aParts = explode("\n", $ccbill_guideline_value);
        $ccbill_guideline_value = '';
        foreach ($aParts as $sPart) {
            $sPart = trim($sPart);
            if (empty($sPart)) {
                $ccbill_guideline_value .= '<br/> ';

                continue;
            }

            $ccbill_guideline_value .= $sPart . ' ';
        }
        $ccbill_guideline_value = trim($ccbill_guideline_value);
    }
    $ccbill_guideline_value = html_entity_decode($ccbill_guideline_value, null, 'UTF-8');
    ?>

    <script type="text/javascript">
        $Behavior.ynapShowHint = function () {
            var $input = $('input[name=id]'), // find id field
                help_content = {
                    'authorizenet': '<?php echo $content; ?>',
                    'ccbill': '<?php echo str_replace("'", "\'", $ccbill_guideline_value); ?>',
                    'itransact': '<?php echo $itransact_guideline_value; ?>',
                    'skrill': '<?php echo $skrill_guideline_value; ?>',
                    'webmoney': '<?php echo $webmoney_guideline_value; ?>',
                    'stripe': '<?php echo $stripe_guideline_value; ?>',
                    'bitpay': '<?php echo $bitpay_guideline_value; ?>',
                    'braintree': '<?php echo $braintree_guideline_value; ?>',
                };

            if ($input.length) {
                if (help_content.hasOwnProperty($input.val()) && !$('.' + $input.val() + '-configuration').length) {
                    $input.parent().prepend('<div class="form-group ' + $input.val() + '-configuration' + '">' +
                        '<label><?php echo $configuration; ?>:</label>' +
                        '<p class="help-block">' + help_content[$input.val()] + '</p>' +
                        '</div>');
                }
            }
        }
    </script>

    <?php
}
if (PHpfox::isUser() && Phpfox::isModule('ynadvancedpayment') && $sFullControllerName == 'api.admincp/gateway/index') {
    ?>
    <script type="text/javascript">
        $Behavior.ynremovepopupClass = function () {
            $('.link_menu ul a').attr('class', '');
        }
    </script>
    <?php
};
if (PHpfox::isAdmin() && Phpfox::isModule('ynadvancedpayment') && $sFullControllerName == 'subscribe.admincp/add') {
    $subsriptionid = _p('subscription_id');
    ?>
    <script type="text/javascript">
        $Behavior.ynapShowSubId = function () {
            var id = $('input[name=id]').val();
            $('#title').parents('.table').find('.table_left').prepend('<div class="table_left"><?php echo $subsriptionid; ?>: ' + id + '</div>');
        }
    </script>
    <?php
}
?>

