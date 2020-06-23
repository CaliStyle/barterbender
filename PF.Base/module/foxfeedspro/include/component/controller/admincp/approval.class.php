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

    class FoxFeedsPro_Component_Controller_Admincp_Approval extends Phpfox_Component
    {
        public function process()
        {
            if($this->request()->get('approval'))
            {
                $arr_select = $this->request()->get('arr_selected');
                $arr_select = substr($arr_select, 1);
                $arr_select = explode(",", $arr_select);
                if(!empty($arr_select))
                {
                	foreach($arr_select as $iKey => $aValue)
                	{
                		$user_id = phpfox::getLib('database')->select('owner_id')
									->from(phpfox::getT('ynnews_items'))
									->where('item_id = '.$aValue)
									->execute('getSlaveField');
						if (Phpfox::isModule('notification')) {
							Phpfox::getService('notification.process') -> add('foxfeedspro_newsapproved', $aValue, $user_id);
						}
						phpfox::getService('foxfeedspro')->addNewsTags($aValue);
                		Phpfox::getLib('phpfox.database')->update(Phpfox::getT('ynnews_items'),array('is_approved'=>1), 'item_id = '.$aValue);
                	}
                }
                //Phpfox::getLib('phpfox.database')->update(Phpfox::getT('ynnews_items'),array('is_approved'=>1), 'item_id IN ('.$arr_select.')');
            }
            if($this->request()->get('unapproval') == 'Unapproval')
            {
                $arr_select = $this->request()->get('arr_selected');
                $arr_select = substr($arr_select,1);
                //Phpfox::getLib('phpfox.database')->update(Phpfox::getT('ynnews_items'),array('is_approved'=>0), 'item_id IN ('.$arr_select.')');
            }
            $feeds = phpfox::getService('foxfeedspro')->getFeed();    
            $arr_feed = array();
			$feeds_approval_status = array();
            foreach($feeds as $feed)
            {
                if(strlen($feed['feed_name']) > 80)
                {
                    $feed['name'] = substr($feed['feed_name'], 0, 80) . '...';

                }
                else
                {
                    $feed['name'] = $feed['feed_name'];
                }

                $arr_feed[$feed['feed_id']]=$feed['name'] ;
				$feeds_approval_status[$feed['feed_id']] = $feed['is_approved'];
            }

            $aTypes = $arr_feed;
            $aTypes['All'] = _p('foxfeedspro.all');

            $aFilters = array(
                'title' => array(
                    'type' => 'input:text',
                    'search' => "ni.item_title LIKE '%[VALUE]%'"
                ),                        
                'type' => array(
                    'type' => 'select',
                    'options' => $aTypes,
                    'default' => _p('foxfeedspro.all'),
                    'search' =>"ni.feed_id = [VALUE]"
                ),
                'approval' =>array(
                    'type' =>'select',
                    'options' =>array(
                        '0' =>'Unapproval',
                        '1' =>'Approval',
                        'All'=> _p('foxfeedspro.all')
                    ),
                    'default' => "0",
                    'search' =>"ni.is_approved = [VALUE]"
                ),
            );
            $oSearch = Phpfox::getLib('search')->set(array(
                'type' => 'ynnews_items',
                'filters' => $aFilters,
                'search' => 'search'
            )
            );
             $iPage = $this->request()->getInt('page');
            if ($this->request()->get('search-id')!='')
            {
                $current_feed = _p('foxfeedspro.all');
                $this->template()->assign(array('is_search'=>'is_search','value_search'=>$this->request()->get('search-id')));
                $_SESSION['current_feed'] = $current_feed;
                $_SESSION['page'] = $iPage;            
            }

            if (isset( $_SESSION['current_feed']))
                $current_feed = $_SESSION['current_feed'];

            if ($this->request()->get('deleteselect')=='Delete selected')
            {

                $arr_select = $this->request()->get('arr_selected');


                $arr_select = substr($arr_select,1);

                Phpfox::getLib('phpfox.database')->delete(Phpfox::getT('ynnews_items'), 'item_id IN ('.$arr_select.')');
                $current_feed = $this->request()->get('feed_selected');  
                $_SESSION['current_feed'] = $current_feed;
            }


            $_SESSION['page'] = $iPage;   
            $iPageSize = $oSearch->getDisplay()+12;
            $arr_filter=$oSearch->getConditions();  
            if (!is_numeric(isset($current_feed)))
            {
                 
                $arr_filter=$oSearch->getConditions();
                $arrSearch = array();
                foreach($arr_filter as $a=>$k)
                {
                    $test = explode('=',$k);
                    if(count($test)>=2 && $test[1]!=" " )
                    {
                        $arrSearch[] = $k;
                    }
                    $test = explode('LIKE',$k);
                    if(count($test)>=2 && $test[1]!=" " )
                    {
                        $arrSearch[] = $k;
                    }
                }
                //if(count($arrSearch) ==0)
                //{
                    $arrSearch[] ='ni.is_approved = 0 ';
                //}
                //$arrSearch[] = "ni.owner_type !='admin' " ;
                list($iCnt, $items) = Phpfox::getService('foxfeedspro')->getItems($arrSearch,"ni.item_pubDate_parse desc,ni.added_time desc", $oSearch->getPage(), $iPageSize);
                $current_feed ='All';
            }
            else
            {
                $arr_filter=$oSearch->getConditions();     
                foreach($arr_filter as $a=>$k)
                {
                    $test = explode('=',$k);
                    if(count($test)>=2 && $test[1]!=" " )
                    {
                        $arrSearch[] = $k;
                    }
                }
                $arrSearch = array();
                $arrSearch[] = " nf.feed_id = ".$current_feed;
                $arrSearch[] = "owner_type !='admin' " ;
                list($iCnt, $items) = Phpfox::getService('foxfeedspro')->getItems($arrSearch,"ni.item_pubDate_parse desc,ni.added_time desc", $iPage, $iPageSize);
            }
			foreach ($items as $key => $itemObj) 
			{
				$feed_approved = 0;
				if (isset($feeds_approval_status[$itemObj["feed_id"]]) && $feeds_approval_status[$itemObj["feed_id"]]>0) 
				{
					$feed_approved = 1;
				}
				$items[$key]["feed_approved"] = $feed_approved;
			}
            Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $oSearch->getSearchTotal($iCnt))); 
            $this->template()->assign(array('items'=>$items,'iCnt'=>$iCnt,'iPage'=>$iPage,'aRows'=>$items,'current_feed'=>$current_feed,'feeds_approval_status'=>$feeds_approval_status))
                    ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'news.js' =>'module_foxfeedspro'

            ));
            
            $this->template()->setBreadCrumb(_p('foxfeedspro.pending_news'), $this->url()->makeUrl('admincp.foxfeedspro.approval'));
        }


    }

?>