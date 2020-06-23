<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Block_Top_inviter extends Phpfox_Component
{
	public function process()
	{
		
		$iLimit = $this->getParam('limit',10);
		if (!(int)$iLimit) {
		    return false;
        }
		$aCond = "pi.provider != 'manual'";
		$topinviter = phpfox::getLib('phpfox.database') 
			-> select('pu.user_name,pu.full_name,pu.email as inviter_email,pi.user_id,pi.provider, SUM(pi.total) as number_invitation')
			-> from(phpfox::getT('contactimporter_contact'), 'pi')
			-> leftJoin(phpfox::getT('user'), 'pu', 'pu.user_id = pi.user_id')
			-> group('pi.user_id') -> where($aCond)
			-> order('number_invitation DESC') -> limit($iLimit) -> execute('getRows');
		
		if (!$topinviter)
		{
			return false;
		}
		
		$this -> template() -> assign(array(
			'sHeader' => _p('top_inviters'), //_p('top-user'),
			'sDeleteBlock' => 'dashboard',
			'contactimporter.js' => 'module_contactimporter',
			'topinviter' => $topinviter,
		));
		
		return 'block';
	}
    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Top Inviters Limit'),
                'description' => _p('Define the limit of how many user can be displayed on this block. Set 0 will hide this block.'),
                'value' => 10,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Top Inviters Limit" must be greater than or equal to 0'
            ]
        ];
    }
}