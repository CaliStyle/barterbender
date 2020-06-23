<?php
if (Phpfox::isModule('contactimporter'))
{
	Phpfox::getService('contactimporter') -> setUserHasInvited($iId);
}
