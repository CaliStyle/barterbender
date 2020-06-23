<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */
defined('PHPFOX') or exit('NO DICE!');

class gettingstarted_Component_Ajax_Ajax extends Phpfox_Ajax {

    public function updateScheduledActivity() {

        if (Phpfox::getService('gettingstarted.process')->updateActivity($this->get('id'), $this->get('active'))) {
            
        }
    }

    public function loadCategory() {
        $category = $this->get('category');
        $aRow_category = Phpfox::getService("gettingstarted")->getAllCategoryMailId($category);
        if (count($aRow_category) > 0) {
            $des = _p($aRow_category['description']);
            $this->html('#div_settings_category', $des);
        }
        $this->html('#loading', '');
    }

    public function viewTodoList() {
        Phpfox::getBlock("gettingstarted.todolist");
    }

    public function viewNextTodoList() {
        $order = $this->get("order");
        $oTodolist = Phpfox::getService('gettingstarted.todolist');
        
        $FirstTodoList = $oTodolist->getFirstLinetodolist($order);
        $SecondTodoList = $oTodolist->getFirstLinetodolist($FirstTodoList['ordering']);
        $preTodoList = $oTodolist->getPreTodolist($FirstTodoList['ordering']);
        $aVals = array();
        $aVals['item_id'] = $FirstTodoList['ordering'];
        $aVals['user_id'] = phpfox::getUserId();
        
        $oTodolist->updatepositiontodolist($aVals);
        
        if (count($SecondTodoList) == 0) {
            $this->html('#nexttodolist', '');
            $done_html = '<a href="javascript:void(0);" onclick="doneTodoList(); return false;" ><button type="button" class="button btn btn-primary btn-sm" onclick="tb_remove();">' . _p('gettingstarted.done') . '</button></a>';
            $this->html('#donetodolist', $done_html);
            $this->html('#closetodolist', '');
        }
        if (count($preTodoList) != 0) {
            $pre_html = '<a href="javascript:void(0);" onclick="javascript:viewPreTodoList();return false;" ><button type="button" class="button btn btn-default btn-sm"><i class="ico ico-angle-left"></i></button></a>';
            $this->html('#pretodolist', $pre_html);
        }
        
        $this->call("$('#ordering').val(" . $FirstTodoList['ordering'] . ");");
        $this->html('#title_todolist', $FirstTodoList['title']);
		$oPhpfoxParseOutput = Phpfox::getLib('parse.bbcode');
		$description_parsed = $oPhpfoxParseOutput->parse($FirstTodoList['description_parsed']);
		
		$sTxt =$description_parsed;
		$sTxt = str_replace("\n\r\n\r", "", $sTxt);
		$sTxt = str_replace("\n\r", "", $sTxt);
		$sTxt = str_replace("\n", "<div class=\"newline\"></div>", $sTxt);
		$description_parsed = $sTxt;
		
        $this->html('#description_todolist', $description_parsed);
        $this->call('$("#description_todolist").removeClass("twa_built");$Core.loadInit();');
    }

    public function viewPreTodoList() {
        $order = $this->get("order");
        $oTodolist = Phpfox::getService('gettingstarted.todolist');
        
        $FirstTodoList = $oTodolist->getPreTodolist($order);
        $SecondTodoList = $oTodolist->getPreTodolist($FirstTodoList['ordering']);
        $nextTodoList = $oTodolist->getFirstLinetodolist($FirstTodoList['ordering']);
        $aVals = array();
        $aVals['item_id'] = $FirstTodoList['ordering'];
        $aVals['user_id'] = phpfox::getUserId();
        
        $oTodolist->updatepositiontodolist($aVals);
        if (count($SecondTodoList) == 0) {
            $this->html('#pretodolist', '');
        }
        if (count($nextTodoList) != 0) {
            $html = '<button type="button" class="button btn btn-default btn-sm"  onclick="tb_remove();">' . _p('gettingstarted.close') . '</button>';
            $this->html('#closetodolist', $html);
            $this->html('#donetodolist', '');
            $next_html = '<a href="javascript:void(0);" onclick="viewNextTodoList(); return false;" ><button type="button" class="button btn btn-default btn-sm"><i class="ico ico-angle-right"></i></button></a>';
            $this->html('#nexttodolist', $next_html);
        }
        
        $this->call("$('#ordering').val(" . $FirstTodoList['ordering'] . ");");
        $this->html('#title_todolist', $FirstTodoList['title']);
		
		$oPhpfoxParseOutput = Phpfox::getLib('parse.bbcode');
		$description_parsed = $oPhpfoxParseOutput->parse($FirstTodoList['description_parsed']);
		
		$sTxt =$description_parsed;
		$sTxt = str_replace("\n\r\n\r", "", $sTxt);
		$sTxt = str_replace("\n\r", "", $sTxt);
		$sTxt = str_replace("\n", "<div class=\"newline\"></div>", $sTxt);
		$description_parsed = $sTxt;

        $this->html('#description_todolist',  ($description_parsed) );
        $this->call('$("#description_todolist").removeClass("twa_built");$Core.loadInit();');
    }

    public function doneTodoList() {
        $active = 0;
        Phpfox::getService("gettingstarted.todolist")->updatepositionactive($active, Phpfox::getUserId());
    }

    public function updateCheckboxTodolist() {
        $active = $this->get('active');
        Phpfox::getService("gettingstarted.todolist")->updatepositionactive($active, Phpfox::getUserId());
    }

    public function getToDoListAlert() {
        $todolist = "tb_show('" . _p('gettingstarted.todo_list') . "',$.ajaxBox('gettingstarted.viewTodoList','height=400&width=600' + '&no_remove_box=true'));";
        $alogin = Phpfox::getService("gettingstarted")->getLastestTypeId(phpfox::getUserId(), "login");
        $kq = 1;
        if (count($alogin) > 0) {
            $time_login = $alogin['time_stamp'];
            $PositionTodoList = Phpfox::getService("gettingstarted.todolist")->getPosiontodolist(Phpfox::getUserId());
            if (count($PositionTodoList) > 0) {

                if ($PositionTodoList['active'] == 0) {
                    $kq = 0;
                    exit;
                }

                $currentTodoList = Phpfox::getService('gettingstarted.todolist')->getCurrentPositionOfUser(Phpfox::getUserId());
                if (count($currentTodoList) == 0) {
                    //	$kq=0;
                    //	exit;
                }

                if ($time_login > $PositionTodoList['time_stamp']) {
                    $kq = 1;
                }
                else
                    $kq = 0;
            }
            else {
                $kq = 1;
                $FirstTodoList = Phpfox::getService('gettingstarted.todolist')->getFirstLinetodolist(0);
                if (count($FirstTodoList) == 0) {
                    $kq = 0;
                    exit;
                }
            }
        }
        if (Phpfox::isUser() == 1 && Phpfox::isAdmin() == 0 && $kq == 1) {

		    echo $todolist;
        
		} 
			
		
    }

    //get article information for edit support multiple languages
    public function getArticleCategoryByLanguage() {
        $art_for_edit['article_category_id'] = '';
        $language_id = $this->get('language_id');
        $art_id = $this->get('art_id');
        $cat_id = $this->get('cat_id');
        //check article existed to know update or insert new
        if (isset($language_id) && isset($art_id) && $language_id != '' && $art_id != '') {
            $isExist = PHPFOX::getService('gettingstarted.articlecategory')->isExistArticleByLanguage($art_id, $language_id);
        }
        //get article information to edit
        if (isset($isExist) && $isExist) {
            $art_for_edit = PHPFOX::getService('gettingstarted.articlecategory')->getArticleForEdit($art_id, $language_id);

            $sTitle = html_entity_decode($art_for_edit['title'], ENT_QUOTES, 'UTF-8');
            $sTitle = str_replace("'", '"', $sTitle);
            $sDescription = html_entity_decode($art_for_edit['description'], ENT_QUOTES, 'UTF-8');
            $sDescription = str_replace("'", '"', $sDescription);
            $this->call('$("#inner_title").val("' . $sTitle . '");');
            $this->call("$('textarea#description').val('" . $sDescription . "');");

            $inner_feature = ' <select class="form-control" name="val[is_featured]" >';
            if ($art_for_edit['is_featured'] == 0) {
                $inner_feature = $inner_feature . '<option value="0" selected >' . _p("gettingstarted.unfeatured") . '</option>';
                $inner_feature = $inner_feature . ' <option value="1" >' . _p('gettingstarted.featured') . '</option>';
            } else {
                $inner_feature = $inner_feature . '<option value="0" >' . _p("gettingstarted.unfeatured") . '</option>';
                $inner_feature = $inner_feature . ' <option value="1" selected  >' . _p('gettingstarted.featured') . '</option>';
            }
            $inner_feature = $inner_feature . '</select>
                                               <span id="loading"></span>';
            $this->html('#is_featured', $inner_feature);
        }
        //reset input to insert
        else {
            $this->call("$('#inner_title').val('');");
            $this->call('$("textarea#description").val("");');
            $inner_feature = ' <select  class="form-control" name="val[is_featured]" >';
            $inner_feature = $inner_feature . '<option value="0" selected >' . _p("gettingstarted.unfeatured") . '</option>';
            $inner_feature = $inner_feature . ' <option value="1" >' . _p('gettingstarted.featured') . '</option>';
            $inner_feature = $inner_feature . '</select>
                                               <span id="loading"></span>';
            $this->html('#is_featured', $inner_feature);
        }


        //get categories by language
        $aRows = phpfox::getLib('database')->select('*')
                ->from(phpfox::getT('gettingstarted_article_category'))
                ->where("language_id ='" . $language_id . "'")
                ->execute('getSlaveRows');
        //define layout
        $inner_html = '<div class="table_left">
						<span class="required">*</span>';
        $inner_html = $inner_html . _p("gettingstarted.category");
        $inner_html = $inner_html . '</div>
                             <div class="table_right">
                                <select  id="article_category_id" name="val[article_category_id]">';
        foreach ($aRows as $aRow) {
            if ($art_for_edit['article_category_id'] == '') {

                if ($aRow['article_category_id'] == $cat_id) {
                    $inner_html = $inner_html . '<option value="' . $aRow['article_category_id'] . '" selected>' . $aRow['article_category_name'] . '</option>';
                } else {
                    $inner_html = $inner_html . '<option value="' . $aRow['article_category_id'] . '" {if $aScheduledMail.article_category_id==$cats.article_category_id}selected{/if}>' . $aRow['article_category_name'] . '</option>';
                }
            } else {

                if ($aRow['article_category_id'] == $art_for_edit['article_category_id']) {
                    $inner_html = $inner_html . '<option value="' . $aRow['article_category_id'] . '" selected>' . $aRow['article_category_name'] . '</option>';
                } else {
                    $inner_html = $inner_html . '<option value="' . $aRow['article_category_id'] . '" {if $aScheduledMail.article_category_id==$cats.article_category_id}selected{/if}>' . $aRow['article_category_name'] . '</option>';
                }
            }
        }
        $inner_html = $inner_html . '</select>
                             <span id="loading"></span>
                            </div>
                            <div class="clear"></div>
                            
';

        $this->html('#inner_categories', $inner_html);
        $this->call('$("#language_hidden_id").val("' . $language_id . '");');
    }

    public function getTodolistForEdit() {
        $iTodolist_id = $this->get('todolist_id');
        $sLanguage_id = $this->get('language_id');

        if (isset($iTodolist_id) && $iTodolist_id != '' && isset($sLanguage_id) && $sLanguage_id != '') {
            $is_Existed = PHPFOX::getService('gettingstarted.todolist')->isExistedTodolist($iTodolist_id, $sLanguage_id);
        }
        if (isset($is_Existed) && $is_Existed) {

            $aTodolist = PHPFOX::getService('gettingstarted.todolist')->getTodolistForEdit($iTodolist_id, $sLanguage_id);

            $sTitle = html_entity_decode($aTodolist['title'], ENT_QUOTES, 'UTF-8');
            $sTitle = str_replace("'", '"', $sTitle);
            $sDescription = html_entity_decode($aTodolist['description'], ENT_QUOTES, 'UTF-8');
            $sDescription = str_replace("'", '"', $sDescription);
            $this->call('$("#title_inner").val("' . $sTitle . '");');
            $this->call("$('textarea#description').val('" . $sDescription . "');");
        } else {
            $this->call("$('#title_inner').val('');");
            $this->call('$("textarea#description").val("");');
        }
    }
    
    /************************************************************************************/
    /* ================================ version 3.02p5 ================================ */
    /************************************************************************************/
    
	public function setOrder()
	{
		$aVals = $this->get('val');
		Phpfox::getService('gettingstarted.todolist')->setOrder($aVals['ordering']);
	}

    public function getCatSelectByLanguage() {
        $sLanguage_id = $this->get('language_id');
        $name = $this->get('name');
        $selected = $this->get('selected') ? $this->get('selected') : null;
        $reduced = $this->get('reduced') ? $this->get('reduced') : null;
        
        $css = array('id'=>'', 'name'=>$name, 'class'=>'');
        
        $inner_html = "\n".Phpfox::getService('gettingstarted.multicat')->getSelectBox($css, $selected, $reduced, $sLanguage_id);
        
        $inner_html .= "\n".'<span id="loading"></span>'."\n";
        
        $this->html('#categories_bylanguage', $inner_html);
    }
    
    public function getCategoriesByLanguage() {
        $sLanguage_id = $this->get('language_id');
        $inner_html = "\n".Phpfox::getService('gettingstarted.articlecategory')->getCatForManage($sLanguage_id);
        $this->html('#categories_bylanguage', $inner_html);
    }

    public function getCategoryForSearchArticle() {
        $aTypes = array();
        $sLanguage_id = $this->get('language_id');
        
        $aTypes = Phpfox::getService('gettingstarted.articlecategory')->getCategoryForSearchArticle($sLanguage_id);

        $inner_html = "\n\t".'<select class="form-control" name="search[type]">';
        
        foreach($aTypes as $key=>$value) {
           $inner_html .= "\n\t\t".'<option value="'.$key.'">'.$value.'</option>';
        }
        
        $inner_html .= "\n\t".'</select>';
        
        $_SESSION['aTypes'] = $aTypes;
        $this->html('#categories_bylanguage', $inner_html);
    }
}

?>
