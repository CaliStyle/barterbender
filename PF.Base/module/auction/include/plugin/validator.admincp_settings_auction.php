<?php

$aValidation = [
    'category_number' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Categories" must be greater than 0'),
    ],
    'refresh_time' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Refresh Time Period must be greater than 0'),
    ],
    'max_items_block_buyers_who_viewed_this_item_also_viewed' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Buyers Who Viewed This Item Also Viewed" must be greater than 0'),
    ],
    'max_items_block_other_auctions_from_this_seller' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Other Auctions From This Seller" must be greater than 0'),
    ],
    'max_items_block_auctions_you_may_like' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Auctions You May Like" must be greater than 0'),
    ],'max_items_block_auctions_bidden_by_my_friends' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Auctions My Friends Bid on" must be greater than 0'),
    ],'max_items_block_most_liked_auctions' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Most Liked Auctions" must be greater than 0'),
    ],
    'max_items_block_upcoming_auctions' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Upcoming Auctions" must be greater than 0'),
    ],
    'max_items_block_auctions_ending_today' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Auctions Ending Today" must be greater than 0'),
    ],
    'max_items_block_todays_live_auctions' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Today\'s Live Auctions" must be greater than 0'),
    ],
    'max_items_sub_categories_list_display' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on Sub-categories list display must be greater than 0'),
    ],
    'max_items_block_featured_auctions' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Featured Auctions" must be greater than 0'),
    ],
    'max_items_block_weekly_hot_auctions' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on block "Hot Auctions" must be greater than 0'),
    ],'max_number_of_items_on_my_bidden_history_popup' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on "My Bidden History" popup must be greater than 0'),
    ],
    'max_number_of_items_on_my_offer_history_popup' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('Number of items on "My Offer History" popup must be greater than 0'),
    ],

];
