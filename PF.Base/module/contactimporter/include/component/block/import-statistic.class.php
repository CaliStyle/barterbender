<?php
defined('PHPFOX') or die('NO DICE!');
class Contactimporter_Component_Block_Import_Statistic extends Phpfox_Component
{
    public function process()
    {
        list ($iTotal, $aStatistics) = Phpfox::getService('contactimporter.contact')->getStatistic();
        $this->template()->assign(array(
            'aStatistics' => $aStatistics,
            'iTotal' => $iTotal,
        ));
        return 'block';
    }
  
    public function clean()
    {
        $this->template()->clean(array(
            'aStatistics', 'iTotal',
        ));
    }
}
?>