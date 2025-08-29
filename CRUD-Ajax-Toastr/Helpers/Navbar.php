<?php

if (! function_exists('active_nav')) {
    function active_nav($path)
    {
        return request()->is($path . '*') ? 'active' : '';
    }
}

if (! function_exists('open_nav_list')) {
    function open_nav_list($path)
    {
        return request()->is($path . '*') ? 'menu-open' : '';
    }
}
