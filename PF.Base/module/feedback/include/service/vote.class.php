<?php
    /*
    * @copyright        [YouNet_COPYRIGHT]
    * @author           YouNet Company
    * @package          Module_FeedBack
    * @version          2.01
    *
    */
    defined('PHPFOX') or exit('NO DICE!');
?>
<?php
    class FeedBack_Service_vote extends Phpfox_Service
    {

        public function __construct()
        {
            $this->_sTable = Phpfox::getT('feedback_vote');
        }
        public function getVote($feedback_id = null , $user_id = null)
        {
            if($feedback_id == null || $user_id == null)
            {
                return false;
            }
            $result = $this->database()->select('*')
                     ->from($this->_sTable,'fv')
                     ->where('fv.feedback_id = '.$feedback_id.' AND  fv.user_id ='.$user_id)
                     ->execute('getRow');
            return $result;
            
        }
        public function insertVote($feedback_id = null , $user_id = null)
        {
            if($feedback_id == null || $user_id == null)
            {
                return false;
            }
            $insertData = array(
                'feedback_id' => $feedback_id,
                'user_id' => $user_id,
                'params' => '',
                );
            $result = $this->database()->insert($this->_sTable,$insertData);
            return $result;
            
        }
        public function deleteVote($feedback_id = null, $user_id = null)
        {
            if($feedback_id == null || $user_id == null)
            {
                return false;
            }
            $result = $this->database()->delete($this->_sTable,'feedback_id = '.$feedback_id.' AND user_id = '.$user_id);
            return $result;
        }
       

    }
?>  