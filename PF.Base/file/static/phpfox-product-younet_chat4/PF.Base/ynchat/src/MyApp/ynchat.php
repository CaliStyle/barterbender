<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Ynchat implements MessageComponentInterface {
    protected $_clients = array();
	private $_userids = array();
	protected $_oYNChat = null;

    public function __construct($oYNChat) {
        $this->_oYNChat = $oYNChat;
    }
	
	public function setCommunicateObject($oYNChat){
        $this->_oYNChat = $oYNChat;
    }
	
    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $id = $conn->resourceId;
        $this->_clients[$id] = array('conn' => $conn);
		
        if(defined('YNCHAT_DEBUG') && YNCHAT_DEBUG){
            echo "New connection! ({$conn->resourceId})\n";
            $this->log_message(YNCHAT_DIR . 'log/', "debug", "New connection! ({$conn->resourceId})\n" . '. Total connection(s): ' . count($this->_clients) . print_r('', true));
        }
        
    }

    public function onMessage(ConnectionInterface $from, $data) {
		
		$decodedData = $this->_decodeData($data);
		if($decodedData === false) {
            return $this->_handleInvalidRequest();
        }
		
		$aData = $decodedData['data'];
        $sUserIdHash = $aData['sUserIdHash'];
        $iUserId = $this->_oYNChat->validateUser($sUserIdHash);
        if($iUserId === false){
            // invalid user
            $this->_handleInvalidRequest($from);
        } else {
            $aData['iUserId'] = $iUserId;
            $actionName = '_action' . ucfirst($decodedData['action']);
            if(method_exists($this, $actionName)) {
                call_user_func(array($this, $actionName), $aData, $from);
            }
        }
    }
	
    public function onClose(ConnectionInterface $conn) {
		
		// update offline status
		$id = $conn->resourceId;
		$userId = $this->_clients[$id]['userId'];
        $aUserIds = $this->_getClientsByUserId($userId);
        if(count($aUserIds) == 1){
            $this->_oYNChat->updateUserStatus($userId, 'offline');
        }
		
		// update last activity
        $this->_oYNChat->updateLastActivity($userId);
		
		$this->_removeClientInUserIdList($id, $userId);
        unset($this->_clients[$id]);

        $this->_cacheOnlineUsers();
		
        if(defined('YNCHAT_DEBUG') && YNCHAT_DEBUG){
            echo "Connection {$conn->resourceId} has disconnected\n";
            $this->log_message(YNCHAT_DIR . 'log/', "debug", "Connection {$conn->resourceId} has disconnected\n" . '. Total connection(s): ' . count($this->_clients) . print_r('', true));
        }

    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        if(defined('YNCHAT_DEBUG') && YNCHAT_DEBUG){
            echo "An error has occurred: {$e->getMessage()}\n";
            $this->log_message(YNCHAT_DIR . 'log/', "error", "An error has occurred: {$e->getMessage()}\n" . '. Total connection(s): ' . count($this->_clients) . print_r('', true));
        }
        
        $conn->close();
    }
	
	private function _actionAuthConnectionWithPlatform($aData, $oClient = null){
        $this->_clients[$oClient->resourceId]['userId'] = $aData['iUserId'];
        $id = $oClient->resourceId;
        if(isset($this->_userids[$aData['iUserId']])){
            // insert more
            $this->_userids[$aData['iUserId']][] = $id;
        } else {
            // add new
            $this->_userids[$aData['iUserId']] = array($id);
        }

        // update available status
        $this->_oYNChat->updateUserStatus($aData['iUserId'], 'available');
        // update last activity
        $this->_oYNChat->updateLastActivity();

        $this->_cacheOnlineUsers();

        $oClient->send($this->_encodeData('echo', 'authConnectionWithPlatformRes', array()));
    }
	
	private function _actionSendMessageAsText($aData, $oClient = null){
        // init
        $aData['sText'] = (string)$aData['sText'];
        if(strlen($aData['sText']) <= 0 && (int)$aData['iMessageId'] <= 0 && (int)$aData['iStickerId'] <= 0){
            return false;
        }

        // process
        if((int)$aData['iStickerId'] > 0){
            $aVals = array(
                'from' => $this->_oYNChat->getUserId(),
                'to' => (int) $aData['iReceiverId'],
                'message' => '',
                'sticker_id' => (int)$aData['iStickerId'],
                'read' => 0,
                'direction' => 0,
                'message_type' => 'sticker',
            );
            $aMessage = $this->_oYNChat->addMessage($aVals);
        } else if((int)$aData['iMessageId'] > 0){
            $aMessage = $this->_oYNChat->getMessageByMessageId((int)$aData['iMessageId']);
        } else {
            // store database
            $aVals = array(
                'from' => $this->_oYNChat->getUserId(),
                'to' => (int) $aData['iReceiverId'],
                'message' => $aData['sText'],
                'read' => 0,
                'direction' => 0,
            );
            $aMessage = $this->_oYNChat->addMessage($aVals);
        }

        // find receiver(s) and send notification
        if(isset($aMessage['iMessageId'])){
            // check CAN send message to receiver
            if($this->_oYNChat->canSendMessage($this->_oYNChat->getUserId(), $aData['iReceiverId'])){
                //$sDataTime = $this->_oYNChat->getTimeStamp() . '_' . $iMessageId;
                $aUserIds = $this->_getClientsByUserId($aData['iReceiverId']);
                $bSend = false;
                if(count($aUserIds) > 0){
                    $bSend = true;
                    foreach($aUserIds as $key){
                        $this->_clients[$key]['conn']->send($this->_encodeData('echo', 'sendMessageAsTextRes', $aMessage));
                    }
                }				
            }

            $aUserIds = $this->_getClientsByUserId($this->_oYNChat->getUserId());
            if(count($aUserIds) > 0){
                foreach($aUserIds as $key){
                    $this->_clients[$key]['conn']->send($this->_encodeData('echo', 'sendMessageAsTextRes', $aMessage));
                }
            }            
        }
        // end
    }
	
	private function _actionSendMessageWithFiles($aData, $oClient = null){
        $sender = $aData['iUserId'];
        $receiver = $aData['iReceiverId'];
        $aVals = array(
            'from' => $aData['iUserId'],
            'to' => (int) $aData['iReceiverId'],
            'message' => $aData['sText'],
            'read' => 0,
			'direction' => 0,
            'data' => $aData['files'],
            'message_type' => 'file',
        );
        $aMessage = $this->_oYNChat->addMessage($aVals);
        $aUserIds = $this->_getClientsByUserId($aData['iReceiverId']);
        if(count($aUserIds) > 0){
            foreach($aUserIds as $key){
                $this->_clients[$key]['conn']->send($this->_encodeData('echo', 'sendMessageWithFilesRes', $aMessage));
            }
        }
        
        $aUserIds = $this->_getClientsByUserId($aData['iUserId']);
        if(count($aUserIds) > 0){
            foreach($aUserIds as $key){
                $this->_clients[$key]['conn']->send($this->_encodeData('echo', 'sendMessageWithFilesRes', $aMessage));
            }
        }
    }
	
	private function _actionEcho($text) {
        $encodedData = $this->_encodeData('echo', 'echo', $text);
        foreach($this->_clients as $sendto)
        {
            $sendto['conn']->send($encodedData);
        }
    }
	
	private function _getClientsByUserId($iUserId){
        if(isset($this->_userids[$iUserId])){
            return $this->_userids[$iUserId];
        }

        return array();
    }
	
	private function _removeClientInUserIdList($sClientId, $iUserId){
        $iUserId = (int)$iUserId;
        if(isset($this->_userids[$iUserId])){
            foreach($this->_userids[$iUserId] as $key => $val){
                if($val == $sClientId){
                    unset($this->_userids[$iUserId][$key]);
                    // existing ONLY 1 connection key in list, so we break in here now
                    break;
                }
            }

            if(count($this->_userids[$iUserId]) == 0){
                unset($this->_userids[$iUserId]);
            }

            return true;
        }

        return false;
    }
	
	protected function _encodeData($action, $method, $data) {
        if(empty($action) || empty($method)) {
            return false;
        }

        $payload = array(
            'action' => $action,
            'method' => $method,
            'data' => $data
        );

        return json_encode($payload);
    }
	
	private function _decodeData($data) {
        $decodedData = json_decode($data, true);
        if($decodedData === null) {
            return false;
        }

        if(isset($decodedData['action'], $decodedData['data']) === false) {
            return false;
        }

        return $decodedData;
    }
	
	private function _handleInvalidRequest($oClient = null){
        return false;
    }

    public function log_message($path, $level = 'error', $msg)
    {
            //if ($_SERVER['REMOTE_ADDR'] == '86.182.174.205' || $_SERVER['REMOTE_ADDR'] == '173.212.202.170' || $_SERVER['REMOTE_ADDR'] == '113.161.85.105')
            {
                    
                    $_date_fmt = 'Y-m-d H:i:s';
                    $filepath = $path . 'log-' . date('Y-m-d') . '.php';

                    $message = '';

                    if (!file_exists($filepath))
                    {
                            $message .= "<" . "?php  ; ?" . ">\n\n";
                    }

                    if (!$fp = @fopen($filepath, 'ab'))
                    {
                            return FALSE;
                    }

                    $message .= $level . ' ' . (($level == 'INFO') ? ' -' : '-') . ' ' . date($_date_fmt) . ' --> ' . $msg . "\n";

                    flock($fp, LOCK_EX);
                    fwrite($fp, $message);
                    flock($fp, LOCK_UN);
                    fclose($fp);

                    @chmod($filepath, 0666);
                    return TRUE;
            }
    }

    private function _cacheOnlineUsers()
    {
        $userIds = array();
        foreach ($this->_clients as $key => $client) {
            if (isset($client['userId'])) {
                $userIds[] = $client['userId'];
            }
        }

        $file = YNCHAT_DIR . 'cache/online-users.txt';
        if (!$fp = @fopen($file, 'w')) {
            return false;
        }

        $content = implode(',', array_unique($userIds));

        flock($fp, LOCK_EX);
        fwrite($fp, $content);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($file, 0666);
        return TRUE;
    }
}