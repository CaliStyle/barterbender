<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class gettingstarted_component_block_latestarticles extends Phpfox_Component{
    public function process()
    {
        //define limit articles on block
        $iLimit = 10;
		if(PHPFOX::getParam('gettingstarted.number_of_limit_latest_articles')>0)
        {
           $iLimit = PHPFOX::getParam('gettingstarted.number_of_limit_latest_articles');
        }
        //get top new articles by date created
        $article_block=Phpfox::getService("gettingstarted.articlecategory")->getArticleLastest($iLimit);
        if(count($article_block) < 1)
        {
        	return false;
        }
        //get viewing article id to active
        if($this->request()->getInt('article'))
        {
            $art = $this->request()->getInt('article');
        }
        else
        {
            $art = -2;
        }
            
        
       
        $this->template()->assign(array(
           'sHeader' => _p('gettingstarted.latest_articles'),
           'article_block' => $article_block,           
            'art' => $art,
        ));

        return 'block';
        
    }
}
?>
