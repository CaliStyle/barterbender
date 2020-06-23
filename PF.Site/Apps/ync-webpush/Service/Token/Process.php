<?php
namespace Apps\YNC_WebPush\Service\Token;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;

class Process extends Phpfox_Service
{
    /**
     * Process constructor.
     */
    private $_sUTTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncwebpush_browser_token');
        $this->_sUTTable = Phpfox::getT('yncwebpush_user_token');
    }

    public function addBrowserToken($sToken, $sBrowser = null)
    {
        if (empty($sToken)) {
            return false;
        }

        $iId = db()->select('id')
            ->from($this->_sTable)
            ->where('token = \'' . $sToken . '\' AND browser like \'' . $sBrowser . '\'')
            ->execute('getField');
        if ($iId) {
            //Update token
            db()->update($this->_sTable, ['last_update' => PHPFOX_TIME], 'id = ' . $iId);
        } else {
            //Add token
            $aInsert = [
                'token' => $sToken,
                'browser' => $sBrowser,
                'skip_time' => 0,
                'time_stamp' => PHPFOX_TIME,
                'last_update' => PHPFOX_TIME,
            ];
            db()->insert($this->_sTable, $aInsert);
        }

        $this->addUserToken($sToken, $sBrowser);

        return true;
    }

    public function addUserToken($sToken, $sBrowser, $iUserId = null, $bCheckToken = true)
    {
        if (!$sToken) {
            return false;
        }
        if ($iUserId === null) {
            $iUserId = Phpfox::getUserId();
        }
        //Disable other token in other user
        db()->update($this->_sUTTable, ['is_active' => 0],
            'token = \'' . $sToken . '\' AND browser = \'' . $sBrowser . '\'');

        //Clear skipped time
        Phpfox::removeCookie('ync_web_push_' . $iUserId);

        if ($bCheckToken) {
            //Clear all expired token of this browser
            $this->cronCheckExpiredToken($iUserId, $sBrowser, false);
        }
        //Current token
        $iId = db()->select('id')
            ->from($this->_sUTTable)
            ->where('token = \'' . $sToken . '\' AND browser like \'' . $sBrowser . '\' AND user_id = ' . $iUserId)
            ->execute('getField');

        //$iUserId = 0 > guest
        if ($iId) {
            db()->update($this->_sUTTable, ['is_active' => 1], 'id =' . $iId);
        } else {
            $aInsert = [
                'user_id' => $iUserId,
                'token' => $sToken,
                'time_stamp' => PHPFOX_TIME,
                'browser' => $sBrowser,
                'is_active' => 1
            ];
            db()->insert($this->_sUTTable, $aInsert);
        }

        //Clear cache token of this user
        $this->cache()->remove('yncwebpush_user_tokens_' . $iUserId);

        return true;
    }

    public function cronCheckExpiredToken($iUserId = null, $sBrowser = null, $bRemoveCache = true, $sToken = null)
    {
        $bResult = true;
        if (!empty($sToken)) {
            if (!empty($sBrowser)) {
                $sCond = 'token = \''.$sToken.'\' AND browser like \''.$sBrowser.'\'';
            } else {
                $sCond = 'token = \''.$sToken.'\'';
            }
        }
        elseif ($iUserId != null && $sBrowser != null) {
            $sCond = 'user_id = '.(int)$iUserId.' AND browser like \''.$sBrowser.'\'';
        } else {
            $sCond = '1 = 1';
        }
        $aAllToken = db()->select('id, token, user_id')
                    ->from($this->_sUTTable)
                    ->where($sCond)
                    ->execute('getSlaveRows');
        if (count($aAllToken)) {
            $aHeaders = array(
                'Authorization: key='. setting('yncwebpush_server_key'),
            );
            foreach ($aAllToken as $aToken) {
                $sUrl = 'https://iid.googleapis.com/iid/info/'. $aToken['token'];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $sUrl);
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeaders);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                $aResult = json_decode($result, true);
                if (isset($aResult['error'])) {
                    //Delete all expired token
                    db()->delete($this->_sUTTable, 'id = '.$aToken['id']);
                    $bResult = false;
                    if ($bRemoveCache) {
                        $this->cache()->remove('yncwebpush_user_tokens_' . $aToken['user_id']);
                    }
                }
            }
        }
        if (!empty($sToken)) {
            return $bResult;
        }
        return true;
    }
}
