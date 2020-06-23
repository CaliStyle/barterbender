<?php 
;

if (Phpfox::isModule('directory'))
{
    $module = $this->request()->get('module');
    $item = $this->request()->get('item');
    if($module == 'directory'){
        $this->template()
            ->assign(array(
                'yndirectory_module' => $module,
                'yndirectory_item' => $item,
            )
        );

        // Check if login as page
        if (Phpfox::getUserBy('profile_page_id') > 0) {
            $aBusiness = Phpfox::getService('directory')->getBusinessById($item);
            if ($aBusiness['module_id'] == 'pages') {
                Phpfox::getService('pages')->setIsInPage();
            }
        }
    }
}

;
?>