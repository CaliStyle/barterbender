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

class FoxFeedsPro_Component_Controller_Details extends Phpfox_Component
{

	public function process()
	{
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsProfile = true;
			$aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}
		else
		{
			$bIsProfile = $this->getParam('bIsProfile');
			if ($bIsProfile === true)
			{
				$aUser = $this->getParam('aUser');
			}
		}


		$bIsFavorite = true;
		$aFavorite = null;
		$bIsAddNews = phpfox::getUserParam('foxfeedspro.allow_users_to_add_article');
		$bIsAddFeed = phpfox::getUserParam('foxfeedspro.allow_users_to_add_feed');
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aFilterMenu = array(
			_p('foxfeedspro.browse_all')  => '',
			_p('foxfeedspro.browse_all_by_recent_news') => 'date',
			_p('foxfeedspro.browse_all_by_most_viewed') => 'most-view',
			_p('foxfeedspro.browsed_by_most_discussed') => 'most-comment',
			_p('foxfeedspro.browse_all_by_most_favorited') => 'most-favorite',
			true,
			_p('foxfeedspro.my_rss_provider') => 'foxfeedspro.feeds',
			_p('foxfeedspro.my_news') => 'foxfeedspro.news',
			_p('foxfeedspro.my_favorite_news')=> 'favourite',
			);
		}
		$this->template()->buildSectionMenu('foxfeedspro', $aFilterMenu);
		$friendly_url = phpfox::getLib('phpfox.database')->select('*')
		->from(Phpfox::getT('ynnews_settings'))
		->where('setting_type = "friendly_url"')
		->execute('getRow');
		$is_friendly_url = 0;
		if(!$friendly_url)
		{
			$is_friendly_url = 0;
		}
		else
		{
			$is_friendly_url = $friendly_url['param_values'];
		}
		$this->template()->assign(array('is_friendly_url'=>$is_friendly_url));
		$language_id = phpfox::getUserBy('language_id');

		$display_popup = phpfox::getLib('phpfox.database')->select('*')
		->from(Phpfox::getT('ynnews_settings'))
		->where('setting_type = "is_display_popup"')
		->execute('getRow');
		if(!$display_popup)
		{
			$is_display_popup = 0;
		}
		else
		{
			$is_display_popup = $display_popup['param_values'];
		}
		$this->template()->assign(array('is_display_popup'=>$is_display_popup)) ;

		// news item popup
		$display_popup_item = phpfox::getLib('phpfox.database')->select('*')
		->from(Phpfox::getT('ynnews_settings'))
		->where('setting_type = "is_display_popup_item"')
		->execute('getRow');
		if(!$display_popup_item)
		{
			$is_display_popup_item = 0;
		}
		else
		{
			$is_display_popup_item = $display_popup_item['param_values'];
		}

		$this->template()->assign(array('is_display_popup_item'=>$is_display_popup_item)) ;

		if($language_id == null)
		{
			$language_id = phpfox::getLib('phpfox.database')->select('language_id')
			->from(phpfox::getT('language'))
			->where('is_master = 1')
			->execute('getSlaveField');
		}

		$feeds = phpfox::getLib('phpfox.database')->select('*')
		->from(Phpfox::getT('ynnews_feeds'))
		->where('is_active = 1 AND (feed_language ="any" OR feed_language = "'.$language_id.'")')
		->execute('getRows');
		$arr_feed = array();
		foreach($feeds as $feed)
		{
			$arr_feed[$feed['feed_id']]=$feed['feed_name'] ;
		}

		$item_id = $this->request()->get('item');
		$feed_id = $this->request()->get('feed');
		$iPage = $this->request()->get('page');
		$req3 = $this->request()->get('req3');
		$req4 = $this->request()->get('req4');

		if ($req3 == 'feed' && $req4 !="" && $is_friendly_url == 1)
		{
			$req4 = urldecode($req4);
			$feed = phpfox::getService('foxfeedspro')->getFeedByNameAlias($req4);
			if($feed != null)
			{
				$feed_id = $feed['feed_id'];
			}
		}
		if ($req3 == 'item' && $req4 !="" && $is_friendly_url == 1)
		{
			$req4 = urldecode($req4);
			$item = phpfox::getService('foxfeedspro')->getItemByNameAlias($req4);
			if($item != null)
			{
				$item_id = $item['item_id'];
			}
		}

			
		if ($item_id)
		{
			//$arrSearch = $oSearch->getConditions();
			$iPageSize = 15;
			$arrSearch = array();
			$arrSearch[] = " ni.item_id  = ".$item_id;
			$arrSearch[] = "(nf.feed_language = 'any'  OR nf.feed_language = '".$language_id."')";


			Phpfox::getService('foxfeedspro')->updateViewCount($item_id);

			list($iCnt, $items) = Phpfox::getService('foxfeedspro')->getItems($arrSearch,"",$iPage, $iPageSize,true);
			if(!phpfox::isAdmin())
			{
				if((phpfox::getUserId() != $items[0]['owner_id'])&&($items[0]['is_active'] == 0 || $items[0]['is_approved'] == 0)){
					$this->url()->send('subscribe');
				}
			}
			if(empty($items))
			{
				return Phpfox_Error::display(_p('foxfeedspro.news_not_found'));
			}
			if (Phpfox::isUser() && Phpfox::isModule('notification'))
			{
				Phpfox::getService('notification.process')->delete('comment_foxfeedspro', $item_id, Phpfox::getUserId());
				Phpfox::getService('notification.process')->delete('foxfeedspro_like', $item_id, Phpfox::getUserId());
			}
			foreach($items as $key=>$its)
			{
				$items[$key]['link_view'] = phpfox::getLib('url')->makeUrl('foxfeedspro.details',array('item'=>$its['item_id']));
				$items[$key]['item_alias'] = phpfox::getLib('url')->cleanTitle($items[$key]['item_alias']);
			}

			Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
			$this->template()->assign(array('display_content'=>true,'items'=>$items,'iCnt'=>$iCnt,'iPage'=>$iPage,'aRows'=>$items))
			->setHeader('cache', array(
                                         'pager.css' => 'style_css',

			));;
			$this->template()->assign(array('feeds_sum'=>$items,'type_view'=>'item_details'));
			//Get more info
			$arrSearch = array();
			$arrSearch[] = " ni.item_id  < ".$item_id;
			$arrSearch[] = " ni.is_active LIKE 1 ";

			if(count($items)>0)
			$arrSearch[] = " ni.feed_id = ".$items[0]['feed_id'];

			list($iCnts1, $itemsnewer) = Phpfox::getService('foxfeedspro')->getItems($arrSearch,"",0, 5,true);

			$arrSearch = array();
			$arrSearch[] = " ni.item_id  != ".$item_id;
			$arrSearch[] = " ni.is_active LIKE 1 ";
			$arrSearch[] = " ni.is_approved = 1 ";
			$sCond = phpfox::getService('foxfeedspro')->getAvailNewsQuery();
			if(!empty($sCond))
			{
				$arrSearch[] = " ".$sCond." ";
			}
			else
			{
				$arrSearch[] = " ni.feed_id IN (0) ";
			}
			if(count($items)>0)
			{
				$arrSearch[] = " ni.feed_id = ".$items[0]['feed_id'];
			}
			$number_related_news = phpfox::getLib('database')->select('param_values')
								->from(phpfox::getT('ynnews_settings'))
								->where('setting_type="number_related_news"')
								->execute('getSlaveField');
			
			if(!isset($number_related_news) || ($number_related_news == ''))
			{
				$number_related_news = 10;
			}
			if($number_related_news == 0)
			{
				$number_related_news = '';
			}
			list($iCnts2, $itemsolder) = Phpfox::getService('foxfeedspro')->getItems($arrSearch, "", 0, $number_related_news, true);
			
			if(!empty($itemsolder))
			{
				foreach($itemsolder as $iKey => $aValue)
				{
					$itemsolder[$iKey]['item_alias'] = phpfox::getLib('url')->cleanTitle($itemsolder[$iKey]['item_alias']);
				}
			}
			$this->template()->assign(array('itemsnewer'=>$itemsolder, 'itemsolder'=>$itemsolder, 'iCountViewAll'=>$iCnts2));
			$title = str_replace('�','',isset($items['item_alias']));
			if(count($items)>0)
			{
				$mytitle = str_replace('-',' ',$items[0]['item_alias']);
			}
			else
			{
				$mytitle = "";
			}

			//$mytitle = mb_convert_case($mytitle, MB_CASE_TITLE, "UTF-8");
			//$mytitle = strtoupper($mytitle);
			$mytitle = $items[0]['item_title'];
			$this->template()->setTitle($mytitle);
			// end of changes

			$this->template()->setMeta('keywords', $this->template()->getKeywords(isset($aItem['title'])));
			if (!empty($items[0]['item_alias']))
			{
				$keylist = str_replace('-',',',$items[0]['item_alias']);
				$this->template()->setMeta('keywords', $keylist);
			}
			if(count($items)>0)
			$key = strtolower(str_replace(' ',',',$items[0]['item_description']));
			else
			$key="";
			$non_tag_word = array(
								"a",
								"able",
								"about",
								"above",
								"abroad",
								"according",
								"accordingly",
								"across",
								"actually",
								"adj",
								"after",
								"afterwards",
								"again",
								"against",
								"ago",
								"ahead",
								"ain't",
								"all",
								"allow",
								"allows",
								"almost",
								"alone",
								"along",
								"alongside",
								"already",
								"also",
								"although",
								"always",
								"am",
								"amid",
								"amidst",
								"among",
								"amongst",
								"an",
								"and",
								"another",
								"any",
								"anybody",
								"anyhow",
								"anyone",
								"anything",
								"anyway",
								"anyways",
								"anywhere",
								"apart",
								"appear",
								"appreciate",
								"appropriate",
								"are",
								"aren't",
								"around",
								"as",
								"a's",
								"aside",
								"ask",
								"asking",
								"associated",
								"at",
								"available",
								"away",
								"awfully",
								"b",
								"back",
								"backward",
								"backwards",
								"be",
								"became",
								"because",
								"become",
								"becomes",
								"becoming",
								"been",
								"before",
								"beforehand",
								"begin",
								"behind",
								"being",
								"believe",
								"below",
								"beside",
								"besides",
								"best",
								"better",
								"between",
								"beyond",
								"both",
								"brief",
								"but",
								"by",
								"c",
								"came",
								"can",
								"cannot",
								"cant",
								"can't",
								"caption",
								"cause",
								"causes",
								"certain",
								"certainly",
								"changes",
								"clearly",
								"c'mon",
								"co",
								"co.",
								"com",
								"come",
								"comes",
								"concerning",
								"consequently",
								"consider",
								"considering",
								"contain",
								"containing",
								"contains",
								"corresponding",
								"could",
								"couldn't",
								"course",
								"c's",
								"currently",
								"d",
								"dare",
								"daren't",
								"definitely",
								"described",
								"despite",
								"did",
								"didn't",
								"different",
								"directly",
								"do",
								"does",
								"doesn't",
								"doing",
								"done",
								"don't",
								"down",
								"downwards",
								"during",
								"e",
								"each",
								"edu",
								"eg",
								"eight",
								"eighty",
								"either",
								"else",
								"elsewhere",
								"end",
								"ending",
								"enough",
								"entirely",
								"especially",
								"et",
								"etc",
								"even",
								"ever",
								"evermore",
								"every",
								"everybody",
								"everyone",
								"everything",
								"everywhere",
								"ex",
								"exactly",
								"example",
								"except",
								"f",
								"fairly",
								"far",
								"farther",
								"few",
								"fewer",
								"fifth",
								"first",
								"five",
								"followed",
								"following",
								"follows",
								"for",
								"forever",
								"former",
								"formerly",
								"forth",
								"forward",
								"found",
								"four",
								"from",
								"further",
								"furthermore",
								"g",
								"get",
								"gets",
								"getting",
								"given",
								"gives",
								"go",
								"goes",
								"going",
								"gone",
								"got",
								"gotten",
								"greetings",
								"h",
								"had",
								"hadn't",
								"half",
								"happens",
								"hardly",
								"has",
								"hasn't",
								"have",
								"haven't",
								"having",
								"he",
								"he'd",
								"he'll",
								"hello",
								"help",
								"",
								"hence",
								"her",
								"here",
								"hereafter",
								"hereby",
								"herein",
								"here's",
								"hereupon",
								"hers",
								"herself",
								"he's",
								"hi",
								"him",
								"himself",
								"his",
								"hither",
								"hopefully",
								"how",
								"howbeit",
								"however",
								"hundred",
								"i",
								"i'd",
								"ie",
								"if",
								"ignored",
								"i'll",
								"i'm",
								"immediate",
								"in",
								"inasmuch",
								"inc",
								"inc.",
								"indeed",
								"indicate",
								"indicated",
								"indicates",
								"inner",
								"inside",
								"insofar",
								"instead",
								"into",
								"inward",
								"is",
								"isn't",
								"it",
								"it'd",
								"it'll",
								"its",
								"it's",
								"itself",
								"i've",
								"j",
								"just",
								"k",
								"keep",
								"keeps",
								"kept",
								"know",
								"known",
								"knows",
								"l",
								"last",
								"lately",
								"later",
								"latter",
								"latterly",
								"least",
								"less",
								"lest",
								"let",
								"let's",
								"like",
								"liked",
								"likely",
								"likewise",
								"little",
								"look",
								"looking",
								"looks",
								"low",
								"lower",
								"ltd",
								"m",
								"made",
								"mainly",
								"make",
								"makes",
								"many",
								"may",
								"maybe",
								"mayn't",
								"me",
								"mean",
								"meantime",
								"meanwhile",
								"merely",
								"might",
								"mightn't",
								"mine",
								"minus",
								"miss",
								"more",
								"moreover",
								"most",
								"mostly",
								"mr",
								"mrs",
								"much",
								"must",
								"mustn't",
								"my",
								"myself",
								"n",
								"name",
								"namely",
								"nd",
								"near",
								"nearly",
								"necessary",
								"need",
								"needn't",
								"needs",
								"neither",
								"never",
								"neverf",
								"neverless",
								"nevertheless",
								"new",
								"next",
								"nine",
								"ninety",
								"no",
								"nobody",
								"non",
								"none",
								"nonetheless",
								"noone",
								"no-one",
								"nor",
								"normally",
								"not",
								"nothing",
								"notwithstanding",
								"novel",
								"now",
								"nowhere",
								"o",
								"obviously",
								"of",
								"off",
								"often",
								"oh",
								"ok",
								"okay",
								"old",
								"on",
								"once",
								"one",
								"ones",
								"one's",
								"only",
								"onto",
								"opposite",
								"or",
								"other",
								"others",
								"otherwise",
								"ought",
								"oughtn't",
								"our",
								"ours",
								"ourselves",
								"out",
								"outside",
								"over",
								"overall",
								"own",
								"p",
								"particular",
								"particularly",
								"past",
								"per",
								"perhaps",
								"placed",
								"please",
								"plus",
								"possible",
								"presumably",
								"probably",
								"provided",
								"provides",
								"q",
								"que",
								"quite",
								"qv",
								"r",
								"rather",
								"rd",
								"re",
								"really",
								"reasonably",
								"recent",
								"recently",
								"regarding",
								"regardless",
								"regards",
								"relatively",
								"respectively",
								"right",
								"round",
								"s",
								"said",
								"same",
								"saw",
								"say",
								"saying",
								"says",
								"second",
								"secondly",
								"see",
								"seeing",
								"seem",
								"seemed",
								"seeming",
								"seems",
								"seen",
								"self",
								"selves",
								"sensible",
								"sent",
								"serious",
								"seriously",
								"seven",
								"several",
								"shall",
								"shan't",
								"she",
								"she'd",
								"she'll",
								"she's",
								"should",
								"shouldn't",
								"since",
								"six",
								"so",
								"some",
								"somebody",
								"someday",
								"somehow",
								"someone",
								"something",
								"sometime",
								"sometimes",
								"somewhat",
								"somewhere",
								"soon",
								"sorry",
								"specified",
								"specify",
								"specifying",
								"still",
								"sub",
								"such",
								"sup",
								"sure",
								"t",
								"take",
								"taken",
								"taking",
								"tell",
								"tends",
								"th",
								"than",
								"thank",
								"thanks",
								"thanx",
								"that",
								"that'll",
								"thats",
								"that's",
								"that've",
								"the",
								"their",
								"theirs",
								"them",
								"themselves",
								"then",
								"thence",
								"there",
								"thereafter",
								"thereby",
								"there'd",
								"therefore",
								"therein",
								"there'll",
								"there're",
								"theres",
								"there's",
								"thereupon",
								"there've",
								"these",
								"they",
								"they'd",
								"they'll",
								"they're",
								"they've",
								"thing",
								"things",
								"think",
								"third",
								"thirty",
								"this",
								"thorough",
								"thoroughly",
								"those",
								"though",
								"three",
								"through",
								"throughout",
								"thru",
								"thus",
								"till",
								"to",
								"together",
								"too",
								"took",
								"toward",
								"towards",
								"tried",
								"tries",
								"truly",
								"try",
								"trying",
								"t's",
								"twice",
								"two",
								"u",
								"un",
								"under",
								"underneath",
								"undoing",
								"unfortunately",
								"unless",
								"unlike",
								"unlikely",
								"until",
								"unto",
								"up",
								"upon",
								"upwards",
								"us",
								"use",
								"used",
								"useful",
								"uses",
								"using",
								"usually",
								"v",
								"value",
								"various",
								"versus",
								"very",
								"via",
								"viz",
								"vs",
								"w",
								"want",
								"wants",
								"was",
								"wasn't",
								"way",
								"we",
								"we'd",
								"welcome",
								"well",
								"we'll",
								"went",
								"were",
								"we're",
								"weren't",
								"we've",
								"what",
								"whatever",
								"what'll",
								"what's",
								"what've",
								"when",
								"whence",
								"whenever",
								"where",
								"whereafter",
								"whereas",
								"whereby",
								"wherein",
								"where's",
								"whereupon",
								"wherever",
								"whether",
								"which",
								"whichever",
								"while",
								"whilst",
								"whither",
								"who",
								"who'd",
								"whoever",
								"whole",
								"who'll",
								"whom",
								"whomever",
								"who's",
								"whose",
								"why",
								"will",
								"willing",
								"wish",
								"with",
								"within",
								"without",
								"wonder",
								"won't",
								"would",
								"wouldn't",
								"x",
								"y",
								"yes",
								"yet",
								"you",
								"you'd",
								"you'll",
								"your",
								"you're",
								"yours",
								"yourself",
								"yourselves",
								"you've",
								"z",
								"zero");

			$key = str_replace($non_tag_word,'',$key);
			$this->template()->setMeta('keywords',$key);
			
			if(count($items)>0)
			{
				$bIsFavorite = phpfox::getService('foxfeedspro')->checkNewsFavorite($items[0]['item_id']);
				$aFavorite = phpfox::getService('foxfeedspro')->checkIsFavoritedByUser($items[0]['item_id']);
				if(strlen($items[0]['item_description']) > 500)
				{
					$items[0]['item_description'] = substr($items[0]['item_description'], 0, 500);
					$pos = strrpos($items[0]['item_description'], " ");
					if($pos != 0)
					{
						$items[0]['item_description'] = substr($items[0]['item_description'], 0, $pos)."...";
					}
				}
				$meta_des = substr(strip_tags($items[0]['item_description']),0,150). '...';
				$mydescription = $mytitle . ' - ' .$meta_des.preg_replace('/((\w+\W*){10}(\w+))(.*)/', '${1}', $items[0]['item_description']) . '[...]';
			}
			else
			{
				$meta_des="";
				$mydescription="";
			}
			$this->template()->setMeta(array('description'=>$mydescription));
			// end of changes


		}
		elseif($feed_id)
		{
			$aTypes = $arr_feed;
			$aTypes['All'] = 'All';
			$aFilters = array(
				            'title' => array(
				            'type' => 'input:text',
				            'search' => "[VALUE]"
				            ),
				            'type' => array(
				            'type' => 'select',
				            'options' => $aTypes,
				            'default' => 'All',
				            'search' =>"ni.feed_id = [VALUE]"
				            )
				             
				            );
				            $oSearch = Phpfox::getLib('search')->set(array(
				            'type' => 'ynnews_items',
				            'search_tool' => array(
									'table_alias' => 'ni',
									'search' => array(
										'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('foxfeedspro.details.feed_'.$feed_id, 'view' => $this->request()->get('foxfeedspro'))) : $this->url()->makeUrl('foxfeedspro.details.feed_'.$feed_id, array('view' => $this->request()->get('view')))),
										'default_value' => _p('foxfeedspro.search_news_dot'),
										'name' => 'search',
										'field' => array('ni.item_title', 'ni.item_description')
				            ),
									'sort' => array(
										'latest' => array('ni.added_time', _p('foxfeedspro.latest')),
										'most-viewed' => array('ni.count_view', _p('foxfeedspro.most_viewed')),
										'most-favorited' => array('', _p('foxfeedspro.most_favorite')),
										'most-talked' => array('ni.total_comment', _p('foxfeedspro.most_discussed'))
				            ),
									'show' => array(5, 10, 15)
				            ),
				            //'filters' => $aFilters,
				            'search' => 'search'
				            )
				            );

				            $feed = phpfox::getService('foxfeedspro')->getFeed($feed_id);
				            if(isset($feed['is_approved']) && $feed['is_approved'] == 0 && $feed['owner_id']!=phpfox::getUserId() && !phpfox::isAdmin())
				            {
				            	$this->url()->send('subscribe');
				            }

				            $iPageSize = 5;
				            if($this->request()->get('show') != '')
				            {
				            	$iPageSize = $this->request()->get('show');
				            }
				            $sSort = 'item_pubDate DESC';
				            $sView = $this->request()->get('sort');
				            switch($sView)
				            {
				            	case 'most-viewed':
				            		$sSort = 'count_view desc';
				            		break;
				            	case 'most-talked':
				            		$sSort = 'total_comment desc';
				            		break;
				            	case 'most-favorited':
				            		$sSort = 'most-favorite';
				            		break;

				            }
				            $arrSearch = array();
				            $sKeyWord = $oSearch->getConditions();
				           // $sKeyWord = str_replace("AND", " ", $oSearch->getConditions());
				            $arrSearch = $sKeyWord;
				            $arrSearch[] = " ni.feed_id  = ".$feed_id;
				            $arrSearch[] = " ni.is_active LIKE 1 ";
				            $arrSearch[] = " ni.is_approved = 1 ";

				            list($iCnt, $items) = Phpfox::getService('foxfeedspro')->getItems($arrSearch,$sSort,$iPage, $iPageSize,true);

				            foreach($items as $key=>$its)
				            {
				            	$items[$key]['item_description'] = strip_tags($items[$key]['item_description']);
				            	if(strlen($items[$key]['item_description']) > 300)
				            	{
				            		$items[$key]['item_description'] = substr($items[$key]['item_description'], 0, 300);
				            		$pos = strrpos($items[$key]['item_description'], " ");
				            		if($pos != 0)
				            		{
				            			$items[$key]['item_description'] = substr($items[$key]['item_description'], 0, $pos)."...";
				            		}
				            		else
				            		{
				            			$items[$key]['item_description'] = $items[$key]['item_description']."...";
				            		}
				            	}
				            	$items[$key]['link_view'] = phpfox::getLib('url')->makeUrl('foxfeedspro.details',array('item'=>$its['item_id']));
				            	$items[$key]['item_alias'] = phpfox::getLib('url')->cleanTitle($items[$key]['item_alias']);
				            }

				            Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
				            $this->template()->assign(array('display_content'=>false,'items'=>$items,'iCnt'=>$iCnt,'iPage'=>$iPage,'aRows'=>$items))
				            ->setHeader('cache', array(
                                         'pager.css' => 'style_css',
				            ));
				            if(empty($items))
				            {
				            	$items = array();
				            }
				            $this->template()->assign(array(
                									'feeds_sum'=>$items,
                									'type_view'=>'feed_details',
				            ));
		}

		$this->template()->assign(array('sFormUrl'=>$this->url()->makeUrl('foxfeedspro')));
		if ($item_id)
		{
			$this->template()->setBreadcrumb(_p('foxfeedspro.news'),$this->url()->makeUrl('foxfeedspro'))->setTitle(_p('foxfeedspro.foxfeedspro')) ;
			$this->template()->setBreadCrumb('','',true);
		}
		else
		{
			$this->template()->setBreadcrumb(_p('foxfeedspro.news'),$this->url()->makeUrl('foxfeedspro'))->setTitle(_p('foxfeedspro.foxfeedspro')) ;
			$this->template()->setBreadCrumb('','',true);
		}
		$this->template()->setHeader(array(
						'news.js'      =>'module_foxfeedspro'
						)
						) ;
						if($item_id)
						{
							
							$item = phpfox::getService('foxfeedspro')->getItemById($item_id);
							if($is_friendly_url)
							{
								$sLink = phpfox::getLib('url')->makeUrl('foxfeedspro.details.item_'.$item['item_id'].'.'.phpfox::getLib('url')->cleanTitle($item['item_alias']));
							}
							else
							{
								$sLink = phpfox::getLib('url')->makeUrl('foxfeedspro.details.item_'.$item['item_id']);
							}
							$this->setParam('aFeed', array(
			                'comment_type_id' => 'foxfeedspro',
			                'privacy' => 0,
			                'comment_privacy' =>0,
			                'like_type_id' => 'foxfeedspro',
			                'feed_is_liked' => $item['is_liked'],
							//'feed_is_friend' => $aItem['is_friend'],
			                'item_id' => $item['item_id'],
			                'user_id' => $item['owner_id'],
			                'total_comment' => $item['total_comment'],
			                'total_like' => $item['total_like'],
			                'feed_link' => $sLink,
			                'feed_title' => $item['item_title'],
			                'feed_display' => 'view',
			                'feed_total_like' => $item['total_like'],
			                'report_module' => 'foxfeedspro',
			                'report_phrase' => _p('foxfeedspro.report_this_news'),
							// for adv share - nhanlt
			                'feed_image' => sprintf("<img src=\"%s\" />", $item['item_image']),
							// for adv share - nhanlt
			                'feed_content' => strip_tags($item['item_description']),
			                'time_stamp' => $item['item_pubDate_parse']
							)
							);
							$this->template()
							->setHeader('cache', array(
			                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
			                'jquery/plugin/jquery.scrollTo.js' => 'static_script',
			                'jquery/plugin/imgnotes/jquery.tag.js' => 'static_script',                        
			                'quick_edit.js' => 'static_script',
			                'pager.css' => 'style_css',
			                'feed.js' => 'module_feed',
			                'switch_legend.js' => 'static_script',
			                'switch_menu.js' => 'static_script',
			                'stream.css' => 'module_foxfeedspro',                    
			                'news.js'  =>'module_foxfeedspro',
			                'suppress_menu.css' => 'module_foxfeedspro',                       
							)
							);

						}

						if (Phpfox::getUserId())
						{
							$this->template()->setEditor(array(
                    'load' => 'simple',
                    'wysiwyg' => ((Phpfox::isModule('comment') && Phpfox::getParam('comment.wysiwyg_comments')) && Phpfox::getUserParam('comment.wysiwyg_on_comments'))
							)
							);
						}
						if (Phpfox::getParam('blog.digg_integration'))
						{
							$this->template()->setHeader('<script type="text/javascript">$(function() {var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0];s.type = \'text/javascript\';s.async = true;s.src = \'http://widgets.digg.com/buttons.js\';s1.parentNode.insertBefore(s, s1);});</script>');
						}

						if ($this->request()->get('req4') == 'comment' && $item_id)
						{
							$this->template()->setHeader('<script type="text/javascript">var $bScrollToBlogComment = false; $Behavior.scrollToBlogComment = function () { if ($bScrollToBlogComment) { return; } $bScrollToBlogComment = true; if ($(\'#js_feed_comment_pager_' . $item['item_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_pager_' . $item['item_id'] . '\', 800); } }</script>');
						}

						if ($this->request()->get('req4') == 'add-comment' && $item_id)
						{
							$this->template()->setHeader('<script type="text/javascript">var $bScrollToBlogComment = false; $Behavior.scrollToBlogComment = function () { if ($bScrollToBlogComment) { return; } $bScrollToBlogComment = true; if ($(\'#js_feed_comment_form_' . $item['item_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_form_' . $item['item_id']. '\', 800); $Core.commentFeedTextareaClick($(\'.js_comment_feed_textarea\')); $(\'.js_comment_feed_textarea\').focus(); } }</script>');
						}
						$core_url = Phpfox::getParam('core.path');
						$this->template()->assign(array(
														'core_url'=>$core_url,
														'bIsFavorite'=>$bIsFavorite,
														'aFavorite' => $aFavorite,
														'bIsAddNews'=>$bIsAddNews,
            											'bIsAddFeed'=>$bIsAddFeed
						))
						->setHeader('cache', array(
							'jquery/plugin/jquery.highlightFade.js' => 'static_script',
							'jquery/plugin/jquery.scrollTo.js' => 'static_script',
							'quick_edit.js' => 'static_script',
							'pager.css' => 'style_css',
							'feed.js' => 'module_feed'
							)
							);


	}

}

?>