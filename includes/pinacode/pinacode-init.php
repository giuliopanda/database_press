<?php
/**
 * Plugin Name: PinaCode
 * Description: Alternative shortcode system
 * Version: 1.0
 * Author: Giulio Pandolfelli
 * 
 */
namespace DatabaseTables;
require_once(dirname(__FILE__) . "/pina-class.php");
require_once(dirname(__FILE__) . "/pina-functions.php");
require_once(dirname(__FILE__) . "/pina-functions-parse-query.php");
require_once(dirname(__FILE__) . "/pina-logical-math.php");
require_once(dirname(__FILE__) . "/pina-registry.php");
require_once(dirname(__FILE__) . "/pina-actions.php");
require_once(dirname(__FILE__) . "/pina-actions2.php");

require_once(dirname(__FILE__) . "/pina-attributes.php");
require_once(dirname(__FILE__) . "/pina-attributes-wrap.php");
require_once(dirname(__FILE__) . "/pina-filter.php");
require_once(dirname(__FILE__) . "/pina-errors.php");
//require_once(dirname(__FILE__) . "/pina-test.php");
  
 
/**
 * TEST
 */
/*
PinaCode::set_var('item', 'FOO') ;
echo PinaCode::get_registry()->short_code('io [%item]');
die;
*/
