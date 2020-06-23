<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/9/17
 * Time: 14:14
 */
namespace Apps\YNC_Affiliate\Installation\Data;

use Phpfox;
class YncAffiliatev401
{
    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }

    public function process()
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(\Phpfox::getT('setting'))
            ->where(['var_name' => 'ynaf_term_of_service_title'])
            ->executeField();
        if(!$iCnt)
        {
            $this->database()->insert(\Phpfox::getT('setting'), [
                'module_id'       => 'yncaffiliate',
                'product_id'      => 'YNC_Affiliate',
                'is_hidden'       => 1,
                'version_id'      => '4.5.1',
                'type_id'         => 'input:text',
                'var_name'        => 'ynaf_term_of_service_title',
                'phrase_var_name' => 'ynaf_term_of_service_title',
                'value_actual'    => '',
                'value_default'   => ''
            ]);
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(\Phpfox::getT('setting'))
            ->where(['var_name' => 'ynaf_term_of_service_content'])
            ->executeField();
        if(!$iCnt)
        {
            $this->database()->insert(\Phpfox::getT('setting'), [
                'module_id'       => 'yncaffiliate',
                'product_id'      => 'YNC_Affiliate',
                'is_hidden'       => 1,
                'version_id'      => '4.5.1',
                'type_id'         => 'input:text',
                'var_name'        => 'ynaf_term_of_service_content',
                'phrase_var_name' => 'ynaf_term_of_service_content',
                'value_actual'    => '',
                'value_default'   => ''
            ]);
        }
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(\Phpfox::getT('setting'))
            ->where(['var_name' => 'ynaf_points_conversion_rate'])
            ->executeField();
        if(!$iCnt)
        {
            $this->database()->insert(\Phpfox::getT('setting'), [
                'module_id'       => 'yncaffiliate',
                'product_id'      => 'YNC_Affiliate',
                'is_hidden'       => 1,
                'version_id'      => '4.5.1',
                'type_id'         => 'string',
                'var_name'        => 'ynaf_points_conversion_rate',
                'phrase_var_name' => 'ynaf_points_conversion_rate',
                'value_actual'    => '',
                'value_default'   => ''
            ]);
        }
        $iCnt = $this->database()->select('COUNT(*)')
                        ->from(Phpfox::getT('yncaffiliate_rules'))
                        ->execute('getSlaveField');
        if(!$iCnt){
            $this->database()->query("
                INSERT INTO `".Phpfox::getT('yncaffiliate_rules')."` (`rule_id`, `module_id`, `rule_title`,`rule_name`) VALUES
                    (1,'core','Membership Purchase','subscription'),
                    (2,'auction','Feature Auction','feature_auction'),
                    (3,'auction','Publish Auction','publish_auction'),
                    (4,'auction','Buy Auction','buy_auction'),
                    (5,'contest','Premium Contest','premium_contest'),
                    (6,'contest','Ending Soon Contest','ending_soon_contest'),
                    (7,'contest','Feature Contest','feature_contest'),
                    (8,'contest','Publish Contest','publish_contest'),
                    (9,'coupon','Feature Coupon','feature_coupon'),
                    (10,'coupon','Publish Coupon','publish_coupon'),
                    (11,'directory','Buy Business Package','buy_business_package'),
                    (12,'directory','Feature Business','feature_business'),
                    (13,'event','Sponsor Event','sponsor_event'),
                    (14,'fevent','Sponsor Advanced Event','sponsor_advanced_event'),
                    (15,'marketplace','Sponsor Marketplace','sponsor_marketplace'),
                    (16,'advancedmarketplace','Sponsor Advanced Marketplace','sponsor_advanced_marketplace'),
                    (17,'photo','Sponsor Photo','sponsor_photo'),
                    (18,'music','Sponsor Music Song','sponsor_music_song'),
                    (19,'music','Sponsor Music Album','sponsor_music_album'),
                    (20,'jobposting','Buy Job Package','buy_job_package'),
                    (21,'jobposting','Sponsor Company','sponsor_company'),
                    (22,'jobposting','Feature Job','feature_job'),
                    (23,'jobposting','Buy Apply Job Package','buy_apply_job_package'),
                    (24,'ynsocialstore','Buy Store Package','buy_store_package'),
                    (25,'ynsocialstore','Feature Store','feature_store'),
                    (26,'ynsocialstore','Feature Product','feature_product'),
                    (27,'ynsocialstore','Buy Product','buy_product'),
                    (28,'socialad','Buy Social Ad Package','buy_social_ad_package'),
                    (29,'donation','Donate In Donation','donate_donation'),
                    (30,'fundraising','Donate In Fundraising','donate_fundraising'),
                    (31,'forum','Sponsor Thread','sponsor_thread'),
                    (32,'v','Sponsor Video','sponsor_video');
                ");
            $iCnt = $this->database()->select('COUNT(*)')
                            ->from(Phpfox::getT('yncaffiliate_rulemaps'))
                            ->execute('getSlaveField');
            if(!$iCnt){
                $aUserGroups = $this->database()->select('ugroup.*')
                        ->from(Phpfox::getT('user_group'), 'ugroup')
                        ->order('user_group_id ASC')
                        ->execute('getSlaveRows');
                $aRuleMapId = [];
                if($aUserGroups){
                    $aRules = $this->database()->select('*')
                                    ->from(Phpfox::getT('yncaffiliate_rules'))
                                    ->execute('getRows');
                    foreach ($aUserGroups as $aUserGroup)
                    {
                        foreach ($aRules as $key => $aRule)
                        {
                            $aRuleMapId[] = $this->database()->insert(Phpfox::getT('yncaffiliate_rulemaps'),
                                                [
                                                    'rule_id' => $aRule['rule_id'],
                                                    'user_group_id' => $aUserGroup['user_group_id']
                                                ]);
                        }
                    }
                    $iCnt = $this->database()->select('COUNT(*)')
                        ->from(Phpfox::getT('yncaffiliate_rulemap_details'))
                        ->execute('getSlaveField');
                    if(!$iCnt)
                    {
                        foreach ($aRuleMapId as $iMapId)
                        {
                            for ($iLevel = 1; $iLevel <= 5; $iLevel++)
                            {
                                $this->database()->insert(Phpfox::getT('yncaffiliate_rulemap_details'),
                                    [
                                        'rulemap_id' => $iMapId,
                                        'rule_level' => $iLevel,
                                        'rule_value' => 0,
                                    ]);
                            }
                        }
                    }
                }
            }
        }
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(\Phpfox::getT('yncaffiliate_suggests'))
            ->execute('getSlaveField');
        if(!$iCnt)
        {
            $this->database()->query("
                INSERT INTO `".Phpfox::getT('yncaffiliate_suggests')."` (`suggest_id`,`is_active`,`module_id`, `suggest_title`, `href`) VALUES
                    (1,1,'core','Home Page','home'),
                    (2,1,'profile','Profile Page','profile'),
                    (3,1,'auction','Auction',''),
                    (4,1,'contest','Contest',''),
                    (5,1,'coupon','Coupon',''),
                    (6,1,'directory','Business Directory',''),
                    (7,1,'event','Event',''),
                    (8,1,'fevent','Advanced Event',''),
                    (9,1,'marketplace','Marketplace',''),
                    (10,1,'advancedmarketplace','Advanced Marketplace',''),
                    (11,1,'photo','Photo',''),
                    (12,1,'advancedphoto','Advanced Photo',''),
                    (13,1,'music','Music',''),
                    (14,1,'jobposting','Job Posting',''),
                    (15,1,'ynsocialstore','Social Store',''),
                    (16,1,'socialad','Social Ads',''),
                    (17,1,'petition','Petition',''),
                    (18,1,'fundraising','Fundraising','');
            ");
        }
    }
}