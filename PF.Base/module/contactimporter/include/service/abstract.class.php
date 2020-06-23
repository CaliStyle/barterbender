<?php

defined('PHPFOX') or exit('NO DICE!');
require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs.class.php');

class Contactimporter_Service_Abstract extends Younet_Service
{

    protected $_sService = '';
	
    protected $_host = '';
	
	protected $_name = '';

	/**
	 * set email service
	 * @param string $sName
	 * @return Contactimporter_Service_Email
	 */
	public function setName($sName)
	{
		$this -> _name = strtolower($sName);
		return $this;
	}
	
	/**
	 * get email service name
	 * @return string
	 */
	public function getName()
	{
		return $this -> _sName;
	}
	
	
	protected function _prepareRows($aRows = array())
	{

	}
	

    public function convert_vi_to_en($str)
    {
        $str1 = $str;

        $str = preg_replace("/[^A-Za-z\+]/", "",  $str1);

        if(empty($str))
        {
            $str = preg_replace("/[^A-Za-z0-9\+]/", "",  $str1);
        }
        $str = str_replace("quot", "", $str);
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);

        return $str;
    }

    
    public function processSocialRows($aRows)
    {
        $aResults = array();
        foreach ($aRows as $i => $aRow)
        {
            $id = $aRow['id'];
            if (empty($id))
                continue;

            if (isset($aRow['profile_image_url']))
            {
                $aRows[$i]['pic'] = $aRow['pic'] = $aRow['profile_image_url'];
            }
            $name = $aRow['name'];
            $name1 = $this->convert_vi_to_en(trim($name));
            $pic = $aRow['pic'];
            if(function_exists('mb_strtoupper'))
            {
                $char = mb_strtoupper(mb_substr($name1, 0, 1, "UTF-8"), "UTF-8");
            }
            else{
                $char = strtoupper(substr($name1, 0, 1));
            }
            if (!preg_match("/[A-Za-z]/", $char))
            {
                $aResults['Z'][] = array('name' => $name, 'id' => $id, 'pic' => $pic);
            }
            else
            {
                for ($start = ord('A'); $start <= ord('Z'); $start++)
                {
                    if (ord($char) == $start)
                    {
                        $aResults[chr($start)][] = array('name' => $name, 'id' => $id, 'pic' => $pic);
                        break;
                    }
                    else
                    {
                        if (!isset($aResults[chr($start)]))
                        {
                            $aResults[chr($start)] = array();
                        }
                    }
                }
            }
        }
        ksort($aResults);
        return $aResults;
    }

}

?>