<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class Contactimporter_Component_Controller_Fb extends Phpfox_Component
{
    public function process()
    {
        if (in_array($_SERVER['REMOTE_ADDR'], array('113.161.85.105', '183.91.25.214'))) {
            define('PHPFOX_DEBUG', 1);
            define('PHPFOX_DEBUG_LEVEL', 1);
        }
        if (!class_exists('YNCFacebook')) {
            require_once(PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'component' . PHPFOX_DS . 'controller' . PHPFOX_DS . 'Apiconnection' . PHPFOX_DS . 'facebook.php');
        }

        $url = phpfox::getLib('url')->makeUrl('contactimporter.fbcontact');
        $fb_settings = Phpfox::getLib('phpfox.database')->select('*')
            ->from(Phpfox::getT('contactimporter_api_settings'), 'st')
            ->where('st.api_name = "facebook"')
            ->execute('getRow');
        if ($fb_settings == null) {
            echo _p('please_enter_your_facebook_api');
            exit;
        }
        $fb_settings['api_params'] = unserialize($fb_settings['api_params']);
        $facebook = new YNCFacebook(array(
            'appId' => $fb_settings['api_params']['appid'],
            'secret' => $fb_settings['api_params']['secret'],
            'cookie' => true,
        ));

        $session = $facebook->getAccessToken();
        $facebook->setAccessToken($session);
        if (in_array($_SERVER['REMOTE_ADDR'], array('113.161.85.105', '183.91.25.214'))) {
            print_r($session);
            exit;
        }
        echo "<script>opener.parent.location.href = '" . $url . "';</script>";
        echo "<script>self.close();</script>";
        exit;
    }
}

?>
