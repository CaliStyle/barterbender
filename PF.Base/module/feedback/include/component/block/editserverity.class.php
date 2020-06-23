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
class FeedBack_Component_Block_EditServerity extends Phpfox_Component
{
    public function process()
    {
        $serverity_id=$this->getParam('serverity_id');
        $page = $this->getParam('page');
        $aSer =  phpfox::getLib('phpfox.database')->select('*')
                    ->from(Phpfox::getT('feedback_serverity'))
                    ->where('serverity_id='.$serverity_id)
                    ->execute('getRow');
        $this->template()->assign(array(
            'sHeader' => 'Edit Serverity',
            'aSer' => $aSer,
        	'page'=>$page,
        	'core_path' => Phpfox::getParam('core.path'),
        ));
        return 'block';

    }
}
?>