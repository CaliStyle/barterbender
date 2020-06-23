<?php


function ynd_install402p2()
{
    db()->delete(':directory_comparison', ['comparison_id' => '11']);
}

ynd_install402p2();