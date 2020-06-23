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

class Gettingstarted_Component_Controller_Admincp_settings extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
         $settings = phpfox::getService('gettingstarted.settings')->getSettings(0);

         if (isset($settings['number_of_article_category'])==null)
         {
            $number_of_article_category=5;
         }
         else
         {
             $number_of_article_category=$settings['number_of_article_category'];
         }
         if (isset($settings['number_of_article'])==null)
         {
            $number_of_article = 10;
         }
         else
         {
             $number_of_article=$settings['number_of_article'];
         }
         if (isset($settings['number_of_letters'])==null)
         {
            $number_of_letters = 100;
         }
         else
         {
             $number_of_letters = $settings['number_of_letters'];
         }
         if(isset($settings['active_getting_started']) == null)
         {
         	$active_getting_started = 1;
         }
         else 
         {
         	$active_getting_started = $settings['active_getting_started'];	
         }
         if(isset($settings['active_base_knowledge']) == null)
         {
         	$active_base_knowledge = 1;
         }
         else 
         {
         	$active_base_knowledge = $settings['active_base_knowledge'];
         }
         if(isset($settings['active_email_remainder']) == null)
         {
         	$active_email_remainder = 1;
         }
         else 
         {
         	$active_email_remainder = $settings['active_email_remainder'];
         }
          if (isset($settings['number_of_most_view_article'])==null)
         {
            $number_of_most_view_article=10;
         }
         else
         {
             $number_of_most_view_article=$settings['number_of_most_view_article'];
         }
        
         if ($this->request()->get('save_change_global_setings'))
         {
             
              $val = $this->request()->get('val');
              
              phpfox::getService('gettingstarted.settings')->setSettings($val, 0);
              $this->url()->send('current', null, 'Update Global settings successfully');
         }
          $this->template()->assign(
                            array(
                                'number_of_article_category' => $number_of_article_category,
                                'number_of_article' => $number_of_article,
                                'number_of_letters' => $number_of_letters,
                            	'active_email_remainder' => $active_email_remainder,
                            	'active_base_knowledge' => $active_base_knowledge,
                            	'active_getting_started' => $active_getting_started,
                                'number_of_most_view_article'=> $number_of_most_view_article
                                )
                            );
    }
}
?>
