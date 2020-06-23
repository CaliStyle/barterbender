<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          3.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FoxFeedsPro_Component_Block_Search extends Phpfox_Component
{	
	public function process()
	{
        list($iCnt,$feed_searchs )= phpfox::getService('foxfeedspro')->getFeedsDisplay();
        $aFilters2  = phpfox::getLib('template')->getVar('aFilters');
        
        if(!isset($aFilters2) || $aFilters2 == null || count($aFilters2)<=0 )
        {
            return false;
        }
        if($this->getParam('nosearch') == 1)
        {
            return false;
        }
     	 $this->template()->assign(array(
                'sHeader' => _p('foxfeedspro.search_news'),
                'feed_searchs' =>$feed_searchs,
                'core_path'=>phpfox::getParam('core.path')

		
            )
        ); 
		return 'block';
	}

}

?>