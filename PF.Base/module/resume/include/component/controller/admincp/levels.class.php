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
class Resume_Component_Controller_Admincp_Levels extends Phpfox_Component {

    /**
     * Process method which is used to process this component
     * @see Resume_Service_Level_Level
     */
    public function process()
    {
        $oLevel = phpFox::getService('resume.level');

        // Get level list
        $aLevelList = $oLevel->getLevels();

        // Assign variables for layout
        $this->template()->assign(array(
            'aLevelList' => $aLevelList
        ));

        // Set page header
        $this->template()->setHeader(array(
            'resume_backend.css' => 'module_resume',
            'quick_edit.js' => 'static_script',
            'manage_level.js' => 'module_resume',
        ))->setHeader('cache', array(
            'drag.js' => 'static_script',
            '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'resume.manageOrdering\'}); }</script>'
                )
        );

        // Set page phrase for jscript call
        $this->template()->setPhrase(array(
            'resume.are_you_sure'
        ));

        // Set breadcrumb
        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
            ->setBreadCrumb(_p('resume.admin_menu_manage_levels'), $this->url()->makeUrl('admincp.resume.levels'));

        // Delete selected levels
        if ($aTask = $this->request()->getArray('task'))
        {
            if ($aTask[0] == 'do_delete_selected')
            {
                foreach ($this->request()->getArray('level_row') as $iLevelId)
                {
                    phpFox::getService('resume.level.process')->delete($iLevelId);
                }
                $this->url()->send('admincp.resume.levels', null, _p('resume.delete_process_is_completed_note_that_some_levels_cannot_be_deleted_if_they_are_in_used'));
            }
        }
    }

}

?>
	