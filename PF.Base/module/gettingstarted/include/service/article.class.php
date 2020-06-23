<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_Gettingstarted
 * @version          3.02p5
 */
defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_Service_Article extends Phpfox_Service
{
    /**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('gettingstarted_article');
	}
	
    public function unCategory($iCatId)
    {
        $aArticles = $this->database()->select('*')
            ->from($this->_sTable, 'a')
            ->where('article_category_id='.(int)$iCatId)
            ->execute('getSlaveRows');
        
        $aUpdate = array('article_category_id' => -1);
        foreach($aArticles as $aArticle) {
            $this->database()->update($this->_sTable, $aUpdate, 'article_id='.(int)$aArticle['article_id']);
        }
        return true;
    }
}