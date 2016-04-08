<?php

namespace Gini\Module;

class Summary
{
    public static function setup()
    {
        \Gini\I18N::setup();
        
        date_default_timezone_set(\Gini\Config::get('system.timezone') ?: 'Asia/Shanghai');

        class_exists('\Gini\Those');
    }

}



    