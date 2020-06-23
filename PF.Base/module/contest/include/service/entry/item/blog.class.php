<?php

defined('PHPFOX') or exit('NO DICE!');
require_once dirname(dirname(__file__)) . '/item/item_abstract.class.php';

class Contest_Service_Entry_Item_Blog extends Phpfox_service implements Contest_Service_Entry_Item_Item_Abstract{

	public function __construct()
	{
		$this->_sTable = Phpfox::getT('blog');
	}
	public function getAddNewItemLink($iContestId, $iSourceId = 1) {
	    if ($iSourceId == 2) {
            return Phpfox::getLib('url')->makeUrl('ynblog.add', array('module' => 'contest', 'item' => $iContestId));
        }

		$sAddParamName = Phpfox::getService('contest.constant')->getYnAddParamForNavigateBack();
		$sLink = Phpfox::getLib('url')->makeUrl('blog.add', array($sAddParamName => $iContestId));

		return $sLink;
	}

	public function getItemsOfCurrentUser($iLimit = 5, $iPage = 0,$iSourceId = 2)
	{
	    if ($iSourceId == 2) {
	        return $this->getAdvancedBlogItemsOfCurrentUser($iLimit, $iPage);
        }

		$sConds = 'is_approved = 1 AND post_status = 1 AND user_id = ' . Phpfox::getUserId() . ' ';
		//in case we encounter a post form, we know it is a search request
		if($iSearchId = Phpfox::getLib('request')->get('search-id') )
		{
			$sKeyword = Phpfox::getService('contest.helper')->getSearchKeyword($iSearchId);
			$sConds .= $this->database()->search($sType ='like%' , $mField = array('title'), $sSearch = $sKeyword) ;
		}

		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where($sConds)
			->execute('getSlaveField');	

			
		$aItems = $this->database()->select('* ' )
				->from($this->_sTable)
				->where($sConds)
				->limit($iPage, $iLimit, $iCnt)
				->order('time_stamp DESC')
				->execute('getSlaveRows');

        foreach ($aItems as $key => &$aItem) {
            if (isset($aItem['server_id'])) {
                $aItem['image_server_id'] = $aItem['server_id'];
            }
            if (!empty($aItem['image_path']) && (substr($aItem['image_path'], 0, 4) != 'blog') && (substr($aItem['image_path'], 0, 15) != 'ynadvancedblog')) {
                $aItem['image_path'] = 'blog' . DIRECTORY_SEPARATOR . $aItem['image_path'];
            }
        }
        
		return array($iCnt, $aItems);
	}

	public function getItemFromFox($iItemId) 
	{
		if(!$iItemId)
		{
			return false;
		}
		$aItem = $this->database()->select('b.*, bt.* ' )
				->from($this->_sTable, 'b')
				->leftJoin(Phpfox::getT('blog_text'), 'bt', 'b.blog_id = bt.blog_id')
				->where('b.is_approved = 1 AND b.post_status = 1 AND bt.blog_id = ' . $iItemId)
				->execute('getSlaveRow');
		if(!empty($aItem['image_path'])) {
            $aItem['image_path'] = 'blog' . DIRECTORY_SEPARATOR . $aItem['image_path'];
        }
		return $aItem;
	}

	public function getTemplateViewPath()
	{
		return 'contest.entry.content.blog';
	}

	public function getDataToInsertIntoEntry($iItemId, $iSourceId = 1)
	{
	    if ($iSourceId == 2) {
            $aItem = $this->getItemFromAdvancedBlog($iItemId);
        } else {
            $aItem = $this->getItemFromFox($iItemId);
        }

		//copy db
		// column name here must comply with column in db
		$aReturn = array(
		    'image_path' => isset($aItem['image_path']) ? $aItem['image_path'] : null,
			'blog_content' => $aItem['text'],
			'blog_content_parsed' => isset($aItem['text_parsed']) ? $aItem['text_parsed'] : $aItem['text'],
			'total_attachment' => $aItem['total_attachment'],
			);

		return $aReturn;
		//copy file
		
	}

	public function getDataFromFoxAdaptedWithContestEntryData($iItemId, $iSourceId = 1)
	{
        $oLibParse = Phpfox::getLib('parse.output');

        if ($iSourceId == 2) {
            $aItem = $this->getItemFromAdvancedBlog($iItemId);
            $aItem['blog_content'] = Phpfox::getParam('core.allow_html') ? $aItem['text'] : $oLibParse->parse($aItem['text']);
            $aItem['blog_content_parsed'] = Phpfox::getParam('core.allow_html') ? $aItem['text'] : $oLibParse->parse($aItem['text']);
            return $aItem;
        }

		$aItem = $this->getItemFromFox($iItemId);
		$aItem['blog_content'] = Phpfox::getParam('core.allow_html') ? $aItem['text_parsed'] : $oLibParse->parse($aItem['text']);
		$aItem['blog_content_parsed'] = Phpfox::getParam('core.allow_html') ? $aItem['text_parsed'] : $oLibParse->parse($aItem['text']);
		return $aItem;	
	}

    /*
     * FOLLOWING FUNCTION FOR SUPPORTING ADVANCED BLOGS
     */

    public function getAdvancedBlogItemsOfCurrentUser($iLimit = 5, $iPage = 0)
    {
        $sConds = 'is_approved = 1 AND post_status = \'public\' AND user_id = ' . Phpfox::getUserId() . ' ';
        //in case we encounter a post form, we know it is a search request
        if($iSearchId = Phpfox::getLib('request')->get('search-id') )
        {
            $sKeyword = Phpfox::getService('contest.helper')->getSearchKeyword($iSearchId);
            $sConds .= $this->database()->search($sType ='like%' , $mField = array('title'), $sSearch = $sKeyword) ;
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('ynblog_blogs'))
            ->where($sConds)
            ->execute('getSlaveField');


        $aItems = $this->database()->select('*, blog_id as item_id' )
            ->from(Phpfox::getT('ynblog_blogs'))
            ->where($sConds)
            ->limit($iPage, $iLimit, $iCnt)
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');

        return array($iCnt, $aItems);
    }

    public function getItemFromAdvancedBlog($iItemId)
    {
        if(!$iItemId)
        {
            return false;
        }
        $aItem = $this->database()->select('b.*, 0 as total_attachment' )
            ->from(Phpfox::getT('ynblog_blogs'), 'b')
            ->where('b.is_approved = 1 AND b.post_status = \'public\' AND b.blog_id = ' . $iItemId)
            ->execute('getSlaveRow');
        if(!empty($aItem['image_path'])) {
            $aItem['image_path'] = 'ynadvancedblog' . DIRECTORY_SEPARATOR . $aItem['image_path'];
        }
        return $aItem;
    }

}