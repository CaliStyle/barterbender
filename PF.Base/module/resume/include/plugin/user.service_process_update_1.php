<?php
if (Phpfox::isModule('resume'))
{
	Phpfox::GetService('resume.basic.process')->updateFullName($aVals);
}
?>