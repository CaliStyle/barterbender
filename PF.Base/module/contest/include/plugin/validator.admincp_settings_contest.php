<?php

$aValidation['ending_soon_before'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => '"Ending Soon before (days)" must be greater than or equal to 0',
];
$aValidation['number_of_contest_block_home_page'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => '"Number of Contests on Premium Contests, Top Contests, Popular Contests Block" must be greater than or equal to 0',
];
$aValidation['number_of_contest_featured_block_home_page'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => '"Number of Contests on Featured Contest Slideshow" must be greater than or equal to 0',
];
$aValidation['number_of_entries_block_most_voted_most_viewed'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => 'Number of Entries in "Most Voted Entries" and "Most Viewed Entries" blocks must be greater than or equal to 0',
];
$aValidation['contest_featured_slideshow_speed'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => 'Featured slideshow speed must be greater than or equal to 0',
];