<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

{if isset($aCompany.canEditCompany) && $aCompany.canEditCompany}
<li><a
		onclick="location.href='{url link='jobposting.company.add' id=$aCompany.company_id}';return false;"
		href="{url link='jobposting.company.add' id=$aCompany.company_id}">{phrase var='edit_company_info'}</a></li>



{if isset($aCompany.canBuyPackages) && $aCompany.canBuyPackages}
<li><a
		onclick="location.href='{url link='jobposting.company.add.packages' id=$aCompany.company_id}';return false;"
		href="{url link='jobposting.company.add.packages' id=$aCompany.company_id}">{phrase var='view_bought_packages'}</a></li>
{/if}

{if isset($aCompany.canSubmissionForm) && $aCompany.canSubmissionForm}
<li><a
		onclick="location.href='{url link='jobposting.company.add.form' id=$aCompany.company_id}';return false;"
		href="{url link='jobposting.company.add.form' id=$aCompany.company_id}">{phrase var='edit_submission_form'}</a></li>
{/if}


<li><a
		onclick="location.href='{url link='jobposting.company.add.jobs' id=$aCompany.company_id}';return false;"
		href="{url link='jobposting.company.add.jobs' id=$aCompany.company_id}">{phrase var='manage_job_posted'}</a></li>


{/if}

{if isset($aCompany.canApproveCompany) && $aCompany.canApproveCompany}
<li><a href="#" onclick="$.ajaxCall('jobposting.approveCompany', 'id={$aCompany.company_id}', 'GET'); return false;">{phrase var='approve'}</a></li>
{/if}

{if isset($aCompany.canSponsorCompany) && $aCompany.canSponsorCompany}
<li><a href="#" onclick="$.ajaxCall('jobposting.sponsorCompany', 'id={$aCompany.company_id}', 'GET'); return false;">{phrase var='sponsor'}</a></li>
{/if}

{if isset($aCompany.canunSponsorCompany) && $aCompany.canunSponsorCompany}
<li><a href="#" onclick="$.ajaxCall('jobposting.unsponsorCompany', 'id={$aCompany.company_id}', 'GET'); return false;">{phrase var='un_sponsor'}</a></li>
{/if}

{if isset($aCompany.canActivatedCompany) && $aCompany.canActivatedCompany}
	<li><a href="#" onclick="$.ajaxCall('jobposting.activatedCompany', 'id={$aCompany.company_id}', 'GET'); return false;">{phrase var='activate'}</a></li>
{/if}
{if isset($aCompany.canDectivatedCompany) && $aCompany.canDectivatedCompany}
	<li><a href="#" onclick="$.ajaxCall('jobposting.deactivatedCompany', 'id={$aCompany.company_id}', 'GET'); return false;">{phrase var='deactivate'}</a></li>
{/if}



{if isset($aCompany.canDeleteCompany) && $aCompany.canDeleteCompany}
<li class="item_delete">
    <a title="{phrase var='delete'}" onclick="$Core.jsConfirm({l}{r},function(){l}$.ajaxCall('jobposting.deleteCompany', '&company_id={$aCompany.company_id}', 'GET');{r});" href="javascript:void(0)" > {phrase var='delete'} </a>
</li>
{/if}
