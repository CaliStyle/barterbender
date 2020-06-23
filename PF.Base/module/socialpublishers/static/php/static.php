<?php
include "cli.php";
phpfox::isUser(true);
$oRequest = phpfox::getLib('request');
$bRedirect = $oRequest->get('redirect');
$sService = $oRequest->get('service');
$sUrlRedirect = phpfox::getLib('url')->makeUrl('socialpublishers.setting');
$aParams = $_REQUEST;
$bRedirect = 0;
if (isset($aParams['redirect']))
{
    $bRedirect = $aParams['redirect'];
}
$sFullName = "";
if (isset($aParams['service']) && $aParams['service'] == 'facebook')
{
    $aParams['session'] = str_replace('\"', '"', $aParams['session']);
    $aSession = (array) (json_decode($aParams['session']));
    list($aSession, $aExtra) = phpfox::getService('socialpublishers')->getProfile($aParams['service'], $aSession);
    phpfox::getService('socialpublishers')->addToken(phpfox::getUserId(), $aParams['service'], $aSession, $aExtra);
    $sFullName = isset($aExtra['full_name']) ? $aExtra['full_name'] : "";
    $sUrlRedirect = phpfox::getLib('url')->makeUrl('socialpublishers.setting');
    $bRedirect = 1;
}
if (isset($aParams['oauth_token']) && $aParams['oauth_token'] && isset($aParams['oauth_verifier']) && $aParams['oauth_verifier'])
{
    if (!isset($aParams['service']))
    {
        $aParams['service'] = 'twitter';
    }
    else
    {
        
    }
    $sService = $aParams['service'];
    $aToken['oauth_token'] = $aParams['oauth_token'];
    $aToken['oauth_verifier'] = $aParams['oauth_verifier'];
    list($aToken, $aExtra) = phpfox::getService('socialpublishers')->getProfile($aParams['service'], $aToken);
    phpfox::getService('socialpublishers')->addToken(phpfox::getUserId(), $aParams['service'], $aToken, $aExtra);
    $sFullName = isset($aExtra['full_name']) ? $aExtra['full_name'] : "";
    $sUrlRedirect = phpfox::getLib('url')->makeUrl('socialpublishers.setting');
    $bRedirect = 1;
}
$sFullName = phpfox::getLib('parse.input')->clean($sFullName);
$sConnected = _p('socialpublishers.connected_as', array('full_name' => '')) . ' ' . $sFullName;
?>
<?php if (isset($bRedirect) && $bRedirect == 1): ?>

    <script type="text/javascript">
        var openerurl = '<?php echo phpfox::getLib('url')->makeUrl('socialpublishers.setting'); ?>';
        if(opener.location.href == openerurl)
        {
            if(opener == null || opener == undefined){
                window.location.href = '<?php echo $sUrlRedirect; ?>';
            }else{
                opener.location='<?php echo $sUrlRedirect; ?>';
                self.close();
            }   
        }
        else
        {
            opener.document.getElementById('showpopup_span_connected_<?php echo $sService; ?>').innerHTML ="<?php echo $sConnected; ?>";
            opener.document.getElementById('showpopup_checkbox_connected_<?php echo $sService; ?>').checked =true;
            opener.document.getElementById('showpopup_checkbox_connected_<?php echo $sService; ?>').removeAttribute("onclick");
            self.close();
        }
            
    </script>
<?php else: ?>

    <script>
        var openerurl = '<?php echo phpfox::getLib('url')->makeUrl('socialpublishers.setting'); ?>';
        if(opener.location.href = openerurl)
        {
            opener.location='<?php echo $sUrlRedirect; ?>';
            self.close();
        }
        else
        {
            self.close();
        }
    </script>
<?php endif; ?>

