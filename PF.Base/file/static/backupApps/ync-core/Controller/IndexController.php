<?php

namespace Apps\YNC_Core\Controller;

use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class IndexController
 * @package Apps\YNC_Core\Controller
 */
class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
    }
}
