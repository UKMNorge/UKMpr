<?php
require_once('UKM/monstring.class.php');
UKMpr::addViewData('monstring', new monstring_v2( get_option('pl_id') ) );