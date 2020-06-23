<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$sModule = phpfox::getLib('module')->getModuleName();
$sController = Phpfox::getLib('module')->getFullControllerName();
$sReq = PHPFOX::getLib('request')->get('group');
if ($sReq && flavor()->active->id != 'material') {
    ?>
    <script>
        $Behavior.onCreateTab = function () {
            var oOject = $('#group_<?php echo $sReq;?>');
            var oCustum = $('.js_custom_group_<?php echo $sReq;?>');
            var sReg = <?php echo (int)$sReq; ?>;
            if (oOject.length <= 0) {
                var iCount = sReg;
                for (i = iCount + 1; i < 10; i++) {
                    oOject = $('#group_' + i);
                    oCustum = $('.js_custom_group_' + i);
                    if (oOject.length > 0) {
                        break;
                    }
                }
                if (oOject.length <= 0) {
                    oOject = $('#group_basic');
                    oCustum = $('.js_custom_group_basic');
                }
            }
            oOject.parents('ul:first').find('li').removeClass('active');
            oOject.parent().addClass('active');
            $('.js_custom_groups').hide();
            oCustum.show();
            return false;
        };
    </script>
    <?php
} ?>
