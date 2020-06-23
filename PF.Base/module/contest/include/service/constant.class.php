<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Constant extends Phpfox_service {

	public function __construct()
	{
		$this->_aErrorStatus = array(
			'invalid_permission' => array(
				'code' => 1,
				'phrase' => '' . _p('contest.invalid_permission')
			),
			'contest_pending' => array(
				'code' => 2,
				'phrase' => '' . _p('contest.contest_pending')
			),
		);
	}

	public function getAllErrorStatus()
	{
		return $this->_aErrorStatus;
	}
	
	/**
	 * 
	 * @return error status code and phrase if having or false
	 */
	public function getErrorStatusNumber($sName)
	{
		if(isset($this->_aErrorStatus[$sName]))
		{
			return $this->_aErrorStatus[$sName]['code'];
		}

		return false;
	}


	private $_aContestType = array(
			'blog' => array(
				'id' => 1,
				'name' => 'blog'
				),
			'photo' => array(
				'id' => 2,
				'name' => 'photo'
				),
			'video' => array(
				'id' => 3,
				'name' => 'video'
				),
            'music' => array(
                'id' => 4,
                'name' => 'music'
                ),
		);


	/**
	  * return type_id int
	  */
	public function getContestTypeIdByTypeName($sTypeName)
	{
		if(isset($this->_aContestType[$sTypeName]))
		{
			return $this->_aContestType[$sTypeName]['id'];
		}
		else
		{
			return false;
		}

	}

	public function getContestTypeNameByTypeId($iTypeId)
	{
		foreach($this->_aContestType as $aContestType)
		{
			if($aContestType['id'] == $iTypeId)
			{
				return $aContestType['name'];
			}
		}

		return false;

	}

	public function getAllContestTypes()
	{
		return $this->_aContestType;
	}

	private $_aContestStatuses = array(
			'draft' => array(
				'id' => 1,
				'name' => 'draft'
				),
			'pending' => array(
				'id' => 2,
				'name' => 'pending'
				),
			'denied' => array(
				'id' => 3,
				'name' => 'denied'
				),
			'on_going' => array(
				'id' => 4,
				'name' => 'on_going'
				),
			'closed' => array(
				'id' => 5,
				'name' => 'closed'
				)
		);
	public function getContestStatusIdByStatusName($sStatusName)
	{
		if(isset($this->_aContestStatuses[$sStatusName]))
		{
			return $this->_aContestStatuses[$sStatusName]['id'];
		}
		else
		{
			return false;
		}

	}

	public function getContestStatusNameByStatusId($iStatusId)
	{
		foreach($this->_aContestStatuses as $aContestStatus)
		{
			if($aContestStatus['id'] == $iStatusId)
			{
				return $aContestStatus['name'];
			}
		}

		return false;

	}

	public function getAllContestStatus()
	{
		return $this->_aContestStatuses;
	}


	private $_sYnAddParamForNavigateBack = 'yncontestid';

	public function getYnAddParamForNavigateBack()
	{
		return $this->_sYnAddParamForNavigateBack;
	}



	private $_aTransactionStatus = array(
			'initialized' => array(
				'id' => 1,
				'name' => 'initialized'
				),
			'pending' => array(
				'id' => 2,
				'name' => 'pending'
				),
			'success' => array(
				'id' => 3,
				'name' => 'success'
				)

		);


	public function getTransactionStatusIdByStatusName($sStatusName)
	{
		if(isset($this->_aTransactionStatus[$sStatusName]))
		{
			return $this->_aTransactionStatus[$sStatusName]['id'];
		}
		else
		{
			return false;
		}

	}

	public function getTransactionStatusNameByStatusId($iStatusId)
	{

		foreach($this->_aTransactionStatus as $aTransactionStatus)
		{
			if($aTransactionStatus['id'] == $iStatusId)
			{
				return $aTransactionStatus['name'];
			}
		}

		return false;

	}

	public function getAllTransactionStatuses()
	{
		return $this->_aTransactionStatus;
	}


	/**
	 * 
	 * remember every entry having a corresponding phrase with prefix email_template_
	 * @var array
	 */
	
	private $_aEmailTemplateTypes = array(
			'create_contest_successfully' => array(
				'id' => 1,
				'name' => 'create_contest_successfully'
				),
			'thanks_participant' => array(
				'id' => 2,
				'name' => 'thanks_participant'
				),
			'thanks_for_submitting_entry' => array(
				'id' => 3,
				'name' => 'thanks_for_submitting_entry'
				),
			'contest_closed' => array(
				'id' => 4,
				'name' => 'contest_closed'
				),
			'contest_approved' => array(
				'id' => 5,
				'name' => 'contest_approved'
				),
			'contest_denied' => array(
				'id' => 6,
				'name' => 'contest_denied'
				),
			'invite_friend_letter' => array(
				'id' => 7,
				'name' => 'invite_friend_letter'
				),
			'entry_denied' => array(
				'id' => 8,
				'name' => 'entry_denied'
				),
			'invite_friend_view_entry_letter' => array(
				'id' => 9,
				'name' => 'invite_friend_view_entry_letter'
				),
			'inform_winning_entries' => array(
				'id' => 10,
				'name' => 'inform_winning_entries'
				)

		);

	public function getEmailTemplateTypeIdByTypeName($sTypeName)
	{
		if(isset($this->_aEmailTemplateTypes[$sTypeName]))
		{
			return $this->_aEmailTemplateTypes[$sTypeName]['id'];
		}
		else
		{
			return false;
		}

	}

	public function getAllEmailTemplateTypesWithPhrases()
	{
		$aTemplates = $this->_aEmailTemplateTypes;
		foreach ($aTemplates as &$aVal) {
			$aVal['phrase'] = _p('contest.email_template_' . $aVal['name']);
		}

		return $aTemplates;
	}

	public function getNumberOfItemsPerPageInAddEntryForm()
	{
		return 9;
	}

	private $_aInviteType = array(
			'contest' => array(
				'id' => 1,
				'name' => 'contest'
				),
			'entry' => array(
				'id' => 2,
				'name' => 'entry'
				)
		);


	public function getInviteTypeIdByName($sName)
	{
		if(isset($this->_aInviteType[$sName]))
		{
			return $this->_aInviteType[$sName]['id'];
		}
		else
		{
			return false;
		}

	}


	private $_aBadgeStatus = array(
		'photo' => array(
				'id' => 1,
				'name' => 'photo'
				),
		'description' => array(
				'id' => 2,
				'name' => 'description'
				),
		'both' => array(
				'id' => 3,
				'name' => 'both'
				),
		'none' => array(
				'id' => 4,
				'name' => 'none'
				)
	);

	public function getBadgeStatusIdByName($sName)
	{
		if(isset($this->_aBadgeStatus[$sName]))
		{
			return $this->_aBadgeStatus[$sName]['id'];
		}
		else
		{
			return false;
		}

	}

	public function getAllBadgeStatus()
	{
		return $this->_aBadgeStatus;

	}

	public function getUneditableStatus()
	{
		return array(
			Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('pending')	,
			Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'),
			Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed')
		);	
	}

	public function getDeletableStatus()
	{
		return array(
			Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('draft')	,
			Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed'),
		);	
	}

	private $_aEntryStatus = array(
			'pending' => array(
				'id' => 0,
				'name' => 'pending'
				),
			'approved' => array(
				'id' => 1,
				'name' => 'approved'
				),
			'denied' => array(
				'id' => 2,
				'name' => 'denied'
				)

		);
	public function getEntryStatusIdByStatusName($sStatusName)
	{
		if(isset($this->_aEntryStatus[$sStatusName]))
		{
			return $this->_aEntryStatus[$sStatusName]['id'];
		}
		else
		{
			return false;
		}

	}

    private $_aTimeLine = array('begin_time', 'start_time', 'stop_time', 'start_vote', 'stop_vote', 'end_time');
    
    public function getTimeLine()
    {
        return $this->_aTimeLine;
    }	
}