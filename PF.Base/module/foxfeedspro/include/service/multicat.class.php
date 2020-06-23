<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Foxfeedspro_Service_Multicat extends Phpfox_Service
{
    
    protected $_css  = array(
        'id'=>'', 
        'class'=>'ulLevelMenu action', 
        'style'=>''
    );
    
	CONST CACHE_ENABLED = FALSE;
    
    protected $_module = 'foxfeedspro';
    
    protected $_col_name = 'ordering';
    
    protected $_col_id = 'category_id';
    
    protected $_col_parent_id = 'parent_id';
    
    protected $_cache_key = 'damde';
    

    /**
     * constructor
     * @return void
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynnews_categories');
    }
    

    /**
     * get data in structure
     * @return array 
     */
    public function loadData($iUserId = 0)
    {
        # get from database
		/*
        $temp_aItems = $this->database()
            ->select('c.*')
            ->from($this->_sTable, 'c')
            ->order($this->_col_name . ' ASC')
            ->execute('getSlaveRows');
		*/
		$temp_aItems = $this->database()->select('name, category_id, parent_id, ordering, name_url')
			->from($this->_sTable)
			->where('is_active = 1 AND user_id = '.(int) $iUserId)
			->order('ordering ASC')
			->execute('getRows');

		// customize for load custom feed here.
		$temp_feeds = $this->database()
			->select('d.category_id, f.feed_id, f.feed_name,f.logo_mini_logo, f.feed_logo, c.parent_id')
			->from(Phpfox::getT('ynnews_category_data'), 'd')
			->join(Phpfox::getT('ynnews_feeds'),'f','d.feed_id = f.feed_id')
            ->join($this->_sTable, 'c', 'c.category_id = d.category_id')
			->where('d.user_id = '.(int) $iUserId.' and d.category_id <> 0 and f.is_approved = 1 and f.is_active = 1')
			->order('d.category_id, f.feed_name')
			->execute('getRows');
        // continue with real category
        $temp_aFeeds = $temp_feeds;
        foreach($temp_aFeeds as $aFeed) {
            foreach($temp_feeds as $k=>$feed) {
                if($feed['feed_id']==$aFeed['feed_id'] && $feed['category_id']==$aFeed['parent_id']) {
                    unset($temp_feeds[$k]);
                }
            }
        }
        
		
		// final list of items.
        $aItems = array();

        // map parent_item_id => parent_id
        foreach ($temp_aItems as $aItem)
        {
			$id = $aItem['category_id'];
			
			if(count($temp_feeds))
			{
				foreach($temp_feeds as $key=>$feed)
				{
					if($feed['category_id'] == $id){
						$aItem['items'][] = $feed;
						unset($temp_feeds[$key]);
					}
				}
			}
            $aItems[$aItem[$this->_col_id]] = $aItem;
        }
		
		unset($temp_feeds);

        // manual unset items for save memory.
        unset($temp_aItems);
		
        for ($i = 0; $i < 10; $i++)
        {
            $map_p2c = array();
            
            // map parent_item_id => parent_id
            foreach ($aItems as $aItem)
            {
                $map_p2c[$aItem[$this->_col_parent_id]] = 1;
            }

            $found = false;
            
            foreach ($aItems as $key => $aItem)
            {
                $id = $aItem[$this->_col_id];
                $pid = $aItem[$this->_col_parent_id];
                if($iUserId)
                    $aItems[$key]['name'] = $aItem['name'];
                else $aItems[$key]['name'] = Phpfox::getLib('locale')->convert(Phpfox::isPhrase($aItem['name']) ? _p($aItem['name']) : $aItem['name']);
                // process p1.
                if (0 == $pid)
                {
                    continue;
                }

                if (!isset($map_p2c[$id]) or $map_p2c[$pid] == 2)
                {
                    $found = true;
                    if (isset($aItems[$pid]) && is_array($aItems[$pid]))
                    {
                        $aItems[$pid]['items'][] = $aItem;
                    }
                    unset($aItems[$key]);
                }
                else
                {
                    $map_p2c[$pid] == 2;
                }
            }
            if (!$found)
            {
                break;
            }
        }

        # return result
        return $aItems;
    }


    /**
     * render items
     * @return string 
     */
    protected function renderMenu($aData, $level, $iIsMine)
    {
        $aItems = array();

		if ($level == 0)
		{
			$aItems = isset($aData['aItems']) ? $aData['aItems'] : array();
		}
		else
		{
			$aItems = $aData;
		}
        
        $css = isset($aData['css']) ? $aData['css'] : null;
        
        if (empty($aItems))
        {
            return '';
        }

        $xhtml = array();

        foreach ($aItems as $key_item => $aItem)
        {
            if(isset($aItem['logo_mini_logo'])){

                if(strpos($aItem['logo_mini_logo'], "http") !== false){
                    //$aFeeds[$key]['logo_mini_logo'] = $aFeeds[$key]['logo_mini_logo'];
                }
                else{

                    $aItem['logo_mini_logo'] = Phpfox::getParam('core.url_pic') . "foxfeedspro/" . $aItem['logo_mini_logo'];
                }  


            }
            try
            {
                $sub = '';

                if (isset($aItem['items']) && $aItem['items'])
                {
                    $sub = $this->renderMenu($aItem['items'], $level + 1, $iIsMine);
                }

                $xhtml[] = $this->renderItem($aItem, $sub, $iIsMine);

            }
            catch (exception $e) 
			{
				echo $e->getMessage();
			}
        }

        $xhtml = sprintf('<ul class="%s" id="%s" style="%s">%s</ul>', "ulLevelMenu action", $css['id'], $css['style'], implode(PHP_EOL, $xhtml));

        return $xhtml;
    }

    protected function renderItem($aItem, $sub, $iIsMine)
    {   
		$label = $pattern = $url = '';
		$delete_btn = '';
		$my_cat_feed_id = '';
		if(isset($aItem['feed_id']))
		{
			if($iIsMine == 1)
			{
				$delete_btn = '<span style="order: 2; display: flex;justify-content: center;align-items: center;"><a class="delete_feed_button" href="javascript:void(0);" onclick="$Core.jsConfirm({message: \''._p('are_you_sure').'\'}, function () { $.ajaxCall(\'foxfeedspro.deleteMyCatFeed\',\'feed_id='.$aItem['feed_id'].'\');}, function () {});"><img style="vertical-align:middle" src="'.Phpfox::getParam('core.path').'theme/frontend/default/style/default/image/misc/delete_hover.gif"></a></span>';
				$my_cat_feed_id = 'id = "my_cat_feed_'.$aItem['feed_id'].'"';
			}
			$label = $aItem['feed_name'];
			$url = Phpfox::getLib('url')->makeUrl('foxfeedspro.feeddetails.feed_'. $aItem['feed_id']);
			if($aItem['logo_mini_logo']){
				$pattern = '<li style="display: flex;justify-content: center;align-items: center;" '.$my_cat_feed_id.'>%s<a style="order: 1;" href="%s" class="ffpro_feed_link"><span><img src="%s" width="16" height="16"/> %s</span></a>%s</li>';
				return sprintf($pattern, $delete_btn, $url, $aItem['logo_mini_logo'], $label, $sub);	
			}
			$pattern = '<li'.$my_cat_feed_id.'>%s<a href="%s"  class="ffpro_feed_link"><span>%s</span></a>%s</li>';
			return sprintf($pattern, $delete_btn, $url, $label, $sub);				
		}	

		$label = Phpfox::getLib('locale')->convert(Phpfox::getLib('parse.input')->clean($aItem['name']));
        $labelurl = Phpfox::getLib('parse.input')->cleanTitle($aItem['name']);
		$pattern = '<li style="padding-left: 20px;"><div class="_moderator" style="position: absolute;left: 0;top: 11px; border: none; background: transparent;">
            <div class="row_edit_bar_parent">
              <div class="row_edit_bar">
                <a href="#" class="row_edit_bar_action" style="padding-right: 0;" data-toggle="dropdown"><i class="fa fa-action"></i></a>
                   <ul class="dropdown-menu">
                   <li>
                       <a class="buttonlink inlinePopup" href="%s" title="">'._p('foxfeedspro.edit').'</a>
                   </li>
                   <li class="item_delete">
                       <a class="buttonlink inlinePopup" href="javascript:void(0);" onclick="%s" title="">'._p('foxfeedspro.delete').'</a>
                   </li>
                   </ul>
                </div>
             </div>
         </div>';
        if(!empty($sub)) {
            $pattern .= '<a href="%s" class="ffpro_cat_link ffpro_my_sub">%s</a><span class="ffpro_arrow_sub"></span>%s</li>';
        }
        else{
            $pattern .= '<a href="%s" class="ffpro_cat_link">%s</a><span></span>%s</li>';
        }
        $urlEdit =  Phpfox::getLib('url')->makeUrl('foxfeedspro.add.'.'id_'.$aItem['category_id']);
        $onClick = "\$Core.jsConfirm({message: '"._p('are_you_sure')."'}, function () { $.ajaxCall('foxfeedspro.deleteMyCategory','&category_id=".$aItem['category_id']."');}, function () {});";
        $url = Phpfox::getLib('url')->makeUrl('foxfeedspro.category.'.$aItem['category_id'].'/'.$labelurl);
		
		return sprintf($pattern,$urlEdit,$onClick, $url, $label, $sub);
    }

    public function toHtml($iUserId = 0, $css = null, $iIsMine = 0)
    {
        # get data from cache
		
        //$sCacheId = $this->cache()->set($this->_cache_key);
        //$aItems = $this->cache()->get($sCacheId);
        $aItems = $this->loadData($iUserId);
		
        if (!$aItems)
        {
			return '';
            # update cache
          //  $this->cache()->save($sCacheId, $aItems);
        }
        
        # include css
        if($css)
        {
            $this->_css = array_merge($this->_css, $css);
        }
        
        $aData = array(
            'css' => $this->_css,
            'aItems' => $aItems
        );
        
        return $this->renderMenu($aData, 0, $iIsMine);
    }
}

?>
