<?php
class Gettingstarted_Component_Block_RelatedEntries extends Phpfox_Component
{
    public function process()
    {
        $iLimit = 10;    
		if(PHPFOX::getParam('gettingstarted.number_of_limit_related_articles')>0)
        {
           $iLimit = PHPFOX::getParam('gettingstarted.number_of_limit_related_articles');
        }
        if ($this->request()->get('article')) {
            $art = $this->request()->getInt('article');
            
            $article_block = PhpFox::getService('gettingstarted')->getRelatedEntries($art,$iLimit);
            if(empty($article_block))
            {
                return false;
            }
        } else {
          
            return false;
        }
        
        $this->template()->assign(array(
            'sHeader' => _p('gettingstarted.related_articles').":",
            'art' => $art,
            'article_block' => $article_block,
        ));


        return 'block';
    }
    
}

?>
