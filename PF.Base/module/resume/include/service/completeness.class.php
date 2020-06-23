<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Service_Completeness extends Phpfox_Service
{
	private static $prolling =  NULL;
	private static $total_marks =  0;
	private $percent = 0;
	private $trans = array(
			'country_iso' => 'location',
			'year_exp' => 'years_of_experience',
			'image_path' => 'photo',
			'email' => 'email_address',
			'imessage' => 'im',
			'birthday' => 'date_of_birth',
			'skills' => 'skills_expertise',
			'language' => 'languages',
			'publication' => 'publications',
			'certification' => 'certifications',
			'addition' => 'additional_information',
		);
	private $aException = array("category","authorized_level_id");
	
	
	private function changeToLowerRequireFields($RequireFields){
		$i = 0;
		$tmpRequireFields = array();
		if(!$RequireFields)
			return $RequireFields;
		foreach($RequireFields as $Fields){
			$Fields = strtolower(trim($Fields));
			$tmpRequireFields[$i] = $Fields;
			$i++;
		}
		return $tmpRequireFields;
	}
	
	public function __construct()
	{
		if (true)
		{
			$aRequireFields = Phpfox::getParam("resume.required_fields");
			$aRequireFields = $this->changeToLowerRequireFields($aRequireFields);
            
			$prolling = $this->database()
                    ->select('name, score')
                    ->from(Phpfox::getT('resume_completeness_weight'))
                    ->execute('getSlaveRows');
            
            foreach ($prolling as $key => $item)
            {
                self::$prolling[$item['name']] = $item['score'];
                $method = 'check' . ucfirst($item['name']);
                if (method_exists($this, $method))
                {
                    if (($aRequireFields && in_array(trim($item['name']), $aRequireFields)) || in_array(trim($item['name']), $this->aException))
                    {
                        self::$total_marks += $item['score'];
                    }
                }
                else
                {
                    self::$total_marks += $item['score'];
                }
            }
            
			//support custom field for version 3.2
			$aFields = $this->database()->select('cf.*')
	        	->from(Phpfox::getT('resume_custom_field'), 'cf')
	            ->order('cf.ordering ASC')
	            ->execute('getRows');
			
            foreach ($aFields as $aField)
            {
                if ($aField['is_active'] == 1)
                {
                    self::$prolling[$aField['field_name'] . "_customfield"] = $aField['score'];
                    self::$total_marks += $aField['score'];
                }
            }

		}
        
		return self::$prolling;
	}
	
    private function checkAuthorized_country_iso($resume_object)
    {
        if (isset($resume_object['authorized']) && count($resume_object['authorized']) > 0)
        {
            return true;
        }
        return false;
    }
    
    private function checkAuthorized_level_id($resume_object)
    {
        if (isset($resume_object['authorized']) && count($resume_object['authorized']) > 0)
        {
            return true;
        }
        return false;
    }
    
    private function checkAuthorized_location($resume_object)
    {
        if (isset($resume_object['authorized']) && count($resume_object['authorized']) > 0)
        {
            return true;
        }
        return false;
    }
    
	private function implementsCustomFields($resume_object)
    {
        $aCustomFields = Phpfox::getService('resume.custom')->getFields($resume_object['resume_id']);
        foreach ($aCustomFields as $key => $aField)
        {
            $resume_object[$aField['field_name'] . "_customfield"] = $aField['value'];
        }
        return $resume_object;
    }
	
	public function calculate($resume_object)
	{
		$score = 0;
		
		if(!is_array($resume_object))
		{
			$resume_object = $this->getSuggestionList($resume_object);
		}
		
		$aRequireFields = Phpfox::getParam("resume.required_fields");
		$aRequireFields = $this->changeToLowerRequireFields($aRequireFields);
		$aListUncomplete = "";
		
		$resume_object = $this->implementsCustomFields($resume_object);
		
		foreach (self::$prolling as $key => $mark)
		{
			$method = 'check' . ucfirst(trim($key));
			
			if (method_exists($this, $method))
            {
                if ($this->$method($resume_object))
                {
                    if (($aRequireFields && in_array(trim($key), $aRequireFields)) || in_array($key, $this->aException))
                    {
                        $score += $mark;
                    }
                }
                else
                {
                    if (($aRequireFields && in_array(trim($key), $aRequireFields)) || in_array($key, $this->aException))
                    {
                        if ($mark > 0)
                        {
                            
                            $aListUncomplete.=$key . ",";
                        }
                    }
                }
            }
            else if (isset($resume_object[$key]) && !empty($resume_object[$key]))
            {
                
                $score += $mark;
            }
            else
            {
                $allow = array("year_exp");

                if (!in_array($key, $allow))
                {
                    if ($mark > 0)
                    {
                        $aListUncomplete.=$key . ",";
                    }
                }
                else
                {
                    $score += $mark;
                }
            }
		}
		
		$aListUncomplete = trim($aListUncomplete, ",");

		return array($score, $aListUncomplete, self::$total_marks);
	}

    public function checkCountry_iso($aResume)
    {
        return true;
    }
    
	public function checkCategory($resume_object)
	{
		$aRow = Phpfox::getService('resume.category')->getCategoriesData($resume_object['resume_id']);
		if(count($aRow)>0)
			return true;
		return false;
	}
	
	public function checklanguage($resume_object)
	{	
		$aRow = Phpfox::getService("resume.language")->getAllLanguage($resume_object['resume_id']);
		if(count($aRow)>0)
		{
			return true;
		}
		return false;
	}
	
	public function checkaddition($resume_object)
	{
		$aRow = Phpfox::getService("resume.addition")->getAddition($resume_object['resume_id']);
		if(isset($aRow['resume_id']))
		{
			if(!empty($aRow['website']))
				return true;
			if($aRow['sport']!=null && trim($aRow['sport'])!="")
				return true;
			if($aRow['movies']!=null && trim($aRow['movies'])!="")
				return true;
			if($aRow['interests']!=null && trim($aRow['interests'])!="")
				return true;
			if($aRow['music']!=null && trim($aRow['music'])!="")
				return true;
		}
		return false;
	}
	
	public function checkexperience($resume_object)
	{
		$aRow = Phpfox::getService("resume.experience")->getAllExperience($resume_object['resume_id']);
		if(count($aRow)>0)
		{
			return true;
		}
		return false;
	}
	
	public function checkcertification($resume_object)
	{
		$aRow = Phpfox::getService("resume.certification")->getAllCertification($resume_object['resume_id']);
		if(count($aRow)>0)
			return true;
		return false;
	}
	
	public function checkeducation($resume_object)
	{
		$aRow = Phpfox::getService("resume.education")->getAllEducation($resume_object['resume_id']);
		if(count($aRow)>0)
			return true;
		return false;
	}
	
	public function checkpublication($resume_object)
	{
		$aRow = Phpfox::getService("resume.publication")->getAllPublication($resume_object['resume_id']);
		if(count($aRow)>0)
			return true;
		return false;
	}
	
	public function checkskills($resume_object)
	{
		if (Phpfox::getLib('parse.format')->isSerialized($resume_object['skills']))
		{
			$aRow = unserialize($resume_object['skills']);
			if(strlen($aRow)>0)
				return true;
		}
		return false;
	}
	
	/**
	 * Get Basic Infor 
	 * @Param @resume_id: int
	 */
	public function getSuggestionList($resume_id)
	{
		$aBasic = PHpfox::getService("resume.basic")->getBasicInfo($resume_id);
		return $aBasic;
	}
	
	/**
	 * Meter resume completeness
	 */
	public function showUnComplete($sListUncomplete, $resume_id, $score = 0)
    {
        if (strlen($sListUncomplete) <= 0)
            return array();
        $aList = explode(",", $sListUncomplete);
        $aRows = array();
        $iLimit = 5;
        $i = 0;
        $bIsLast = false;
        foreach ($aList as $key => $item)
        {

            if($key == count($aList) - 1)
            {
                $bIsLast = true;
            }
            $phrases = $this->convertPhrase($item, $resume_id, $bIsLast, $score);
            if ($phrases != "")
            {
                $aRows[$item] = $phrases;
                $i++;
                if ($i == $iLimit)
                    break;
            }
            else
            {
                
            }
        }
        return $aRows;
    }
	
	
	/**
     * Used at view page
     * @param string $item
     * @param int $resume_id
     * 
     * 
     * @return string
     */
	public function convertPhrase($item,$resume_id, $bIsLast = false,$score = 0)
	{
		$phrase = "";
		$sCore = 0;
		if(isset(self::$prolling[$item]))
			$sCore = self::$prolling[$item];
		if($bIsLast){
            $iPercentCompleted = round($score * 100 / self::$total_marks);
            $sCore = " (+" . (100 - $iPercentCompleted - $this->percent). "%)";
        }
        else {
		    $iCore = round($sCore * 100 / self::$total_marks);
            $sCore = " (+" . $iCore . "%)";
            $this->percent += $iCore;
        }
		$url = Phpfox::getLib("url")->makeUrl("resume");
        
        $aResume = Phpfox::getService('resume')->getResume($resume_id);
        if (isset($aResume['is_synchronize']) && $aResume['is_synchronize'] == 1)
        {
            $urlprofile = Phpfox::getLib("url")->makeUrl("user.profile");
        }
		else
        {
            $urlprofile = Phpfox::getLib("url")->makeUrl("resume.add", array('id' => $resume_id));
        }
		
		//for support for custom fields version 3.02
		$pos = strpos($item, "_customfield");
		if($pos>0){
			$item = str_replace("_customfield", '', $item);
			$aInfoFields = Phpfox::getService('resume.custom')->getInfoFieldsByPhares($item);
			if($aInfoFields)
			{
				$url = $url."add/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add')." "._p($aInfoFields['phrase_var_name']) . $sCore."</a></p>";
			}
			return $phrase;
		}
		
		//This is the old verison 3.01
		switch($item)
		{
			//for summary fields
			case "headline":
				$url = $url."summary/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_a_headline') . $sCore."</a></p>";
				break;
			case "country_iso":
				$url = $urlprofile;
				$phrase = "<p><a href='".$url."'>"._p('resume.add_location') . $sCore."</a></p>";
				break;
			case "city":
				$url = $urlprofile;
				$phrase = "<p><a href='".$url."'>"._p('resume.add_city') . $sCore."</a></p>";
				break;
			case "zip_code":
				$url = $urlprofile;
				$phrase = "<p><a href='".$url."'>"._p('resume.add_zip_code') . $sCore."</a></p>";
				break;
			case "summary":
				$url = $url."summary/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_a_summary') . $sCore."</a></p>";
				break;	
			case "year_exp":
				$url = $url."summary/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_year_of_experience') . $sCore."</a></p>";
				break;
			case "category":
				$url = $url."summary/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.category') . $sCore."</a></p>";
				break;
			case "authorized_location":
				$url = $url."summary/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.authorized_location') . $sCore."</a></p>";
				break;
			case "authorized_country_iso":
				$url = $url."summary/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.authorized_country_iso') . $sCore."</a></p>";
				break;
			case "authorized_level_id":
				$url = $url."summary/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.authorized_level_id') . $sCore."</a></p>";
				break;
			case "level_id":
				$url = $url."summary/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.level_id') . $sCore."</a></p>";
				break;
					
			//for basic info fields
			case "image_path":
				$url = $url."add/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_a_picture') . $sCore."</a></p>";
				break;
			case "email":
				$url = $url."add/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_email_address') . $sCore."</a></p>";
				break;
			case "imessage":
				$url = $url."add/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_im') . $sCore."</a></p>";
				break;
			case "marital_status":
				$url = $urlprofile;
				$phrase = "<p><a href='".$url."'>"._p('resume.add_marital_status') . $sCore."</a></p>";
				break;
			case "gender":
				$url = $urlprofile;
				$phrase = "<p><a href='".$url."'>"._p('resume.add_gender') . $sCore."</a></p>";
				break;
			case "birthday":
				$url = $url."add/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_date_of_birth') . $sCore."</a></p>";
				break;
			case "full_name":
				$url = Phpfox::getLib("url")->makeUrl("user.setting");
				$phrase = "<p><a href='".$url."'>"._p('resume.add_full_name') . $sCore."</a></p>";
				break;
			case "phone":
				$url = $url."add/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.phone') . $sCore."</a></p>";
				break;


			//for extend fields
			case "skills":
				$url = $url."skill/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_your_skills_expertise') . $sCore."</a></p>";
				break;
			case "experience":
				$url = $url."experience/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_your_experience') . $sCore."</a></p>";
				break;
			case "language":
				$url = $url."language/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_your_language') . $sCore."</a></p>";
				break;
			case "publication":
				$url = $url."publication/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_your_publication') . $sCore."</a></p>";
				break;
			case "certification":
				$url = $url."certification/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_your_certification') . $sCore."</a></p>";
				break;
			case "addition":
				$url = $url."addition/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_your_addition') . $sCore."</a></p>";
				break;
			case "education":
				$url = $url."education/id_".$resume_id."/";
				$phrase = "<p><a href='".$url."'>"._p('resume.add_your_education') . $sCore."</a></p>";
				break;
		}
		return $phrase;
	}

	public function getWeightUncomplete()
	{
		//this is the first version
		$prolling = $this->database()->select('name,score')
			->from(Phpfox::getT('resume_completeness_weight'))
			->execute('getSlaveRows');
		
		$aRequireFields = Phpfox::getParam("resume.required_fields");
		$aRequireFields = $this->changeToLowerRequireFields($aRequireFields);
		foreach($prolling as $key=>$phrases)
		{
			$method = 'check'.ucfirst($phrases['name']);
			if(method_exists($this, $method)){
					if ((!$aRequireFields || ($aRequireFields && !in_array(trim($phrases['name']), $aRequireFields))) && !in_array($phrases['name'],$this->aException)) {
						unset($prolling[$key]);
						continue;
					}
			}
			$name = $this->getNamePhrase($phrases['name']);
			$prolling[$key]['phrase'] = $name;
		}
		
		//support custom field for version 3.2
		$aFields = $this->database()->select('cf.*')
        	->from(Phpfox::getT('resume_custom_field'), 'cf')
            ->order('cf.ordering ASC')
            ->execute('getRows');
		
		$i = count($prolling);	
		foreach($aFields as $aField){
			while(1)
			{
				if(isset($prolling[$i]))
				{
					$i++;
				}	
				else {
					break;
				}
			}
			if($aField['is_active']==1){
				$prolling[$i]['name'] = $aField['field_name']."_customfield";
				$prolling[$i]['phrase'] = _p($aField['phrase_var_name']);
				$prolling[$i]['score'] = $aField['score'];
				$i++;
			}
		}

		return $prolling;
	}
	
	/**
	 * Phrase is converted and apply in admincp
	 */
	public function getNamePhrase($item)
	{
		$phrase = "";
		$trans = $this->trans;
		if(isset($trans[$item]))
		{
			$item = $trans[$item];
		}
		$phrase = "resume.".$item;
		return _p($phrase);
	}

	public function updateWeightComplete($aVals)
	{
        foreach ($aVals as $key => $value) {
            if ((filter_var($value, FILTER_VALIDATE_INT) === false) || (int)$value < 0) {
                return Phpfox_Error::set(_p('Each weight value must be greater than or equal to 0'));
            }
        }
		foreach($aVals as $key=>$value)
		{
			$pos = strpos($key, "_customfield");
			if($pos<=0)
			{
				$this->database()->update(Phpfox::getT('resume_completeness_weight'),array('score'=>$value),'name="'.$key.'"');
			}
			else {
				$key = str_replace("_customfield", '', $key);
				phpfox::getService('resume.custom.process')->updateScoreByFieldId($key, $value);
			}
			
		}
		return true;
	}
}

?>