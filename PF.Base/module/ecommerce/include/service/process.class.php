<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Process extends Phpfox_Service
{

    private $_bIsPublished = false;

	public function __construct()
	{
		
	}
	
    public function deleteGlobalSetting()
    {
        $this->database()->delete(Phpfox::getT('ecommerce_global_setting'), 'TRUE');
    }

    public function addGlobalSetting($aDefaultSetting, $aActualSetting)
    {
        $sDefaultSetting = json_encode($aDefaultSetting);
        $sActualSetting = json_encode($aActualSetting);
        
        $id = $this->database()->insert(Phpfox::getT("ecommerce_global_setting"), array(
            "default_setting" => $sDefaultSetting,
            "actual_setting" => $sActualSetting
        ));

        return $id;
    }

    public function add($aVals,$sType){

        $oFilter = Phpfox::getLib('parse.input');
        $bHasAttachments = false;

        $aCurrentCurrencies = Phpfox::getService('ecommerce.helper')->getCurrentCurrencies();
        $unitCurrencyFee = $aCurrentCurrencies[0]['currency_id'];

        $oImage = Phpfox::getLib('image');
        $oFile = Phpfox::getLib('file');
        $bHasImage = false;
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            $aImage = $oFile->load('image', array('jpg','gif','png'), (Phpfox::getUserParam('ecommerce.max_size_for_icons') === 0 ? null : (Phpfox::getUserParam('ecommerce.max_size_for_icons') / 1024)));
            if (!Phpfox_Error::isPassed()){
                return false;
            }

            if ($aImage === false)
            {
                return Phpfox_Error::set(_p('please_select_an_image_to_upload'));
            }

            $bHasImage = true;
        }

        $aInsert = array(
            'name' => $aVals['name'],
            'user_id' => Phpfox::getUserId(),
            'product_creating_type' => $sType,
            'uom_id' => isset($aVals['uom'])?intval($aVals['uom']):0,
            'theme_id' => 1,
            'product_creation_datetime' => PHPFOX_TIME,
            'product_modification_datetime' => PHPFOX_TIME,
            'product_status' => 'draft',

            'feature_day' => (int)$aVals['feature_number_days'],
            'feature_fee' => !empty($aVals['feature_fee']) ? $aVals['feature_fee'] : 0,
            'feature_start_time' => 0, //will check later
            'feature_end_time' => 0, //will check later

            'creating_item_fee' => !empty($aVals['creating_item_fee']) ? $aVals['creating_item_fee'] : 0,
            'creating_item_currency' => $unitCurrencyFee,

            'start_time' => $aVals['start_time'],
            'end_time'   => $aVals['end_time'],
            'actual_end_time' => $aVals['end_time'],

            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'privacy_photo' => (isset($aVals['privacy_photo']) ? $aVals['privacy_photo'] : '0'),
            'privacy_video' => (isset($aVals['privacy_video']) ? $aVals['privacy_video'] : '0'),

            'total_comment' => 0,
            'total_view' => 0,
            'total_like' => 0,
            'total_dislike' => 0,
            'total_watch' => 0,
            'total_review' => 0,
            
            'module_id' => (isset($aVals['module_id']) ? $aVals['module_id'] : $sType),
            'item_id' => (isset($aVals['item_id']) ? $aVals['item_id'] : '0'),

            'product_quantity' => $aVals['quantity'],
            'product_quantity_main' => $aVals['quantity'],
            'product_price' => (isset($aVals['product_price']) && $aVals['product_price'] > 0) ? $aVals['product_price'] : 0,
        );

        $iProductId = $this->database()->insert(Phpfox::getT('ecommerce_product'), $aInsert);

        // insert image 
        if ($bHasImage)
        {
            $this->upload($iProductId, $aVals, $oFile, $oImage);
        }

        $sFileName = $this->database()->select('logo_path')->from(Phpfox::getT('ecommerce_product'))->where('product_id = ' . $iProductId)->execute('getSlaveField');

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $sFileName['logo_path'] = 'ynecommerce/' . $aFile['path'];
                $sFileName['server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                $this->database()->update(Phpfox::getT('ecommerce_product'),array('server_id' => $sFileName['server_id'], 'logo_path'=> $sFileName['logo_path']),'product_id='.$iProductId);
            }
        }

        //insert text 
        $sDescription = $oFilter->clean($aVals['description']);
        $sDescription_parse = $oFilter->prepare($aVals['description']);

        $sShipping = $oFilter->clean($aVals['shipping']);
        $sShipping_parse = $oFilter->prepare($aVals['shipping']);

        $aInsertText = array(
            'product_id' => $iProductId,
            'description' => $sDescription,
            'description_parsed'           => $sDescription_parse,
            'shipping'        => $sShipping,
            'shipping_parsed' => $sShipping_parse,
        );

        $this->database()->insert(Phpfox::getT('ecommerce_product_text'), $aInsertText);
    
        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iProductId);
        }


        // insert customfield_user
        if(isset($aVals['customfield_user_title']) && count($aVals['customfield_user_title']) > 0){
            foreach($aVals['customfield_user_title'] as $key => $val){
                if(strlen(trim($val)) > 0){
                    $aInsertUserCustomField = array(
                        'product_id' => $iProductId,
                        'usercustomfield_title' => $this->cleanTextWithStripTag($aVals['customfield_user_title'][$key]),
                        'usercustomfield_content' => $this->cleanTextWithStripTag($aVals['customfield_user_content'][$key]),
                        'usercustomfield_content_parsed' => $oFilter->prepare($aVals['customfield_user_content'][$key]),
                    );

                    $this->database()->insert(Phpfox::getT('ecommerce_product_usercustomfield'), $aInsertUserCustomField);
                }
            }
        }

        // insert category
        if (isset($aVals['categories']) && count($aVals['categories']))
        {
            $this->database()->delete(Phpfox::getT('ecommerce_category_data'), 'product_id = ' . (int) $iProductId.' and product_type ="'.$sType.'"');

            foreach ($aVals['categories'] as $key => $iCategoryId)
            {
                $data = array('product_id' => $iProductId, 'category_id' => $iCategoryId, 'product_type' => $sType);
                $data['is_main'] = ($key == (count($aVals['categories']) - 1)) ? 1 : 0;
                $this->database()->insert(Phpfox::getT('ecommerce_category_data'), $data);
            }
        }

        // insert custom field by category 
        if(isset($aVals['custom']) && count($aVals['custom']) > 0){
            Phpfox::getService('ecommerce.custom.process')->addValue($aVals['custom'],$iProductId, $sType);
        }


        // insert tag 
        if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
        {
            Phpfox::getService('tag.process')->add($sType, $iProductId, Phpfox::getUserId(), $aVals['tag_list']);
        }


        return $iProductId;

    }


    public function upload($iProductId, $aVals, $oFile, $oImage)
    {
        $sFileName = $this->database()->select('logo_path')->from(Phpfox::getT('ecommerce_product'))->where('product_id = ' . $iProductId)->execute('getSlaveField');

        // calculate space used
        if (!empty($sFileName))
        {
            // check if the file exists and get its size
            if (file_exists(Phpfox::getParam('core.dir_pic'). 'ynecommerce/' . sprintf($sFileName, '')))
            {
                $oFile->unlink(Phpfox::getParam('core.dir_pic') . 'ynecommerce/' . sprintf($sFileName, ''));
                $aSize = array(50, 100, 120, 200, 400);
                foreach($aSize as $iSize)
                {
                    $oFile->unlink(Phpfox::getParam('core.dir_pic') . 'ynecommerce/' . sprintf($sFileName, '_' . $iSize));
                    $oFile->unlink(Phpfox::getParam('core.dir_pic') . 'ynecommerce/' . sprintf($sFileName, '_' . $iSize . '_square'));
                }
            }
        }

        if (is_bool($iProductId)) $iProductId = (int)$aVals['product_id'];

        $sFileName = $oFile->upload('image', Phpfox::getParam('core.dir_pic') . 'ynecommerce/', $iProductId);


        // update the product
        $this->database()->update(Phpfox::getT('ecommerce_product')
            , array(
                'logo_path' => 'ynecommerce/' . $sFileName, 
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'), 
            )
            , 'product_id = ' . $iProductId);
        // now the thumbnails
        $aSizes = array(50, 100, 120, 200, 400);
        foreach ($aSizes as $iSize)
        {
            $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "ynecommerce/" . sprintf($sFileName, ''), Phpfox::getParam('core.dir_pic') . "ynecommerce/" . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
            $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "ynecommerce/" . sprintf($sFileName, ''), Phpfox::getParam('core.dir_pic') . "ynecommerce/" . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
        }

        $this->database()->update(Phpfox::getT('ecommerce_product'), array('server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')), 'product_id = ' . (int) $iProductId);

        $sImageCopy = Phpfox::getParam('core.dir_pic') . "ynecommerce/" . sprintf($sFileName, '_cover');

        if($oFile->copy(Phpfox::getParam('core.dir_pic') ."ynecommerce/" . sprintf($sFileName, ''), $sImageCopy)){
            /*get file name and update many cover-photos*/
            $sImageDirCopy = sprintf($sFileName, '_cover%s');
            $this->processImagesCoverPhotos($iProductId,$sImageDirCopy);
        }

    }


    public function processImagesCoverPhotos($iId,$sFileName)
    {

        $aSize = array(100, 120, 200, 400);
        $aType = array('jpg', 'gif', 'png');

        $oImage = Phpfox::getLib('image');

        $iFileSizes = 0;
        $sDirImage = Phpfox::getParam('core.dir_pic').'ynecommerce/';


        $iFileSize = filesize($sDirImage.sprintf($sFileName, ''));
        $iFileSizes += $iFileSize;



        list($width, $height, $type, $attr) = getimagesize($sDirImage.sprintf($sFileName, ''));

        foreach($aSize as $iSize)
        {
            if ($iSize == 50 || $iSize == 120)
            {
                if ($width < $iSize || $height < $iSize)
                {
                    $this->resizeImage($sFileName, $width > $iSize ? $iSize : $width, $height > $iSize ? $iSize : $height, '_'.$iSize);
                }
                else
                {
                    $this->resizeImage($sFileName, $iSize, $iSize, '_'.$iSize);
                }
            }
            else
            {
                $oImage->createThumbnail($sDirImage.sprintf($sFileName, ''), $sDirImage.sprintf($sFileName, '_'.$iSize), $iSize, $iSize);
            }

            $iFileSizes += filesize($sDirImage.sprintf($sFileName, '_'.$iSize));

        }

        $this->database()->insert(Phpfox::getT('ecommerce_product_image'), array(
            'product_id' => $iId,
            'image_path' => 'ynecommerce/' . $sFileName,
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'ordering' => 0,
            'is_profile' => 0,
            'file_size' => $iFileSize,
            'extension' => pathinfo($sDirImage.sprintf($sFileName, ''), PATHINFO_EXTENSION),
            'width' => $width,
            'height' => $height,
        ));

        
    }

    public function close($iProductId){
            $aUpdate = array(
                            'end_time'         => PHPFOX_TIME,
                            'actual_end_time' => PHPFOX_TIME,
                            'product_status'    => 'completed'
                            );
            return $this->database()->update(Phpfox::getT('ecommerce_product'), $aUpdate ,'product_id = '.(int)$iProductId);

    }
    public function update($aVals,$iProductId,$sType){

        $oFilter = Phpfox::getLib('parse.input');
        $bHasAttachments = false;

        $aEditedProduct = Phpfox::getService('ecommerce')->getQuickProductById($iProductId);
            
        $aCurrentCurrencies = Phpfox::getService('ecommerce.helper')->getCurrentCurrencies();
        $unitCurrencyFee = $aCurrentCurrencies[0]['currency_id'];

        $oImage = Phpfox::getLib('image');
        $oFile = Phpfox::getLib('file');
        $bHasImage = false;
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            $aImage = $oFile->load('image', array('jpg','gif','png'), (Phpfox::getUserParam('ecommerce.max_size_for_icons') === 0 ? null : (Phpfox::getUserParam('ecommerce.max_size_for_icons') / 1024)));            
            if (!Phpfox_Error::isPassed()){
                return false;
            }
            $bHasImage = true;
        }

        $sName = $oFilter->clean(strip_tags($aVals['name']), 255);

        $aUpdate = array(
            'name' => $sName,
            'uom_id' => intval($aVals['uom']),
            'product_modification_datetime' => PHPFOX_TIME,

            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'privacy_photo' => (isset($aVals['privacy_photo']) ? $aVals['privacy_photo'] : '0'),
            'privacy_video' => (isset($aVals['privacy_video']) ? $aVals['privacy_video'] : '0'),

            'product_quantity' => $aVals['quantity'],

            'product_quantity_main' => $aVals['quantity'],
            'product_price' => (isset($aVals['product_price']) && $aVals['product_price'] > 0) ? $aVals['product_price'] : 0,
        );
        if($sType == 'ynsocialstore_product')
        {
            $aUpdate['product_quantity'] = $aVals['quantity_remain'];
        }

        $aVals['feature_number_days'] = (int) $aVals['feature_number_days'];

        /*not in feature or feature is expired*/
        if(isset($aEditedProduct['feature_end_time']) && $aEditedProduct['feature_end_time'] < PHPFOX_TIME ){
            $aUpdate['feature_day'] = $aVals['feature_number_days'];
        }
        else{/*already featured ,wanna expand feature time*/

            $aUpdate['feature_day'] = $aEditedProduct['feature_day'] + $aVals['feature_number_days'];
        }

        if($aEditedProduct['product_status'] != 'running'){
              $aUpdate['start_time'] = $aVals['start_time'];
              $aUpdate['end_time'] = $aVals['end_time'];
              $aUpdate['actual_end_time'] = $aVals['end_time'];
              $aUpdate['creating_item_currency'] = $unitCurrencyFee;
        }

        $this->database()->update(Phpfox::getT('ecommerce_product'), $aUpdate ,'product_id = '.(int)$iProductId);

        // insert image 
        if ($bHasImage)
        {
            $this->upload($iProductId, $aVals, $oFile, $oImage);
        }

        $sFileName = $this->database()->select('logo_path')->from(Phpfox::getT('ecommerce_product'))->where('product_id = ' . (int)$iProductId)->execute('getSlaveField');
        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $sFileName['logo_path'] = 'ynecommerce/' . $aFile['path'];
                $sFileName['server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                $this->database()->update(Phpfox::getT('ecommerce_product'),array('server_id' => $aFile['server_id'], 'logo_path'=> 'ynecommerce/' . $aFile['path']),'product_id='.(int)$iProductId);
            }
        }

        // update  text 
        $sDescription = $oFilter->clean($aVals['description']);
        $sDescription_parse = $oFilter->prepare($aVals['description']);

        $sShipping = $oFilter->clean($aVals['shipping']);
        $sShipping_parse = $oFilter->prepare($aVals['shipping']);

        $aUpdateText = array(
            'description' => $sDescription,
            'description_parsed' => $sDescription_parse,
            'shipping'        => $sShipping,
            'shipping_parsed' => $sShipping_parse,
        );


        $this->database()->update(Phpfox::getT('ecommerce_product_text'), $aUpdateText,'product_id = '.(int)$iProductId);

    
        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iProductId);
        }


        // update customfield_user
        if(isset($aVals['customfield_user_title']) && count($aVals['customfield_user_title']) > 0){

            $this->database()->delete(Phpfox::getT('ecommerce_product_usercustomfield'), 'product_id = ' . (int) $iProductId);

            foreach($aVals['customfield_user_title'] as $key => $val){
                if(strlen(trim($val)) > 0){
                    $aUpdateUserCustomField = array(
                        'product_id' => $iProductId,
                        'usercustomfield_title' => $this->cleanTextWithStripTag($aVals['customfield_user_title'][$key]),
                        'usercustomfield_content' => $this->cleanTextWithStripTag($aVals['customfield_user_content'][$key]),
                        'usercustomfield_content_parsed' => $oFilter->prepare($aVals['customfield_user_content'][$key]),
                    );

                    $this->database()->insert(Phpfox::getT('ecommerce_product_usercustomfield'), $aUpdateUserCustomField);
                }
            }
        }

        // insert category
        if (isset($aVals['categories']) && count($aVals['categories']))
        {
            $this->database()->delete(Phpfox::getT('ecommerce_category_data'), 'product_id = ' . (int) $iProductId.' and product_type ="'.$sType.'"');

            foreach ($aVals['categories'] as $key => $iCategoryId)
            {
                $data = array('product_id' => $iProductId, 'category_id' => $iCategoryId,'product_type' => $sType);
                $data['is_main'] = ($key == (count($aVals['categories']) - 1)) ? 1 : 0;
                $this->database()->insert(Phpfox::getT('ecommerce_category_data'), $data);
            }
        }

        // insert custom field by category 
        if(isset($aVals['custom']) && count($aVals['custom']) > 0){
            
            $this->database()->delete(Phpfox::getT('ecommerce_custom_value'), 'product_id = ' . (int) $iProductId.' and product_type ="'.$sType.'"');

            Phpfox::getService('ecommerce.custom.process')->updateValue($aVals['custom'],$iProductId,$sType);
        }


        // insert tag 
        if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
        {
            Phpfox::getService('tag.process')->update($sType, $iProductId, Phpfox::getUserId(), $aVals['tag_list']);
        }


        return $iProductId;

    }

    public function addInvoice($iId, $sCurrency, $sCost, $sType, $data = array(), $sItemType = 'auction')
    {
        $iInvoiceId = $this->database()->insert(Phpfox::getT('ecommerce_invoice'), array(
                'item_id' => $iId,
                'type' => $sType,
                'item_type' => $sItemType,
                'user_id' => Phpfox::getUserId(),
                'currency_id' => $sCurrency,
                'price' => $sCost,
                'time_stamp' => PHPFOX_TIME, 
                'invoice_data' => json_encode($data), 
                'pay_type' => trim($data['pay_type'], '|'), 
            )

        );

        return $iInvoiceId; 
    }


    public function updateProductFeatureTime($iProductId, $iStartTime, $iEndTime,$feature_days,$feature_fee){
        
        $this->database()->update(Phpfox::getT('ecommerce_product')
            , array(
                    'feature_start_time' => (int)$iStartTime, 
                    'feature_end_time'   => (int)$iEndTime,
                    'feature_day'        => (int)$feature_days,
                    'feature_fee'        => (int)$feature_fee
                   ),
                     'product_id = ' . $iProductId );
    }

    public function updateProductStatus($iProductId, $iStatus){
        $this->database()->update(Phpfox::getT('ecommerce_product')
            , array('product_status' => $iStatus), 'product_id = ' . $iProductId);
    }

    public function approveProduct($iProductId, $aItem = null,$sType,$isAdmin = false){
        if($aItem === null){
            $aItem = Phpfox::getService('ecommerce')->getProductForEdit($iProductId, true);
        }

        #note#
        // create feed
        $aCallback = ((!empty($aItem['module_id']) && $aItem['module_id'] != $aItem['product_creating_type'] && $aItem['product_creating_type'] != 'ynsocialstore_product') ? Phpfox::getService('ecommerce')->getProductAddCallback($aItem['item_id']) : null);
        if(Phpfox::getService('ecommerce.helper')->isHavingFeed($aItem['product_creating_type'], $iProductId) == false){
            if($sType != 'ynsocialstore_product')
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->allowGuest()->add($aItem['product_creating_type'], $aItem['product_id'], $aItem['privacy'], (isset($aItem['privacy_comment']) ? (int) $aItem['privacy_comment'] : 0), (isset($aItem['item_id']) ? (int) $aItem['item_id'] : 0), $aItem['user_id']) : null);
            else{
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback('ynsocialstore.getFeedDetails', $aItem['item_id']))->add('ynsocialstore_product', $iProductId, $aItem['privacy'], (isset($aItem['privacy_comment']) ? (int) $aItem['privacy_comment'] : 0), $aItem['item_id']) : null);
            }
            if(!$isAdmin && $sType == 'ynsocialstore_product') {
                // plugin call
                (($sPlugin = Phpfox_Plugin::get('ynsocialstore.service_product_process_approveProduct__end')) ? eval($sPlugin) : false);
            }
            // Update user activity
            Phpfox::getService('user.activity')->update($aItem['user_id'], $aItem['product_creating_type'], '+');            
        }
        $featureFee = 0;
        $publishFee = 0;

        switch ($sType) {
            case 'auction':
                    $featureFee = doubleval(((int)$aItem['feature_day'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id'])));
                    $publishFee = doubleval((Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id'])));
                break;
            case 'ynsocialstore_product':
                $aStore = Phpfox::getService('ynsocialstore')->getStoreById($aItem['item_id']);
                $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);

                $featureFee = doubleval(((int)$aItem['feature_day'] * $aPackage['feature_product_fee']));
                //update total_products
                $this->database()->updateCounter('ynstore_store', 'total_products', 'store_id', $aItem['item_id']);
                break;
            default:
                # code...
                break;
        }
        //update status
        $this->database()->update(Phpfox::getT('ecommerce_product'), array('product_approved_datetime' => PHPFOX_TIME),'product_id = ' . (int)$iProductId.' AND product_creating_type ="'.$sType.'"');
        
        //update feature time

        if( $aItem['feature_day'] > 0 ){

            $start_time = $aItem['start_time'];
                        
            if($start_time < PHPFOX_TIME){/*in available time of auction*/   
                $start_feature_time = PHPFOX_TIME;
            }
            else{/*start time of auction in future*/
                $start_feature_time = $start_time;
            }   

            $end_feature_time = $start_feature_time + ((int)$aItem['feature_day'] * 86400); 
           
           if($end_feature_time >= 4294967295){
                $end_feature_time = 4294967295;
            }
            
            Phpfox::getService('ecommerce.process')->updateProductFeatureTime($iProductId, $start_feature_time, $end_feature_time,(int)$aItem['feature_day'],$featureFee);
     
        }
        /*send email to owner business*/
        if($sType == 'auction'){
            
            $iReceiveId = $aItem['user_id'];
            $aUser = Phpfox::getService('user')->getUser($iReceiveId);
            $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
            $email = $aUser['email'];
            $aExtraData = array();

            $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction','auction_has_been_approved' , $language_id ,  $iReceiveId, $iProductId, $aExtraData);
            Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
            Phpfox::getService('notification.process')->add('auction_approve',$iProductId, $aItem['user_id']);
        }
        if($sType == 'ynsocialstore_product'){
            //Send mail
            if($aItem['user_id'] != Phpfox::getUserId()) {
                $sSiteName = Phpfox::getParam('core.site_title');
                $sLink = Phpfox_Url::instance()->permalink('ynsocialstore.product', $aItem['product_id'], $aItem['name']);
                $sSubject = _p('your_product_in_site_name_site_is_approved', ['site_name' => $sSiteName]);
                $sText = _p('your_product_product_name_is_approved_by_sender_for_more_detail_please_view_this_link_link', ['product_name' => $aItem['name'], 'sender' => Phpfox::getService('ynsocialstore')->getUserFullName(Phpfox::getUserId()), 'link' => $sLink]);
                Phpfox::getService('ynsocialstore.process')->sendMail($aItem['user_id'], $sText, $sSubject);
            }
            $aFollowers = Phpfox::getService('ynsocialstore.following')->getAllFollowingByStoreId($aItem['item_id']);
            if($aFollowers) {
                $aStore = Phpfox::getService('ynsocialstore')->getStoreById($aItem['item_id'], true);
                $storeLink = Phpfox_Url::instance()->permalink('ynsocialstore.store', $aStore['store_id'], $aStore['name']);
                $productLink  = Phpfox_Url::instance()->permalink('ynsocialstore.product', $aItem['product_id'],$aItem['name']);
                if(empty($aItem['logo_path']))
                {
                    $productImage = Phpfox::getParam('core.path_actual').'PF.Base/module/ynsocialstore/static/image/product_default.png';
                }
                else
                {
                    $productImage = Phpfox::getParam('core.path_file').'file/pic/'.$aItem['logo_path'];
                }
                $sSubject = _p('store_name_sell_a_new_product', ['store_name' => $aItem['name']]);
                $sText = _p('sell_new_product_text', ['store_link' => $storeLink,'store_name' => $aStore['name'],'product_link' => $productLink,'product_image' => $productImage,'product_name' => $aItem['name'],'product_price' => $aItem['product_price'].'&nbsp;'.$aItem['creating_item_currency']]);

                foreach ($aFollowers as $aFollower) {
                    $ownerEmail = Phpfox::getService('ynsocialstore')->getOwnerEmail($aFollower['user_id']);
                    $aVal = array(
                        'email_message' => Phpfox::getLib('parse.input')->prepare($sText),
                        'email_subject' => $sSubject,
                        'product_id' => $iProductId,
                        'receivers' => serialize($ownerEmail),
                        'is_sent' => 0,
                        'time_stamp' => PHPFOX_TIME
                    );
                    Phpfox::getService('ecommerce.mail.process')->saveEmailToQueue($aVal);
                    Phpfox::getService("notification.process")->add("ynsocialstore_sellproduct", $aItem['product_id'], $aFollower['user_id'], Phpfox::getUserId());
                }
            }
            Phpfox::getService('notification.process')->add('ynsocialstore_approveproduct',$iProductId, $aItem['user_id']);
        }
        return true;

    }

    public function cleanTextWithStripTag($sText){
        $oFilter = Phpfox::getLib('parse.input');

        return $oFilter->clean(strip_tags($sText));

    }
    
    public function updateCoverPhotos($aVals, $sType)
    {
        $iProductId = $aVals['product_id'];
        $aFiles = $this->processImages($iProductId, $sType);

        // Handle to update order of cover photos
        $this->updateOrderCoverPhotos($aVals);

        if (is_array($aFiles)) {
            if ($aFiles['error']) {
                return $aFiles;
            }
            $aSql['image_path'] = $aFiles['image_path'];
            $aSql['server_id'] = $aFiles['server_id'];
        }
    }

    public function updateOrderCoverPhotos($aVals)
    {
        if (isset($aVals['photo-order']) && count($aVals['photo-order'])) {
            foreach ($aVals['photo-order'] as $iBus => $iOrder) {
                $this->database()->update(Phpfox::getT('ecommerce_product_image')
                    , array(
                        'ordering' => (int)$iOrder,
                    ),
                    'image_id = ' . $iBus);
            }
        }
    }

    public function processImages($iId,$sType)
    {
        
        $aSize = array(100, 120, 200, 400);
        $aType = array('jpg', 'gif', 'png');

        $oImage = Phpfox::getLib('image');
        $oFile = Phpfox::getLib('file');
        $iFileSizes = 0;
        $sDirImage = Phpfox::getParam('core.dir_pic').'ynecommerce/';
    
        $aResult = array();

        $iMaxUploadSize = 500;
        switch ($sType) {
            case 'auction':
				
				//get global settings
				$aGlobalSetting = Phpfox::getService('auction')->getGlobalSetting();
				
				//get size cover
				if(isset($aGlobalSetting['actual_setting']['max_upload_size_cover_photos']))
				{
					$iMaxUploadSize = $aGlobalSetting['actual_setting']['max_upload_size_cover_photos'];
				}
				else 
				{
					$iMaxUploadSize = 500;
				}
				
                break;

            default:
                $iMaxUploadSize = Phpfox::getUserParam('ecommerce.max_size_for_icons');
                break;
        }
        
        foreach ($_FILES['image']['error'] as $iKey => $sError)
        {
                
            if ($sError == UPLOAD_ERR_OK)
            {
                
                if ($aImage = $oFile->load('image['.$iKey.']', $aType, ($iMaxUploadSize === 0 ? null : ($iMaxUploadSize / 1024))))
                {
                    $sFileName = Phpfox::getLib('file')->upload('image['.$iKey.']', $sDirImage, $iId);
                    

                    $iFileSize = filesize($sDirImage.sprintf($sFileName, ''));
                    $iFileSizes += $iFileSize;
    

                    
                    list($width, $height, $type, $attr) = getimagesize($sDirImage.sprintf($sFileName, ''));
                    
                    foreach($aSize as $iSize)
                    {
                        if ($iSize == 50 || $iSize == 120)
                        {
                            if ($width < $iSize || $height < $iSize)
                            {
                                $this->resizeImage($sFileName, $width > $iSize ? $iSize : $width, $height > $iSize ? $iSize : $height, '_'.$iSize);
                            }
                            else
                            {
                                $this->resizeImage($sFileName, $iSize, $iSize, '_'.$iSize);
                            }
                        }
                        else
                        {
                            $oImage->createThumbnail($sDirImage.sprintf($sFileName, ''), $sDirImage.sprintf($sFileName, '_'.$iSize), $iSize, $iSize);
                        }
                        
                        $iFileSizes += filesize($sDirImage.sprintf($sFileName, '_'.$iSize));
                    }

                $this->database()->insert(Phpfox::getT('ecommerce_product_image'), array(
                        'product_id' => $iId,
                        'image_path' => 'ynecommerce/' . $sFileName,
                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                        'ordering' => 0,
                        'is_profile' => 0,
                        'file_size' => $iFileSize,
                        'extension' => pathinfo($sDirImage.sprintf($sFileName, ''), PATHINFO_EXTENSION),
                        'width' => $width,
                        'height' => $height,
                        'product_type' => $sType,
                    ));

                }
                else {
                    $aResult = array('error' => 1,'message' => _p('some_photos_you_uploaded_is_invalid_type_or_exceed_limited_size'));
                }
            }
        }

        
        if ($iFileSizes === 0)
        {
        
            return array('error' => 1,'message' => _p('some_photos_you_uploaded_is_invalid_type_or_exceed_limited_size'));
        }


        if(!count($aResult)){
            return array(
                'error'     => 0,
                'file_size' => $iFileSizes, 
                'image_path' => $sFileName, 
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
            );    
        }else{
            return $aResult;
        }
        
    }

        /**
     * Resize Image
     * @todo improve performance
     */
    public function resizeImage($sFilePath, $iThumbWidth, $iThumbHeight, $sSubfix)
    {
        $sRealPath = Phpfox::getParam('core.dir_pic').'ynecommerce'.PHPFOX_DS;
        
        #Resize to Width/Height
        list($iWidth, $iHeight, $sType, $sAttr) = getimagesize($sRealPath . sprintf($sFilePath, ''));
        $iNewWidth = $iWidth;
        $iNewHeight = $iHeight;
        $fSourceRatio = $iWidth / $iHeight;
        $fThumbRatio = $iThumbWidth / $iThumbHeight;
        if($fSourceRatio > $fThumbRatio)
        {
            $iNewHeight = $iThumbHeight;
            $fRatio = $iNewHeight / $iHeight;
            $iNewWidth = $iWidth * $fRatio;
        }
        else
        {
            $iNewWidth = $iThumbWidth;
            $fRatio = $iNewWidth / $iWidth;
            $iNewHeight = $iHeight * $fRatio;                            
        }

        $sDestination = $sRealPath . sprintf($sFilePath, $sSubfix);
        $sTemp1 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp1');
        $sTemp2 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp2');
        $sTemp3 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp3');
        
        Phpfox::getLib("image")->createThumbnail($sRealPath . sprintf($sFilePath, ""), $sTemp1, $iNewWidth, $iNewHeight, true, false);

        #Crop the resized image
        if($iNewWidth > $iThumbWidth)
        {
            $iX = ceil(($iNewWidth - $iThumbWidth)/2);
            Phpfox::getLib("image")->cropImage($sTemp1, $sTemp2, $iThumbWidth, $iThumbHeight, $iX, 0, $iThumbWidth);
        }
        else
        {
            @copy($sTemp1, $sTemp2);
        }
        
        if($iNewHeight > $iThumbHeight)
        {
            $iY = ceil(($iNewHeight - $iThumbHeight)/2);
            Phpfox::getLib("image")->cropImage($sTemp2, $sTemp3, $iThumbWidth, $iThumbHeight, 0, $iY, $iThumbWidth);
        }
        else
        {
            @copy($sTemp2, $sTemp3);
        }
        
        @copy($sTemp3, $sDestination);

        Phpfox::getLib('cdn')->put($sDestination);

        @unlink($sTemp1);
        @unlink($sTemp2);
        @unlink($sTemp3);
    }

    public function deleteImage($iImageId)
    {
        $aSuffix = array('','_100', '_120', '_200', '_400');

        $aImage = $this->database()->select('di.image_id, di.image_path, di.server_id')
            ->from(Phpfox::getT('ecommerce_product_image'), 'di')
            ->where('di.image_id = '.$iImageId)
            ->execute('getSlaveRow');
        
        if (!$aImage)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_image'));
        }
        
        $iFileSizes = 0;
        foreach ($aSuffix as $sSize)
        {
            $sImage = Phpfox::getParam('core.dir_pic').'ynecommerce/'.sprintf($aImage['image_path'], $sSize);
            if (file_exists($sImage))
            {
                $iFileSizes += filesize($sImage);
                @unlink($sImage);
            }
        }
        /*
        if ($iFileSizes > 0)
        {
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'directory', $iFileSizes, '-');
        }*/
        
        return $this->database()->delete(Phpfox::getT('ecommerce_product_image'), 'image_id = '.$aImage['image_id']);
    }

    public function updateThemeForEcommerce($aVals){
            $this->database()->update(Phpfox::getT('ecommerce_product')
            , array(
                    'theme_id' => $aVals['theme'], 
                   ),
            'product_id = ' . (int)$aVals['product_id']);

            return true;

    }

    public function removeCart($iRemoveCartId){
        return $this->database()->delete(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_id = '.(int)$iRemoveCartId);

    }

    public function saveAddressUser($aData,$iAddressId = 0){
        if($iAddressId){
              $this->database()->update(Phpfox::getT('ecommerce_address')
                        , array(
                            'address_user_name' => $aData['contact_name'] ,
                            'address_customer_country_iso'         => $aData['country_iso'] ,
                            'address_customer_country_child_id'    => isset($aData['country_child_id'])?$aData['country_child_id']:0 ,
                            'address_customer_street'    => $aData['address_street'] ,
                            'address_customer_street_2'  => $aData['address_street_2'] ,
                            'address_customer_city'      => $aData['address_city'] ,
                            'address_customer_postal_code'       => $aData['address_postal_code'] ,
                            'address_customer_country_code'      => $aData['address_country_code'] ,
                            'address_customer_city_code'         => $aData['address_city_code'] ,
                            'address_customer_phone_number'      =>  $aData['address_phone_number'] ,
                            'address_customer_mobile_number'     => $aData['address_mobile_number'] ,
                        ),
                        'address_id = ' . (int)$iAddressId);
              return true;
        }
        else{
            return $this->database()->insert(Phpfox::getT('ecommerce_address'), array(
                    'address_user_id' => Phpfox::getUserId(),
                    'address_type' => 'buyer' ,
                    'address_user_name' => $aData['contact_name'] ,
                    'address_customer_country_iso'         => $aData['country_iso'] ,
                    'address_customer_country_child_id'    => isset($aData['country_child_id'])?$aData['country_child_id']:0 ,
                    'address_customer_street'    => $aData['address_street'] ,
                    'address_customer_street_2'  => $aData['address_street_2'] ,
                    'address_customer_city'      => $aData['address_city'] ,
                    'address_customer_postal_code'       => $aData['address_postal_code'] ,
                    'address_customer_country_code'      => $aData['address_country_code'] ,
                    'address_customer_city_code'         => $aData['address_city_code'] ,
                    'address_customer_phone_number'      =>  $aData['address_phone_number'] ,
                    'address_customer_mobile_number'     => $aData['address_mobile_number'] ,
                ));
        }
    }

    public function updateQuantityProduct($iCartId,$iProductId,$iQuantity){
             $this->database()->update(Phpfox::getT('ecommerce_cart_product')
                        , array(
                            'cartproduct_quantity' => $iQuantity ,
                        ),
                        'cartproduct_product_id = ' . (int)$iProductId.' AND cartproduct_cart_id = ' . (int)$iCartId);
              return true;
    }

    public function updateProductQuantity($iProductId,$iQuantity,$sType = 'auction'){

             $this->database()->update(Phpfox::getT('ecommerce_product')
                        , array(
                            'product_quantity' => $iQuantity ,
                        ),
                        'product_id = ' . (int)$iProductId);
             if($iQuantity <=0 && $sType == 'auction'){
                    $this->close($iProductId);
             }
              return true;
    }

    public function handleAfterPayment($aParams){

    }

    public function buyItNow($iProductId){

        $aCart = Phpfox::getService('ecommerce.cart')->get(Phpfox::getUserId());
        if (!$aCart)
        {
            $iCartId = Phpfox::getService('ecommerce.cart.process')->add(array('user_id' => Phpfox::getUserId()));
            $aCart = array(
                'cart_id' => $iCartId,
                'cart_user_id' => Phpfox::getUserId(),
                'cart_creation_datetime' => PHPFOX_TIME,
                'cart_modification_datetime' => 0
            );
        }
        $aCartProduct = Phpfox::getService('ecommerce.cart')->getProductsByProductId(Phpfox::getUserId(),$iProductId, 'buy');


        if(empty($aCartProduct)){
             $aProduct =  Phpfox::getService('ecommerce')->getQuickProductById($iProductId);
             if($aProduct['product_creating_type'] == 'auction'){
                $aProduct =  Phpfox::getService('auction')->getAuctionById($iProductId);
                $fPrice = $aProduct['auction_item_buy_now_price'];
            }
             $aVals = array(
                'cart_id' => $aCart['cart_id'],
                'product_id' => $iProductId,
                'quantity' => 1,
                'product_data' => $aProduct,
                'price' => $fPrice,
                'type' => 'buy',
                'currency' => $aProduct['creating_item_currency']
            );

            return Phpfox::getService('ecommerce.cart.process')->addProducts($aVals);
        }
        
        return true;
    
    }   

    public function deny($iDenyId)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('product_status' => 'denied'), 'product_id = ' . (int) $iDenyId);
    }

    public function delete($iProductId)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('product_status' => 'deleted'), 'product_id = ' . (int) $iProductId);
    }
    public function deleteAddress ($iAddressId){
        return $this->database()->delete(Phpfox::getT('ecommerce_address'), 'address_id = '.(int)$iAddressId);
    }

}
?>