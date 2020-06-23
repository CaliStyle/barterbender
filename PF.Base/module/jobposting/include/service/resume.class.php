<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

class JobPosting_Service_Resume extends Phpfox_service
{
    private $_iId;
    private $_aVars;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_aVars = array();
    }
    
    public function getCss()
    {
        $urlStyle = Phpfox::getParam('core.path_file').'theme/frontend/default/style/default/css/';
        
        $sCss = file_get_contents($urlStyle.'layout.css');
        $sCss .= file_get_contents($urlStyle.'custom.css');
        $sCss .= file_get_contents(Phpfox::getParam('core.url_module').'resume/static/css/default/default/resume.css');
        $sCss .= ' #main_content { margin-left: 0; }  .yns .basic_info_content{background:none} .yns .basic_info_content .yns-bg {background: none;} #content_holder { overflow:hidden; } .content3 { width: 100% !important; }';
        
        return $sCss;
    }
    
    public function buildHtml($iId)
    {
        $this->_iId = $iId;
        
        if(!$this->_getView())
        {
            return false;
        }
        
        $this->_getBasic();
        $this->_getExperience();
        $this->_getSkill();
        $this->_getEducation();
        $this->_getCertification();
        $this->_getLanguage();
        $this->_getPublication();
        $this->_getAddition();
        
        $sHtml = $this->_buildHtmlBasic();
        $sHtml .= $this->_buildHtmlExperience();
        $sHtml .= $this->_buildHtmlSkill();
        $sHtml .= $this->_buildHtmlEducation();
        $sHtml .= $this->_buildHtmlCertification();
        $sHtml .= $this->_buildHtmlLanguage();
        $sHtml .= $this->_buildHtmlPublication();
        $sHtml .= $this->_buildHtmlAddition();
        
        if(empty($sHtml))
        {
            return false;
        }
        
        $sHtml = '<div id="main_core_body_holder">
            <div id="main_content_holder">
                <div id="js_controller_resume_view">
                    <div class="holder">
                        <div id="content_holder">
                            <div id="main_content">
                                <div id="main_content_padding">
                                    <idv id="content_load_data">
                                        <div id="content" class="content_float content2">
                                            <div id="site_content">'.$sHtml.'</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        
        return $sHtml;
    }
    
    private function _getView()
    {
		$aResume = Phpfox::getService('resume.basic')->getQuick($this->_iId);
        if(!$aResume)
        {
            return false;
        }
		
		//support custom fields
		$turnonFields = false;
		$aCustom = Phpfox::getService('resume.custom')->getFields($this->_iId);
        if (isset($aCustom[0]))
        {
            foreach ($aCustom as $iKey => $aField)
            {
                $sValue = $aField['value'];
				
                if (preg_match("/^\[.*?\]$/", $sValue))
                {
                    $aValues = explode(",", trim($sValue, '[]'));
                    $sValue = "";
                    foreach ($aValues as $sVal)
                    {
                        $sVal = trim($sVal, '"');
                        $sValue .= "<li>$sVal</li>";
                    }
                    $sValue = '<ul>' . $sValue . '</ul>';
                }
                $aField['value'] = $sValue;
               
				if($sValue!="")
				{
					$turnonFields = true;
				}
                $aCustom[$iKey] = $aField;
            }
            $aCustomFields = $aCustom;
        }
        else
        {
            $aCustomFields = array();
        }
        
        $this->_aVars['turnonFields'] = $turnonFields;
        
        return true;
    }
    
    private function _getBasic()
    {
        $aResume = Phpfox::getService("resume.basic")->getBasicInfo($this->_iId);
        
		if(!$aResume['headline'])
		{
			$aResume['headline'] = _p('untitled_resume');
		}
		
		//Get Category List
		$aCats = Phpfox::getService('resume.category')->getCatNameList($this->_iId);
		
		// Get Gender
		$aResume['gender_parsed'] = Phpfox::getService('user')->gender($aResume['gender']);
		
		// Get Birthday
		if($aResume['birthday'])
		{
			$aBirthDay = Phpfox::getService('user')->getAgeArray($aResume['birthday']);
			$aResume['birthday_parsed']=Phpfox::getTime(Phpfox::getParam('user.user_dob_month_day_year'), mktime(0, 0, 0, $aBirthDay['month'], $aBirthDay['day'], $aBirthDay['year']), false);
		}
		else
		{
			$aResume['birthday_parsed'] = "";
		}
		
        // Get Level
		$aResume['level_name'] = Phpfox::getService('resume.level')->getLevelById($aResume['level_id']);
		$aResume['authorized_position'] = Phpfox::getService('resume.level')->getLevelById($aResume['authorized_level_id']);
		
		// Parse Skills 
		if(Phpfox::getLib('parse.format')->isSerialized($aResume['skills']))
		{
			$aSkills = unserialize($aResume['skills']);
			$aResume['skills'] = explode(',', $aSkills);
		}
		
		// Get Current Working Place
		$aCurrentWork = Phpfox::getService('resume.experience')->getCurrentWork($this->_iId);
		
		// Get latest education 
		$aLatestEducation = Phpfox::getService('resume.education')->getLatestEducation($this->_iId);
        
        $aAccount = Phpfox::getService("resume.account")->getAccount();
		
        $this->_aVars['aResume'] = $aResume;
		$this->_aVars['aCurrentWork'] = $aCurrentWork;
		$this->_aVars['aLatestEducation'] = $aLatestEducation;
		$this->_aVars['aCats'] = $aCats;
		$this->_aVars['aAccount'] = $aAccount;
        
        return true;
    }
    
    private function _buildHtmlBasic()
    {
        if(empty($this->_aVars['aResume']))
        {
            return '';
        }
        
        $oUrl = Phpfox::getLib('url');
        $oImageHelper = Phpfox::getLib('image.helper');
        
        $sHtml = '<h1 class="yns-basic-header"><a title="'.$this->_aVars['aResume']['headline'].'" href="'.$oUrl->permalink('resume.view', $this->_aVars['aResume']['resume_id'], $this->_aVars['aResume']['headline']).'">'.$this->_aVars['aResume']['headline'].'</a><div></div></h1>';
        $sHtml .= '<div class="yns resume_basic no-res-complete"><div class="basic_info_content"><div class="yns-bg" style="padding-top: 10px;">';
        $sHtml .= '<!-- resume image --><div class="resume_image">';
        $imageResume = $oImageHelper->display(array(
			'server_id' => $this->_aVars['aResume']['server_id'],
			'path' => 'core.url_pic',
			'file' => 'resume/'.$this->_aVars['aResume']['image_path'],
			'suffix' => '_120',
			'max_width' => 120,
			'max_height' => 120
		));
        // let show image in pfd export
        $imageResume = str_replace("data-src","src",$imageResume);
        $sHtml .=$imageResume;		



        $sHtml .= '</div>';
		$sHtml .= '<div class="basic_info"><div>';
		$sHtml .= '<!-- full name - birthday - gender - marital status --><p><span class="name">'.$this->_aVars['aResume']['full_name'].'</span></p>';
		$sHtml .= '<p class="extra_info">';
        $sHtml .= (!empty($this->_aVars['aResume']['birthday_parsed'])) ? $this->_aVars['aResume']['birthday_parsed'] : '';
        $sHtml .= (!empty($this->_aVars['aResume']['gender_parsed'])) ? ((!empty($this->_aVars['aResume']['birthday_parsed'])) ? ' | ' : '').$this->_aVars['aResume']['gender_parsed'] : '';
        $sHtml .= (!empty($this->_aVars['aResume']['marital_status'])) ? ((!empty($this->_aVars['aResume']['birthday_parsed']) || !empty($this->_aVars['aResume']['gender_parsed'])) ? ' | ' : '')._p(''.$this->_aVars['aResume']['marital_status']) : '';
        $sHtml .= '</p>';
		$sHtml .= '<!-- Current position --><p>';
        $sHtml .= $this->_aVars['aCurrentWork'] ? $this->_aVars['aCurrentWork']['title'].' '._p('at').' '.$this->_aVars['aCurrentWork']['company_name'] : '';
        $sHtml .= '</p>';
		$sHtml .= '<!-- Country + City --><p class="extra_info">'.Phpfox::getService('core.country')->getCountry($this->_aVars['aResume']['country_iso']);
        $sHtml .= (!empty($this->_aVars['aResume']['location_child_id'])) ? ', '.Phpfox::getService('core.country')->getChild($this->_aVars['aResume']['country_child_id']) : '';
        $sHtml .= (!empty($this->_aVars['aResume']['city'])) ? ' > '.$this->_aVars['aResume']['city'] : '';
        $sHtml .= '</p></div>';
		$sHtml .= '<div class="person-info">';
        if (count($this->_aVars['aCats']) > 0)
        {
            $sHtml .= '<div class="item"><!-- Category list --><div class="item-label">'._p('categories').':</div><div class="item-value">';
            foreach ($this->_aVars['aCats'] as $iKey => $aCat)
            {
                if ($iKey > 0)
                {
                    $sHtml .= ' | ';
                }
                $sHtml .= '<a href="'.$oUrl->permalink('resume.category', $aCat['category_id'], $aCat['category_id']).'">'.$aCat['name'].'</a>';
            }
            $sHtml .= '</div></div>';
        }
        if ($this->_aVars['aResume']['year_exp'] != 0)
        {
            $sHtml .= '<!-- Year of experience --><div class="info"><div class="item-label">'._p('years_of_experience').':</div><div class="item-value">';
            $sHtml .= $this->_aVars['aResume']['year_exp'].' '.(($this->_aVars['aResume']['year_exp']==1) ? _p('lowercase_year') : _p('years'));
            $sHtml .= '</div></div>';
        }
        if ($this->_aVars['aResume']['level_id'] > 0)
        {
            $sHtml .= '<!-- Highest level --><div class="info"><div class="item-label">'._p('highest_level').': </div><div class="item-value">'.$this->_aVars['aResume']['level_name'].'</div></div>';
        }
        if ($this->_aVars['aLatestEducation'])
        {
            $sHtml .= '<!-- Education --><div class="info"><div class="item-label">'._p('education').': </div><div class="item-value">'.$this->_aVars['aLatestEducation']['degree'].', '.$this->_aVars['aLatestEducation']['field'].' '._p('at').' '.$this->_aVars['aLatestEducation']['school_name'].'</div></div>';
        }
		if ($this->_aVars['aResume']['authorized_country_iso'] || $this->_aVars['aResume']['authorized_location'] || $this->_aVars['aResume']['authorized_level_id'])
        {
            $sHtml .= '<!-- Authorized to work on --><div class="info"><div style="margin-bottom: 5px;"><strong>'._p('authorized_to_work_on').'</strong></div>';
			if ($this->_aVars['aResume']['authorized_country_iso'])
            {
				$sHtml .= '<div class="item-label">'._p('country').': </div><div class="item-value">'.Phpfox::getService('core.country')->getCountry($this->_aVars['aResume']['authorized_country_iso']);
                if (!empty($this->_aVars['aResume']['authorized_country_child_id']))
                {
                    $sHtml .= ', '.Phpfox::getService('core.country')->getChild($this->_aVars['aResume']['authorized_country_child_id']);
                }
			    $sHtml .= '</div>';
			}
			if ($this->_aVars['aResume']['authorized_location'])
            {              
				$sHtml .= '<div class="item-label">'._p('location').': </div><div class="item-value">'.$this->_aVars['aResume']['authorized_location'].'</div>';
			}
			if ($this->_aVars['aResume']['authorized_other_level'])
            {
				$sHtml .= '<div class="item-label">'._p('position').': </div><div class="item-value">'.$this->_aVars['aResume']['authorized_other_level'].'</div>';
			}
            elseif ($this->_aVars['aResume']['authorized_level_id'] > 0)
            {
                $sHtml .= '<div class="item-label">'._p('position').': </div><div class="item-value">'.$this->_aVars['aResume']['authorized_position'].'</div>';
            }
			$sHtml .= '</div>';
        }
		if ($this->_aVars['turnonFields'])
        {
            $sHtml .= '<div class="info"><div style="margin-bottom: 5px;"><strong>'._p('custom_fields').'</strong></div><div>';
            if (count((array)$this->_aVars['aViewCustomFields']))
            {
                foreach ((array) $this->_aVars['aViewCustomFields'] as $this->_aVars['aCustomField'])
                {
                    if ($this->_aVars['aCustomField']['value'] != "")
                    { 
						$sHtml .= '<div class="item-label">'._p($this->_aVars['aCustomField']['phrase_var_name']).': </div><div class="item-value">'.$this->_aVars['aCustomField']['value'].'</div>';
                    }
                }
            }
            $sHtml .= '</div></div>';
        }
		$sHtml .= '</div></div></div></div><div class="clear"></div>';
        $sHtml .= '<!-- Contact Information --><div class="yns contact-info extra_info"><h3>'._p('contact_info').'</h3>';
		if (!empty($this->_aVars['aResume']['phone']))
        {
            $sHtml .= '<!-- Phone --><div class="info"><div class="item-label">'._p('phone_number').':</div><div class="item-value">';
			foreach ($this->_aVars['aResume']['phone'] as $aPhone)
            {
			   $sHtml .= '<p>'.$aPhone['text'].' ('._p(''.$aPhone['type']).')</p>';
			}
			$sHtml .= '</div></div>';
		}
        if (!empty($this->_aVars['aResume']['imessage']))
        {
            $sHtml .= '<!-- IM --><div class="info"><div class="item-label">'._p('im').':</div><div class="item-value"><p></p>';
			foreach ($this->_aVars['aResume']['imessage'] as $aImessage)
            {
			   $sHtml .= '<p>'.$aImessage['text'].' ('._p(''.$aImessage['type']).')</p>';
			}
			$sHtml .= '</div></div>';
		}
        if (!empty($this->_aVars['aResume']['email']))
        {
            $sHtml .= '<!-- Email --><div class="info"><div class="item-label">'._p('email').':</div><div class="item-value"><p></p>';
            foreach ($this->_aVars['aResume']['email'] as $aEmail)
            {
                $sHtml .= '<p>'.$aEmail.'</p>';
            }
            $sHtml .= '</div></div>';
        }
    	$sHtml .= '</div>';
		if ($this->_aVars['aResume']['summary_parsed'])
        {
            $sHtml .= '<!-- Summary --><div class="yns contact-info summary_info extra_info"><h3>'._p('summary').'</h3><p>'.$this->_aVars['aResume']['summary_parsed'].'</p>';
		}
    	$sHtml .= '</div></div>';

        return $sHtml;
    }
	
    private function _getExperience()
    {
        // Get experience
        $aExperience = Phpfox::getService("resume.experience")->getAllExperience($this->_iId);

        // Get working time period for each experience
        foreach ($aExperience as $iKey => $aExp)
        {
            if ($aExp['is_working_here'])
            {
                $aExp['end_month'] = date("m");
                $aExp['end_year'] = date("Y");
            }
            $iYearPeriod = $aExp['end_year'] - $aExp['start_year'];
            $iMonthPeriod = $aExp['end_month'] - $aExp['start_month'];

            if ($iMonthPeriod < 0)
            {
                $iMonthPeriod = $iMonthPeriod + 12;
                $iYearPeriod = $iYearPeriod - 1;
            }

            $aExperience[$iKey]['period'] = " ";

            if ($iYearPeriod > 1)
            {
                $aExperience[$iKey]['period'] .= $iYearPeriod." "._p("years")." ";
            }
            elseif ($iYearPeriod == 1)
            {
                $aExperience[$iKey]['period'] .= $iYearPeriod." "._p("lowercase_year")." ";
            }

            if ($iMonthPeriod > 1)
            {
                $aExperience[$iKey]['period'] .= $iMonthPeriod." "._p("months")." ";
            }
            elseif ($iMonthPeriod == 1)
            {
                $aExperience[$iKey]['period'] .= $iMonthPeriod." "._p("month")." ";
            }
        }

        $this->_aVars['aExperience'] = $aExperience;
        
        return true;
    }

    private function _buildHtmlExperience()
    {
        $sHtml = '<!-- Experience information layout here --><div class="yns contact-info resume_experience">';
        if (count((array)$this->_aVars['aExperience']) > 0)
        {
            $sHtml .= '<h3>'._p("experience").'</h3>';
            foreach ((array)$this->_aVars['aExperience'] as $this->_aVars['aExp'])
            {
                $sHtml .= '<div class="experience_content extra_info" id="experience_'.$this->_aVars['aExp']['experience_id'].'" >';
                $sHtml .= '<!-- Title --><p class="f_14"><b>'.$this->_aVars['aExp']['title'].'</b></p>';
                $sHtml .= '<!-- Company Name --><p class="company_name">'.$this->_aVars['aExp']['company_name'].'</p>';
                $sHtml .= '<!-- Start time --><p>'.Phpfox::getTime('F Y', mktime(0, 0, 0, $this->_aVars["aExp"]["start_month"], 1, $this->_aVars["aExp"]["start_year"]), false).' - ';
                $sHtml .= '<!-- End Time -->';
                if ($this->_aVars['aExp']['is_working_here'] || !$this->_aVars['aExp']['end_month'] || !$this->_aVars['aExp']['end_year'])
                {
                    $sHtml .= _p("present");
                }
                else
                {
                    $sHtml .= Phpfox::getTime('F Y', mktime(0, 0, 0, $this->_aVars["aExp"]["end_month"], 1, $this->_aVars["aExp"]["end_year"]), false);
                }
                $sHtml .= '<!-- Working Period -->('.$this->_aVars['aExp']['period'].')';
                $sHtml .= '<!-- Location -->';
                if (!empty($this->_aVars['aExp']['location']))
                {
                    $sHtml .= '	| '.$this->_aVars['aExp']['location'];
                }
                $sHtml .= '</p><!-- Description --><p>'.$this->_aVars['aExp']['description_parsed'].'</p></div>';
            }
        }
        $sHtml .= '</div>';

        return $sHtml;
    }

    private function _getSkill()
    {
        return true;
    }

    private function _buildHtmlSkill()
    {
        $sHtml = '<!-- Skill layout here --><div class="yns contact-info">';
        if (count((array)$this->_aVars['aResume']['skills']) > 0)
        {
            $sHtml .= '<h3>'._p("skills_expertise").'</h3><div class="skill_education">';
            foreach ((array)$this->_aVars['aResume']['skills'] as $k => $this->_aVars['aSkill'])
            {
                if($k > 0)
                {
                    $sHtml .= ', ';
                }
                $sHtml .= '<a>'.$this->_aVars['aSkill'].'</a>';
            }
            $sHtml .= '</div>';
        }
        $sHtml .= '</div>';

        return $sHtml;
    }

    private function _getEducation()
    {
        // Get education
        $aEducation = Phpfox::getService("resume.education")->getAllEducation($this->_iId);

        $this->_aVars['aEducation'] = $aEducation;
        
        return true;
    }

    private function _buildHtmlEducation()
    {
        $sHtml = '<!-- Education information layout here --><div class="yns contact-info resume_experience">';
        if (count((array)$this->_aVars['aEducation']) > 0)
        {
            $sHtml .= '<h3>'._p("education").'</h3>';
            foreach ((array) $this->_aVars['aEducation'] as $this->_aVars['aEdu'])
            {
                $sHtml .= '<div class="experience_content extra_info" id="education_'.$this->_aVars['aEdu']['education_id'].'">';
                $sHtml .= '<!-- School Name --><p class="f_14"><strong>'.$this->_aVars['aEdu']['school_name'].'</strong></p>';
                $sHtml .= '<!-- Degree, Field --><p>'.$this->_aVars['aEdu']['degree'].', '.$this->_aVars['aEdu']['field'].'</p>';
                $sHtml .= '<!-- Time Period --><p>'.$this->_aVars['aEdu']['start_year'].' - '.$this->_aVars['aEdu']['end_year'].'</p>';
                if ($this->_aVars['aEdu']['grade'])
                {
                    $sHtml .= '<!-- Grade --><p>'._p("grade").': '.$this->_aVars['aEdu']['grade'].'</p>';
                }
                if ($this->_aVars['aEdu']['activity_parsed'])
                {
                    $sHtml .= '<!-- Activity --><p><i>'._p("activities_and_societies").':</i></p><p style="margin-left:10px;">'.$this->_aVars['aEdu']['activity_parsed'].'</p>';
                }
                if ($this->_aVars['aEdu']['note_parsed'])
                {
                    $sHtml .= '<!-- Note --><p><i>'._p("additional_notes").':</i></p><p style="margin-left:10px;">'.$this->_aVars['aEdu']['note_parsed'].'</p>';
                }
                $sHtml .= '</div>';
            }
        }
        $sHtml .= '</div>';

        return $sHtml;
    }

    private function _getCertification()
    {
        // Get certification
        $aCertificates = Phpfox::getService("resume.certification")->getAllCertification($this->_iId);

        $this->_aVars['aCertificates'] = $aCertificates;
        
        return true;
    }

    private function _buildHtmlCertification()
    {
        $sHtml = '<!-- Certification information layout here --><div class="yns contact-info resume_experience">';
        if (count((array)$this->_aVars['aCertificates']) > 0)
        {
            $sHtml .= '<h3>'._p("certifications").'</h3>';
            foreach ((array)$this->_aVars['aCertificates'] as $this->_aVars['aCertificate'])
            {
                $sHtml .= '<div class="experience_content extra_info" id="certification_'.$this->_aVars['aCertificate']['certification_id'].'">';
                $sHtml .= '<!-- Certificate Name --><p class="f_14"><strong>'.$this->_aVars['aCertificate']['certification_name'].'</strong></p>';
                if ($this->_aVars['aCertificate']['course_name'])
                {
                    $sHtml .= '<!-- Course Name --><p>'._p("course_s_name").': '.$this->_aVars['aCertificate']['course_name'].'</p>';
                }
                if ($this->_aVars['aCertificate']['start_month'] && $this->_aVars['aCertificate']['start_year'] && $this->_aVars['aCertificate']['end_month'] && $this->_aVars['aCertificate']['end_year'] && $this->_aVars['aCertificate']['training_place'])
                {
                    $sHtml .= '<!-- Time Period --><p>'._p("attended").': '.Phpfox::getTime('F Y', mktime(0, 0, 0, $this->_aVars["aCertificate"]["start_month"], 1, $this->_aVars["aCertificate"]["start_year"]), false).' - '.Phpfox::getTime('F Y', mktime(0, 0, 0, $this->_aVars["aCertificate"]["end_month"], 1, $this->_aVars["aCertificate"]["end_year"]), false).' '._p("at").' '.$this->_aVars['aCertificate']['training_place'].'</p>';
                }
                if ($this->_aVars['aCertificate']['note_parsed'])
                {
                    $sHtml .= '<!-- Note --><p>'._p("additional_notes").':</p><p style="margin-left:10px;">'.$this->_aVars['aCertificate']['note_parsed'].'</p>';
                }
                $sHtml .= '</div>';
            }
        }
        $sHtml .= '</div>';

        return $sHtml;
    }

    private function _getLanguage()
    {
        // Get language
        $aLanguages = Phpfox::getService("resume.language")->getAllLanguage($this->_iId);

        $this->_aVars['aLanguages'] = $aLanguages;
        
        return true;
    }

    private function _buildHtmlLanguage()
    {
        $sHtml = '<!-- Language information layout here --><div class="yns contact-info resume_experience">';
        if (count((array)$this->_aVars['aLanguages']) > 0)
        {
            $sHtml .= '<h3>'._p("languages").'</h3>';
            foreach ((array) $this->_aVars['aLanguages'] as $this->_aVars['aLanguage'])
            {
                $sHtml .= '<div class ="experience_content extra_info" id="language_'.$this->_aVars['aLanguage']['language_id'].'"><!-- Language Name (Level) --><p class="f_14"><strong>'.$this->_aVars['aLanguage']['name'].'</strong> ';
                if ($this->_aVars['aLanguage']['level'])
                {
                    $sHtml .= '('.$this->_aVars['aLanguage']['level'].')';
                }
                $sHtml .= '</p>';
                if ($this->_aVars['aLanguage']['note'])
                {
                    $sHtml .= '<!-- Note --><i>'._p("note").':</i><p>'.$this->_aVars['aLanguage']['note'].'</p>';
                }
                $sHtml .= '</div>';
            }
        }
        $sHtml .= '</div>';

        return $sHtml;
    }

    private function _getPublication()
    {
        // Get publication
        $aPublications = Phpfox::getService("resume.publication")->getAllPublication($this->_iId);

        $this->_aVars['aPublications'] = $aPublications;
        
        return true;
    }

    private function _buildHtmlPublication()
    {
        $sHtml = '<!-- Publication information layout here --><div class="yns contact-info resume_experience">';
        if (count((array)$this->_aVars['aPublications']) > 0)
        {
            $sHtml .= '<h3>'._p("publications").'</h3>';
            foreach ((array)$this->_aVars['aPublications'] as $this->_aVars['aPub'])
            {
                $sHtml .= '<div class="experience_content extra_info" id="publication_'.$this->_aVars['aPub']['publication_id'].'"><!-- Publication Type --><span class="f_14"><strong>';
                if ($this->_aVars['aPub']['type_id'] == 1)
                {
                    $sHtml .= _p("book");
                }
                elseif ($this->_aVars['aPub']['type_id'] == 2)
                {
                    $sHtml .= _p("magazine");
                }
                else
                {
                    $sHtml .= $this->_aVars['aPub']['other_type'];
                }
                $sHtml .= '</strong></span>';
                if ($this->_aVars['aPub']['publisher'])
                {
                    $sHtml .= ' - <span class="f_14"><strong>'.$this->_aVars['aPub']['publisher'].'</strong></span>';
                }
                if ($this->_aVars['aPub']['published_month'] && $this->_aVars['aPub']['published_year'])
                {
                    $sHtml .= ', '.Phpfox::getTime('d F Y', mktime(0, 0, 0, $this->_aVars["aPub"]["published_month"], $this->_aVars["aPub"]["published_day"], $this->_aVars["aPub"]["published_year"]), false);
                }
                $sHtml .= '<!-- Publication Title and Url --><div class="publication_title_url"><strong>'.$this->_aVars['aPub']['title'].'</strong>';
                if ($this->_aVars['aPub']['publication_url'])
                {
                    $sHtml .= '	- <a href ="'.$this->_aVars['aPub']['publication_url'].'" target="_blank" title="'.$this->_aVars['aPub']['publication_url'].'">'.Phpfox::getLib('phpfox.parse.output')->shorten($this->_aVars['aPub']['publication_url'], 150, '...').'</a>';
                }
                $sHtml .= '</div>';
                if (isset($this->_aVars['aPub']['author_list']))
                {
                    $sHtml .= '<!-- Publication Authors --><div class="publication_info"><i>'._p("author").':</i>'.$this->_aVars['aPub']['author_list'].'</div>';
                }
                if ($this->_aVars['aPub']['note_parsed'])
                {
                    $sHtml .= '<!-- Publication Summary --><div class ="publication_summary"><i>'._p("summary").':</i><div style="margin-left:10px;">'.$this->_aVars['aPub']['note_parsed'].'</div></div>';
                }
                $sHtml .= '</div>';
            }
        }
        $sHtml .= '</div>';

        return $sHtml;
    }

    private function _getAddition()
    {
        // Get additional information
        $aAddition = Phpfox::getService("resume.addition")->getAddition($this->_iId);

        $this->_aVars['aAddition'] = $aAddition;
        
        return true;
    }

    private function _buildHtmlAddition()
    {
        $sHtml = '<!-- Addition information layout here --><div class="yns contact-info extra_info">';
        if (!empty($this->_aVars['aAddition']['email']) || !empty($this->_aVars['aAddition']['sport']) || !empty($this->_aVars['aAddition']['movies']) || !empty($this->_aVars['aAddition']['interests']) || !empty($this->_aVars['aAddition']['music']))
        {
            $sHtml .= '<h3>'._p("additional_information").'</h3><div class ="experience_content extra_info">';
            if (!empty($this->_aVars['aAddition']['email']))
            {
                $sHtml .= '<div class="info "><div class="item-label">'._p("websites").':</div><div class="item-value"><p></p>';
                if (count((array)$this->_aVars['aAddition']['email']))
                {
                    foreach ((array)$this->_aVars['aAddition']['email'] as $this->_aVars['aWebsite'])
                    {
                        $sHtml .= '<p>'.$this->_aVars['aWebsite'].'</p>';
                    }
                }
                $sHtml .= '</div></div>';
            }
            if (!empty($this->_aVars['aAddition']['sport']))
            {
                $sHtml .= '<div class="info "><div class="item-label">'._p("sport").': </div><div class="item-value">'.$this->_aVars['aAddition']['sport'].'</div></div>';
            }
            if (!empty($this->_aVars['aAddition']['movies']))
            {
                $sHtml .= '<div class="info "><div class="item-label">'._p("movies").': </div><div class="item-value">'.$this->_aVars['aAddition']['movies'].'</div></div>';
            }
            if (!empty($this->_aVars['aAddition']['interests']))
            {
                $sHtml .= '<div class="info "><div class="item-label">'._p("interests").':</div><div class="item-value">'.$this->_aVars['aAddition']['interests'].'</div></div>';
            }
            if (!empty($this->_aVars['aAddition']['music']))
            {
                $sHtml .= '<div class="info "><div class="item-label">'._p("music").':</div><div class="item-value">'.$this->_aVars['aAddition']['music'].'</div></div>';
            }
            $sHtml .= '</div>';
        }
        $sHtml .= '</div>';

        return $sHtml;
    }

}