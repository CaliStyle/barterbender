<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class gettingstarted_component_block_FeaturedArticle extends Phpfox_Component{
    public function process()
    {
        //limit articles show on block
        $iLimit = 10;
		if(PHPFOX::getParam('gettingstarted.number_of_limit_featured_articles')>0)
        {
           $iLimit = PHPFOX::getParam('gettingstarted.number_of_limit_featured_articles');
        }
        //Get featured articles
        $article_block=Phpfox::getService("gettingstarted")->getArticleFeatured($iLimit);
        if(count($article_block) < 1)
        {
        	return false;
        }
        //Get article id current view to active 
        if($this->request()->getInt('article'))
        {
            $art = $this->request()->getInt('article');
        }
        else
        {
            $art = -2;
        }
            
        
       
        $this->template()->assign(array(
           'sHeader' => _p('gettingstarted.featured_articles'),
           'article_block' => $article_block,           
            'art' => $art,
        ));

        return 'block';
        
    }
}
?>
