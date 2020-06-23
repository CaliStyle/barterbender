<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <b>{phrase var='advancedmarketplace.listing_statistics'}
        </div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_listings'}:</b>
            {$aListingStatistics.total_listings}
        </div>
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_available_listings'}:</b>
            {$aListingStatistics.available_listings}
        </div>
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_closed_listings'}:</b>
            {$aListingStatistics.closed_listings}
        </div>
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_draft_listings'}:</b>
            {$aListingStatistics.draft_listings}
        </div>
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_approved_listings'}:</b>
            {$aListingStatistics.approved_listings}
        </div>
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_featured_listings'}:</b>
            {$aListingStatistics.featured_listings}
        </div>
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_sponsored_listings'}:</b>
            {$aListingStatistics.sponsored_listings}
        </div>
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_reviews'}:</b>
            {$aListingStatistics.total_reviews}
        </div>
        <div class="form-group">
            <b>{phrase var='advancedmarketplace.total_reviewed_listings'}:</b>
            {$aListingStatistics.total_reviewed_listings}
        </div>
    </div>
</div>
