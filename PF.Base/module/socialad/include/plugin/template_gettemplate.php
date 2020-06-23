<?php

if ($sTemplate == 'api.block.gateway.form') {
    $aGatewayData = $this->_aVars['aGatewayData'];
    if (!Phpfox::getParam('socialad.activate_activity_points') && !empty($aGatewayData) && strpos($aGatewayData['item_number'], 'socialad') !== false) {
        $aGateways = $this->_aVars['aGateways'];;
        foreach ($aGateways as $key => $aGateway) {
            if ($aGateway['gateway_id'] == 'activitypoints') {
                unset($aGateways[$key]);
            }
        }
        $this->_aVars['aGateways'] = $aGateways;
    }
}
