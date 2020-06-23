<?php

defined('PHPFOX') or exit('NO DICE!');
require_once dirname(dirname(__file__)) . '/item/item_abstract.class.php';

class Contest_Service_Entry_Item_Photo extends Phpfox_service implements Contest_Service_Entry_Item_Item_Abstract
{
    private $_aImageSize = array(30, 50, 200, 500, 1024);

    /**
     * [$_sOriginalSuffix string of the original images
     * @var string
     */
    private $_sOriginalSuffix = '';

    public function __construct()
    {
        if (Phpfox::isModule('advancedphoto')) {
            $this->_sPhotoModuleName = 'advancedphoto';
        } else {
            $this->_sPhotoModuleName = 'video';

        }
        $this->_sTable = Phpfox::getT('photo');
    }

    public function getAddNewItemLink($iContestId, $iSourceId = 1)
    {
        $sAddParamName = Phpfox::getService('contest.constant')->getYnAddParamForNavigateBack();
        if ($this->_sPhotoModuleName == 'advancedphoto') {
            $sLink = Phpfox::getLib('url')->makeUrl('advancedphoto.add', array($sAddParamName => $iContestId));
        } else {
            $sLink = Phpfox::getLib('url')->makeUrl('photo.add', array($sAddParamName => $iContestId));
        }


        return $sLink;
    }

    /**
     * in case we encounter a post form, we know it is a search request
     * @param  integer $iLimit number of items per page
     * @param  integer $iPage page number
     * @return array {'total' => int, 'aItems' => array of item}
     */
    public function getItemsOfCurrentUser($iLimit = 5, $iPage = 0, $iSourceId = 2)
    {
        $sConds = 'user_id = ' . Phpfox::getUserId() . ' ';
        //in case we encounter a post form, we know it is a search request
        if ($iSearchId = Phpfox::getLib('request')->get('search-id')) {
            $sKeyword = Phpfox::getService('contest.helper')->getSearchKeyword($iSearchId);
            $sConds .= $this->database()->search($sType = 'like%', $mField = array('title'), $sSearch = $sKeyword);
        }

        // by default we only get items of current user
        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($sConds)
            ->execute('getSlaveField');


        $aItems = $this->database()->select('*')
            ->from($this->_sTable)
            ->where($sConds)
            ->limit($iPage, $iLimit, $iCnt)
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');

        return array($iCnt, $aItems);
    }

    public function getItemFromFox($iItemId)
    {
        if (!$iItemId) {
            return false;
        }

        $aItem = $this->database()->select('* ')
            ->from($this->_sTable)
            ->where('photo_id = ' . $iItemId)
            ->execute('getSlaveRow');

        // change path to make it more general when generating images
        $aItem['destination'] = 'photo' . PHPFOX_DS . $aItem['destination'];

        return $aItem;
    }

    public function getTemplateViewPath()
    {
        return 'contest.entry.content.photo';
    }


    public function getDataToInsertIntoEntry($iItemId, $iSourceId = 2)
    {
        $aItem = $this->getItemFromFox($iItemId);
        $sFullSourcePath = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aItem['server_id']
        ,
            'path' => 'core.url_pic'
        ,
            'file' => $aItem['destination']
        ,
            'suffix' => ''
        ,
            'return_url' => true
        ));

        if (setting('pf_cdn_enabled') && !Phpfox::getParam('core.keep_files_in_server') && !file_exists($sFullSourcePath)) {
            $p = PHPFOX_DIR_FILE . PHPFOX_DS . 'pic' . PHPFOX_DS . 'contest' . PHPFOX_DS;
            if (!is_dir($p)) {
                if (!@mkdir($p, 0777, 1)) {
                }
            }
            $sImage = $sFullSourcePath;

            $sNewImageFullPath = $this->processImage(
                $sImage
                , Phpfox::getUserId()
                , Phpfox::getParam('core.dir_pic') . 'contest/'
            );
            $sOriginalDes = sprintf($sNewImageFullPath, '');
            $oImage = Phpfox::getLib('image');
            $sImagePath = str_replace(Phpfox::getParam('core.dir_pic'), '', $sNewImageFullPath);
            foreach ($this->_aImageSize as $iSize) {
                $oImage->createThumbnail($sOriginalDes, sprintf($sNewImageFullPath, '_' . $iSize), $iSize, $iSize);
            }
            Phpfox::getLib('cdn')->put($sOriginalDes);
            @unlink($sOriginalDes);
        } else {
            $sFullSourcePath = Phpfox::getParam('core.dir_pic') . $aItem['destination'];
            $sSuffix = file_exists(sprintf($sFullSourcePath, '')) ? '' : '_1024';
            $sImagePath = Phpfox::getService('contest.entry.process')->copyImageToContest($sFullSourcePath, $sSuffix,
                $this->_aImageSize);
        }
        // column name here must comply with column in db
        $aReturn = array(
            'image_path' => $sImagePath,
            'blog_content' => "",
            'blog_content_parsed' => "",
            'item_id' => 0,
            'total_attachment' => 0,
        );

        return $aReturn;
    }

    public function processImage($sImgUrl, $iUserId, $dir)
    {
        $oFile = Phpfox::getLib('file');

        $sFileName = md5($iUserId . PHPFOX_TIME . uniqid());
        $sFileDir = $oFile->getBuiltDir($dir);
        $ext = $this->getExt($sImgUrl);
        $sFilePath = $sFileDir . $sFileName . '%s.' . $ext;

        $result = $this->fetchImage($sImgUrl, sprintf($sFilePath, ''));
        if ($result === false) {
            return false;
        }

        $iFileSize = filesize(sprintf($sFilePath, ''));

        if ($iFileSize) {
            return $sFilePath;
        }

        return false;
    }

    public function fetchImage($photo_url, $tmpfile)
    {
        $timeout = 60;
        $fp = fopen($tmpfile, 'w+');
        # start curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $photo_url);
        # set return transfer to false
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_REFERER,
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . Phpfox::getParam('core.host') . '/');
        # increase timeout to download big file
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        # write data to local file
        curl_setopt($ch, CURLOPT_FILE, $fp);
        # execute curl
        curl_exec($ch);
        # close curl
        curl_close($ch);
        # close local file
        fclose($fp);

        if (filesize($tmpfile) > 0) {
            return true;
        }
        return false;
    }

    public function getExt($file)
    {
        $path_parts = pathinfo($file);

        if (isset($path_parts['extension'])) {
            // to prevent some path: .jpg?c=6d03, .png?param1=abc, ...
            // we will check and return exactly extension
            $extension = strtolower($path_parts['extension']);

            if ($extension == '') {
                return 'jpg';
            } else {
                return strtolower($extension);
            }


        }
    }

    public function getDataFromFoxAdaptedWithContestEntryData($iItemId, $iSourceId = 2)
    {
        $aItem = $this->getItemFromFox($iItemId);

        $aItem['image_path'] = $aItem['destination'];
        return $aItem;
    }


}