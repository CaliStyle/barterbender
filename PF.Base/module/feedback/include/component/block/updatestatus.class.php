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
class FeedBack_Component_Block_UpdateStatus extends Phpfox_Component
{
    public function process()
    {
        $feedback_id = $this->getParam('feedback_id');
        $aFeedBack = Phpfox::getService('feedback')->getFeedBackById($feedback_id);
        $aStatus = phpfox::getLib('database')
                ->select('*')
                ->from(phpfox::getT('feedback_status'))
                ->where(1)
                ->execute('getRows');
         
        foreach($aStatus as $key=>$Status){
            $Status['name'] = Phpfox::getLib('locale')->convert($Status['name']);
            $aStatus[$key] = $Status;
        }
        
        $this->template()->assign(array(
            'aFeedBack' => $aFeedBack, 
            'aStatus' => $aStatus
        ));
        return 'block';
    }
}
?>