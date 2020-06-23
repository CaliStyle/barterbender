<?php
if ($aMenu['url'] == 'resume.add' && !Phpfox::getUserParam('resume.can_create_resumes')) {
    unset($aMenus[$iKey]);
}