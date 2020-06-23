<?php

class Document_Component_Ajax_Ajax extends Phpfox_Ajax  
{
	public function updateAllowDownload()
	{		
		Phpfox::getService('document.process')->updateAllowDownload($this->get('id'), $this->get('active'));
	}

	public function updateApprove()
	{		
		Phpfox::getService('document.process')->updateApprove($this->get('id'), $this->get('active'));
	}

	public function updateFeature()
	{		
		Phpfox::getService('document.process')->updateFeature($this->get('id'), $this->get('active'));
	}

    public function frontendUpdateApprove()
    {
		Phpfox::getService('document.process')->updateApprove($this->get('id'), $this->get('active'));
        $this->updateStatus($this->get('id'));
        if ($this->get('active') == 1)
        {
            $this->alert(_p('approve_successfully'),_p('pages.moderation'),300,150,true);

             //$this->call('$(\'#document_block_' . $this->get('id') . '\').remove();');

        }

        else
            $this->alert(_p('disapprove_successfully'),_p('pages.moderation'),300,150,true);

    }
    public  function frontendUpdateApproveRemove()
    {
        Phpfox::getService('document.process')->updateApprove($this->get('id'), $this->get('active'));
        $this->updateStatus($this->get('id'));
        if ($this->get('active') == 1)
        {
            $this->alert(_p('approve_successfully'),_p('pages.moderation'),300,150,true);

            $this->call('$(\'#document_block_' . $this->get('id') . '\').remove();');

        }
    }
    
    public function frontendUpdateFeature()
    {
		Phpfox::getService('document.process')->updateFeature($this->get('id'), $this->get('active'));
        $this->updateStatus($this->get('id'));
        if ($this->get('active') == 1)
            $this->alert(_p('feature_successfully'),_p('pages.moderation'),300,150,true);
        else
            $this->alert(_p('un_feature_successfully'),_p('pages.moderation'),300,150,true);


    }
    
    public function updateStatus($iId)
    {
        $aStatus = Phpfox::getService('document')->getStatus($iId);
        
        if($aStatus['is_approved'])
        {
            if($aStatus['is_featured'])
            {
                if($aStatus['is_new'])
                {
                    $this->showOne($iId, 'feature_new');
                }
                else
                {
                    $this->showOne($iId, 'feature');
                }
                $this->show('#js_unfeature_'.$iId);
            }
            else
            {
                if($aStatus['is_new'])
                {
                    $this->showOne($iId, 'new');
                }
                else
                {
                    $this->showOne($iId, '');
                }
                $this->show('#js_feature_'.$iId);
            }
            $this->show('#item_edit_'.$iId);
        }
        else
        {
            $this->showOne($iId, 'pending');
            $this->hide('#js_feature_'.$iId);
            $this->hide('#js_unfeature_'.$iId);
            $this->hide('#item_edit_'.$iId);
        }
    }
    
    public function showOne($iId, $sShow)
    {
        $aStatus = array(
            'feature_new' => 'feature_new_document_'.$iId,
            'feature' => 'feature_document_'.$iId,
            'new' => 'new_document_'.$iId,
            'pending' => 'pending_document_'.$iId
        );
        
        foreach($aStatus as $k=>$v)
        {
            if($k==$sShow)
            {
                $this->show('#'.$v);
            }
            else
            {
                $this->hide('#'.$v);
            }
        }
    }
    
    public function adminDelete()
    {
        if (Phpfox::getService('document.process')->delete($this->get('document_id')))
        {
            $this->hide('#jp_row_' . $this->get('document_id'));
            $this->hide('#public_message');
            $this->show('#document_public_message');
            $this->html('#document_public_message',_p('document_successfully_deleted'));
            
           // $this->setMessage(_p('document_successfully_deleted'));
        }    
    }
    public function popup()
    {        
        Phpfox::getBlock('document.email', array(
                'id' => $this->get('id'),
            )
        );
    }
    public function sendEmails()
    {

        if (Phpfox::getService('document.process')->sendWithAttachment($this->get('val')))
        {

            //$this->setMessage(_p('share.message_successfully_sent'));
            echo "$('.js_box_close a').trigger('click');";
            $this->alert(_p('share.message_successfully_sent'), _p('photo.notice'), 300, 100, true);
           //

        }else
        {
            $this->setMessage('Could not send this email');
        } 
    }
    public function delete()
    {
        
        if (Phpfox::getService('document.process')->delete($this->get('document_id')))
        {
            $this->alert(_p('document_successfully_deleted'), 'Moderation', 300, 150, true);
            $this->remove('#document_block_' . $this->get('document_id'));
            $this->call("rebuildDocument();");
        }    
    }
    public function approve()
    {
        if (Phpfox::getService('document.process')->updateApprove($this->get('document_id'), 1))
        {
            $this->alert(_p('document_has_been_approved'), _p('approved'), 300, 150, true);
            echo('$("#js_item_bar_approve_image").hide();');
            echo('window.location.reload()');
        }
    } 
    public function feature()
    {
        if (Phpfox::getService('document.process')->feature($this->get('document_id'),$this->get('type')))
        {
            
        }
    }
    public function moderation()
    {
        Phpfox::isUser(true);    
        
        switch ($this->get('action'))
        {
            case 'approve':
                Phpfox::getUserParam('document.can_approve_documents', true);
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('document.process')->updateApprove($iId, 1);
                    $this->call('$(\'#document_block_' . $iId . '\').remove();'); 
                    $this->updateCount();                   
                }                
              
                $sMessage = _p('document_successfully_approved');
                break;            
            case 'delete':
                Phpfox::getUserParam('document.can_delete_other_document', true);
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('document.process')->delete($iId);
                    $this->remove('#document_block_' . $iId);
                } 
                $sMessage = _p('document_successfully_deleted');
                break;
        }
        $this->alert($sMessage, 'Moderation', 300, 150, true);

        $this->hide('.moderation_process'); 
        $this->call("rebuildDocument();"); 
    }
    public function displayFeed()
    {
        $iDocumentId = $this->get('document_id');
		(($sPlugin = Phpfox_Plugin::get('document.service_process_feed__end')) ? eval($sPlugin) : false);
		Phpfox::getService('feed')->processAjax($this->get('id'));
    }
	
	public function migrate(){
		$oDBLib = PHPFOX::getLib("database");
		$this->call("$('#contener_percent').css({'width': '0%'}).text('0%');");
		
		PHPFOX::getService("document.process")->alterMissingColumn(PHPFOX::getT("document"), array(
			"image_url_updated_time" => 'INTEGER UNSIGNED NOT NULL AFTER `image_url`',
			"in_process" => " tinyint(4) NOT NULL default '0' AFTER `view_id` ",
			"privacy_comment" => " tinyint(4) NOT NULL default '0' AFTER `in_process` ",
			"total_like" => " int(10) unsigned NOT NULL default '0' AFTER `privacy_comment` ",
			"process_status" => " varchar(20) NULL default NULL AFTER `total_like` ",
			"page_count" => " int(10) NOT NULL default '0' AFTER `process_status` ",
		));
		$this->call("$('#contener_percent').css({'width': '10%'}).text('10%');");
		PHPFOX::getService("document.process")->updateTable(array(
			array(
				"table"=>PHPFOX::getT("document"),
				"vals"=> " `image_url_updated_time` = UNIX_TIMESTAMP() ",
				"cond"=> " `image_url_updated_time` = 0 "
			),
			array(
				"table"=>PHPFOX::getT("document"),
				"vals"=> " `process_status` = \"DONE\" ",
				"cond"=> " `process_status` = NULL "
			),
		));
		$this->call("$('#contener_percent').css({'width': '40%'}).text('40%');");
		
		$oDBLib->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('document_text') . "` (
			`document_id` int(10) NOT NULL,
			`text` mediumtext NOT NULL,
			`text_parsed` mediumtext NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 "
		);
		$this->call("$('#contener_percent').css({'width': '70%'}).text('70%');");
		PHPFOX::getService("document.process")->alterMissingColumn(PHPFOX::getT("user_activity"), array(
			"activity_document" => " INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' ",
		));
		PHPFOX::getService("document.process")->alterMissingColumn(PHPFOX::getT("user_field"), array(
			"total_document" => " INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' ",
		));
		
		$oDBLib->query(sprintf("UPDATE `%s` as `ufield` SET `total_document` = (SELECT COUNT(*) from `%s` as `doc` where `doc`.`user_id` = `ufield`.`user_id`);", PHPFOX::getT("user_field"), PHPFOX::getT("document")));
		$this->call("$('#contener_percent').css({'width': '80%'}).text('80%');");
		
		$documents = PHPFOX::getService("document.process")->getRawDocuments();
		foreach($documents as $aDoc) {
			$oDBLib->insert(PHPFOX::getT("document_text"), array(
				"document_id" => $aDoc["document_id"],
				"text" => $aDoc["description"],
				"text_parsed" => Phpfox::getLib('parse.input')->prepare($aDoc["description"]),
			));
		}
		
		/*solve all old block*/
		$oDBLib->query(sprintf("DELETE FROM `%s` WHERE `module_id` = \"document\";", PHPFOX::getT("block")));
		$oDBLib->query(sprintf("
			INSERT INTO `%s` (`title`,`type_id`,`m_connection`,`module_id`,`product_id`,`component`,`location`,`is_active`,`ordering`,`disallow_access`,`can_move`,`version_id`) VALUES
			 ('document_topview',0,'document.index','document','younet_document','topview','3',1,5,NULL,0,NULL),
			 ('document_statistic',0,'document.index','document','younet_document','statistic','3',1,4,NULL,0,NULL),
			 (NULL,0,'document.view','document','younet_document','detail','1',0,2,NULL,0,NULL),
			 (NULL,0,'document.view','document','younet_document','menu','3',0,1,NULL,0,NULL),
			 (NULL,0,'document.index','document','younet_document','filter','1',0,3,NULL,0,NULL),
			 (NULL,0,'document.index','document','younet_document','category','1',1,2,NULL,0,NULL),
			 (NULL,0,'document.add','document','younet_document','menu','3',0,1,NULL,0,NULL),
			 (NULL,0,'document.index','document','younet_document','menu','3',0,1,NULL,0,NULL),
			 (NULL,0,'document.index','document','younet_document','topusers','3',1,6,NULL,0,NULL);
		", PHPFOX::getT("block")));
		/*/solve all old block*/
		
		/*solve module info*/
		$oDBLib->query(sprintf("DELETE FROM `%s` WHERE `product_id` = \"younet_document\";", PHPFOX::getT("product")));
		$oDBLib->query(sprintf("
			INSERT INTO `%s` (`product_id`,`is_core`,`title`,`description`,`version`,`latest_version`,`last_check`,`is_active`,`url`,`url_version_check`) VALUES 
			 ('younet_document',0,'YouNet Document','by YouNet Company','3.01p2',NULL,0,1,NULL,NULL);
		", PHPFOX::getT("product")));
		
		$oDBLib->query(sprintf("DELETE FROM `%s` WHERE `product_id` = \"younet_document\";", PHPFOX::getT("product_dependency")));
		$oDBLib->query(sprintf("
			INSERT IGNORE INTO `%s`(`product_id`,`type_id`, `check_id`, `dependency_start`) VALUES
				('younet_document','product', 'younetcore', '3.01p2')
		", Phpfox::getT('product_dependency')));
		
		$oDBLib->query(sprintf("DELETE FROM `%s` WHERE `product_id` = \"younet_document\";", PHPFOX::getT("product_install")));
		$oDBLib->query("
			INSERT INTO `".Phpfox::getT('product_install')."` (`product_id`,`version`,`install_code`,`uninstall_code`) VALUES 
				('younet_document','3.01','\$this->database()->query(\"CREATE TABLE IF NOT EXISTS `\" . Phpfox::getT(\'document\') . \"` (\r\n  `document_id` int(10) unsigned NOT NULL auto_increment,\r\n  `document_file_name` varchar(255) NOT NULL,\r\n  `document_file_path` varchar(255) NOT NULL,\r\n  `doc_id` int(10) NOT NULL,\r\n  `access_key` varchar(45) NOT NULL,\r\n  `visibility` tinyint(4) NOT NULL,\r\n  `user_id` int(10) unsigned NOT NULL default \'0\',\r\n  `is_approved` tinyint(1) NOT NULL default \'0\',\r\n  `is_featured` tinyint(1) NOT NULL default \'0\',\r\n  `document_privacy` tinyint(4) NOT NULL,\r\n  `document_license` int(11) NOT NULL default \'0\',\r\n  `privacy` tinyint(1) NOT NULL default \'0\',\r\n  `module_id` varchar(75) default NULL,\r\n  `item_id` int(10) unsigned NOT NULL default \'0\',\r\n  `title` varchar(255) default NULL,\r\n  `title_url` varchar(255) default NULL,\r\n  `image_url` varchar(255) NOT NULL,\r\n  `image_url_updated_time` int(10) NOT NULL,\r\n  `description` mediumtext,\r\n  `allow_comment` tinyint(1) NOT NULL default \'0\',\r\n  `total_comment` int(10) unsigned NOT NULL default \'0\',\r\n  `total_score` decimal(4,2) NOT NULL default \'0.00\',\r\n  `total_rating` int(10) unsigned NOT NULL default \'0\',\r\n  `time_stamp` int(10) unsigned NOT NULL default \'0\',\r\n  `total_view` int(10) unsigned NOT NULL default \'0\',\r\n  `allow_rating` tinyint(4) NOT NULL,\r\n  `allow_download` tinyint(1) NOT NULL default \'0\',\r\n  `allow_attach` tinyint(1) NOT NULL default \'0\',\r\n  `view_id` tinyint(4) NOT NULL default \'0\',\r\n  `in_process` tinyint(4) NOT NULL default \'0\',\r\n  `privacy_comment` tinyint(4) NOT NULL default \'0\',\r\n  `total_like` int(10) unsigned NOT NULL,\r\n  `process_status` varchar(20) NOT NULL,\r\n  `page_count` int(10) NOT NULL default \'0\',\r\n  PRIMARY KEY  (`document_id`),\r\n  KEY `view_id` (`module_id`,`item_id`,`title_url`),\r\n  KEY `user_id` (`user_id`),\r\n  KEY `in_process` (`is_approved`),\r\n  KEY `document_id_2` (`document_id`,`is_approved`,`user_id`),\r\n  KEY `in_process_2` (`is_approved`,`module_id`,`item_id`,`user_id`),\r\n  KEY `in_process_3` (`is_approved`,`user_id`),\r\n  KEY `in_process_4` (`is_approved`,`module_id`,`item_id`),\r\n  KEY `in_process_5` (`is_approved`),\r\n  KEY `view_id_2` (`module_id`,`item_id`),\r\n  KEY `document_id` (`document_id`,`is_approved`,`title`),\r\n  KEY `document_id_3` (`document_id`,`is_approved`,`user_id`),\r\n  KEY `view_id_4` (`user_id`)\r\n) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 \");\r\n\r\n\$this->database()->query(\"CREATE TABLE IF NOT EXISTS `\" . Phpfox::getT(\'document_category\') . \"` (\r\n `category_id` mediumint(8) unsigned NOT NULL auto_increment,\r\n  `parent_id` mediumint(8) unsigned NOT NULL default \'0\',\r\n  `is_active` tinyint(1) NOT NULL default \'0\',\r\n  `name` varchar(255) NOT NULL,\r\n  `name_url` varchar(255) NOT NULL,\r\n  `time_stamp` int(10) unsigned NOT NULL default \'0\',\r\n  `used` int(10) unsigned NOT NULL default \'0\',\r\n  `ordering` int(11) unsigned NOT NULL default \'0\',\r\n  PRIMARY KEY  (`category_id`),\r\n  KEY `parent_id` (`parent_id`,`is_active`),\r\n  KEY `is_active` (`is_active`,`name_url`)\r\n) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 \");\r\n\r\n\$this->database()->query(\"INSERT IGNORE INTO `\" . Phpfox::getT(\'document_category\') . \"` (`category_id`, `parent_id`, `is_active`, `name`, `name_url`, `time_stamp`, `used`, `ordering`) VALUES\r\n(1, 0, 1, \'Autos & Vehicles\', \'\', 0, 0, 1),\r\n(2, 0, 1, \'Comedy\', \'\', 0, 0, 2),\r\n(3, 0, 1, \'Education\', \'\', 0, 0, 3),\r\n(4, 0, 1, \'Entertainment\', \'\', 0, 0, 4),\r\n(5, 0, 1, \'Film & Animation\', \'\', 0, 0, 5),\r\n(6, 0, 1, \'Gaming\', \'\', 0, 0, 6),\r\n(7, 0, 1, \'Howto & Style\', \'\', 0, 0, 7),\r\n(8, 0, 1, \'News & Politics\', \'\', 0, 0, 8),\r\n(9, 0, 1, \'Nonprofits & Activism\', \'\', 0, 0, 9),\r\n(10, 0, 1, \'People & Blogs\', \'\', 0, 0, 10),\r\n(11, 0, 1, \'Pets & Animals\', \'\', 0, 0, 11),\r\n(12, 0, 1, \'Science & Technology\', \'\', 0, 0, 12),\r\n(13, 0, 1, \'Sports\', \'\', 0, 0, 13),\r\n(14, 0, 1, \'Travel & Events\', \'\', 0, 0, 14) \");\r\n\r\n\$this->database()->query(\"CREATE TABLE IF NOT EXISTS `\" . Phpfox::getT(\'document_text\') . \"` (\r\n  `document_id` int(10) NOT NULL,\r\n  `text` mediumtext NOT NULL,\r\n  `text_parsed` mediumtext NOT NULL\r\n) ENGINE=MyISAM DEFAULT CHARSET=latin1 \");\r\n\r\n\$this->database()->query(\"CREATE TABLE IF NOT EXISTS `\" . Phpfox::getT(\'document_license\') . \"` (\r\n `license_id` int(11) NOT NULL AUTO_INCREMENT,\r\n  `license_name` varchar(255) NOT NULL,\r\n  `reference_url` varchar(255) DEFAULT NULL,\r\n  `image_url` varchar(255) NOT NULL,\r\n  `time_stamp` int(10) NOT NULL DEFAULT \'0\',\r\n  `used` tinyint(4) NOT NULL DEFAULT \'0\',\r\n  PRIMARY KEY (`license_id`)\r\n) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 \");\r\n\r\n\$this->database()->query(\"INSERT IGNORE INTO `\" . Phpfox::getT(\'document_license\') .\"` (`license_id`, `license_name`, `reference_url`, `image_url`, `time_stamp`, `used`) VALUES\r\n(1, \'Attribution CC BY\', \'http://creativecommons.org/licenses/by/3.0\', \'document/static/image/8b1b578f076907e35e5f5489bb6093bb_ccby.png\', 1312517885, 0),\r\n(2, \'Attribution-NoDerivs CC BY-ND\', \'http://creativecommons.org/licenses/by-nd/3.0\', \'document/static/image/78cfae3954e62bd26e87c8685681b601_ccbynd.png\', 1312517893, 0),\r\n(3, \'Attribution-NonCommercial CC BY-NC\', \'http://creativecommons.org/licenses/by-nc/3.0\', \'document/static/image/0e6b4e99d665e0841c4abdce1b2f5779_ccbync.png\', 1312517904, 0),\r\n(4, \'Attribution-ShareAlike CC BY-SA\', \'http://creativecommons.org/licenses/by-sa/3.0\', \'document/static/image/8bf75fbc3523d50108e37a4a965af649_ccbysa.png\', 1312517922, 0) \");\r\n\r\n\$this->database()->query(\"INSERT IGNORE INTO `\" . Phpfox::getT(\'block\') .\"` ( `title`, `type_id`, `m_connection`, `module_id`, `product_id`, `component`, `location`, `is_active`, `ordering`, `disallow_access`, `can_move`, `version_id`) VALUES\r\n(\'Profile Photo &amp; Menu\', 0, \'document.profile\', \'profile\', \'phpfox\', \'pic\', \'1\', 1, 1, NULL, 0, \'3\') \");\r\n\r\n\$this->database()->query(\"CREATE TABLE IF NOT EXISTS `\" . Phpfox::getT(\'document_category_data\') . \"` (\r\n  `document_id` int(10) unsigned NOT NULL,\r\n  `category_id` int(10) unsigned NOT NULL,\r\n  KEY `category_id` (`category_id`),\r\n  KEY `document_id` (`document_id`)\r\n) ENGINE=InnoDB DEFAULT CHARSET=latin1 \");\r\n\r\n\$this->database()->query(\"CREATE TABLE IF NOT EXISTS `\" . Phpfox::getT(\'document_embed\') . \"` (\r\n  `document_id` int(10) unsigned NOT NULL,\r\n  `document_url` varchar(255) NOT NULL,\r\n  `embed_code` mediumtext NOT NULL,\r\n  UNIQUE KEY `document_id` (`document_id`)\r\n) ENGINE=InnoDB DEFAULT CHARSET=latin1 \");\r\n\r\n\$this->database()->query(\"CREATE TABLE IF NOT EXISTS `\" . Phpfox::getT(\'document_rating\') . \"` (\r\n  `rate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,\r\n  `item_id` int(10) unsigned NOT NULL,\r\n  `user_id` int(10) unsigned NOT NULL,\r\n  `rating` decimal(4,2) NOT NULL DEFAULT \'0.00\',\r\n  `time_stamp` int(10) unsigned NOT NULL,\r\n  PRIMARY KEY (`rate_id`),\r\n  KEY `item_id` (`item_id`,`user_id`),\r\n  KEY `item_id_2` (`item_id`)\r\n) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1\");\r\n\r\n\$this->database()->query(\"CREATE TABLE IF NOT EXISTS `\" . Phpfox::getT(\'document_track\') . \"` (\r\n  `item_id` int(10) unsigned NOT NULL,\r\n  `user_id` int(10) unsigned NOT NULL,\r\n  `time_stamp` int(10) unsigned NOT NULL,\r\n  KEY `item_id` (`item_id`,`user_id`)\r\n) ENGINE=InnoDB DEFAULT CHARSET=latin1 \");\r\n\r\n\$this->database()->query(\"ALTER TABLE `\" . Phpfox::getT(\'user_field\') . \"`\r\n ADD `total_document` INT( 10 ) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `total_video` \");\r\n\r\n\$this->database()->query(\"ALTER TABLE `\" . Phpfox::getT(\'user_activity\') . \"`\r\n ADD `activity_document` INT( 10 ) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `activity_comment` \");\r\n ',NULL),
				('younet_document','3.01p1','\r\n\$this->database()->query(\"INSERT INTO `\" . Phpfox::getT(\'feed_share\') . \"`(`product_id`,`module_id`,`title`,`description`,`block_name`,`no_input`,`is_frame`,`ajax_request`,`no_profile`,`icon`,`ordering`) VALUES (\'phpfox\', \'document\', \'{phrase var=\\\\\'document.quick_share_document\\\\\'}\', \'{phrase var=\\\\\'document.say_something_about_this_document\\\\\'}\', \'share\', \'0\', \'1\', null, \'1\', \'document.jpg\', \'7\');\");\r\n			\r\n			',NULL),
				('younet_document','3.01p2','\r\n				\$this->database()->query(\"INSERT IGNORE INTO `\" . Phpfox::getT(\'product_dependency\') . \"`(`product_id`,`type_id`, `check_id`, `dependency_start`) VALUES\r\n					(\'younet_document\',\'product\', \'younetcore\', \'3.01p2\')\");\r\n				\$this->database()->query(sprintf(\"DELETE FROM `%s` WHERE `module_id` = \\\\\"document\\\\\";\", PHPFOX::getT(\"block\")));\r\n				\$this->database()->query(sprintf(\"\r\n					INSERT INTO `%s` (`title`,`type_id`,`m_connection`,`module_id`,`product_id`,`component`,`location`,`is_active`,`ordering`,`disallow_access`,`can_move`,`version_id`) VALUES\r\n					 (\'document_topview\',0,\'document.index\',\'document\',\'younet_document\',\'topview\',\'3\',1,5,NULL,0,NULL),\r\n					 (\'document_statistic\',0,\'document.index\',\'document\',\'younet_document\',\'statistic\',\'3\',1,4,NULL,0,NULL),\r\n					 (NULL,0,\'document.view\',\'document\',\'younet_document\',\'detail\',\'1\',0,2,NULL,0,NULL),\r\n					 (NULL,0,\'document.view\',\'document\',\'younet_document\',\'menu\',\'3\',0,1,NULL,0,NULL),\r\n					 (NULL,0,\'document.index\',\'document\',\'younet_document\',\'filter\',\'1\',0,3,NULL,0,NULL),\r\n					 (NULL,0,\'document.index\',\'document\',\'younet_document\',\'category\',\'1\',1,2,NULL,0,NULL),\r\n					 (NULL,0,\'document.add\',\'document\',\'younet_document\',\'menu\',\'3\',0,1,NULL,0,NULL),\r\n					 (NULL,0,\'document.index\',\'document\',\'younet_document\',\'menu\',\'3\',0,1,NULL,0,NULL),\r\n					 (NULL,0,\'document.index\',\'document\',\'younet_document\',\'topusers\',\'3\',1,6,NULL,0,NULL);\r\n				\", PHPFOX::getT(\"block\")));\r\n				\$this->database()->query(sprintf(\"DELETE FROM `%s` WHERE `product_id` = \\\\\"younet_document\\\\\";\", PHPFOX::getT(\"product\")));\r\n				\$this->database()->query(sprintf(\"\r\n					INSERT INTO `%s` (`product_id`,`is_core`,`title`,`description`,`version`,`latest_version`,`last_check`,`is_active`,`url`,`url_version_check`) VALUES \r\n					 (\'younet_document\',0,\'YouNet Document\',\'by YouNet Company\',\'3.01p2\',NULL,0,1,NULL,NULL);\r\n				\", PHPFOX::getT(\"product\")));\r\n				\r\n				\$this->database()->query(sprintf(\"DELETE FROM `%s` WHERE `product_id` = \\\\\"younet_document\\\\\";\", PHPFOX::getT(\"product_dependency\")));\r\n				\$this->database()->query(sprintf(\"\r\n					INSERT IGNORE INTO `%s`(`product_id`,`type_id`, `check_id`, `dependency_start`) VALUES\r\n						(\'younet_document\',\'product\', \'younetcore\', \'3.01p2\')\r\n				\", Phpfox::getT(\'product_dependency\')));\r\n			',NULL);
			");
		/*/solve module info*/
		$this->call("$('#contener_percent').css({'width': '100%'}).text('100%');");
		$this->alert("Data migrated successfully!");
	}

    public function updateActivity()
    {
        if (Phpfox::getService('document.category.process')->updateActivity($this->get('id'), $this->get('active')))
        {

        }
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'document_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove();
    }
}  
?>
