<?php
/**
 * Database table
 * 
 *
 * @package          Database tables
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Database tables
 * Plugin URI:        https://github.com/giuliopanda/database_tables
 * Description:       Database Tables is a tool designed to manage the administration and publication of new MySQL tables.
 * Version:           0.8.1
 * Requires at least: 5.9
 * Requires PHP:      7.2
 * Author:            Giulio Pandolfelli
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: 	  database_tables
 * Domain Path: 	  /languages
 */
namespace DatabaseTables;

if (!defined('WPINC')) die;
define('database_tables_VERSION', '0.8.1');

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

define('DBT_DIR',   __DIR__ ."/");

require_once(DBT_DIR . "includes/dbt-loader.php");
require_once(DBT_DIR . "includes/dbt-functions.php");
require_once(DBT_DIR . "includes/dbt-functions-import.php");
require_once(DBT_DIR . "includes/dbt-functions-structure.php");
require_once(DBT_DIR . "includes/dbt-list-functions.php");
require_once(DBT_DIR . "includes/dbt-functions-items-setting.php");
require_once(DBT_DIR . "includes/dbt-shortcode.php");
require_once(DBT_DIR . "includes/dbt-facade.php");
require_once(DBT_DIR . "includes/pinacode/pinacode-init.php");


// frontend
//require_once(DBT_DIR . "includes/dbt-html-table-frontend.php");
require_once(DBT_DIR . "includes/dbt-html-search-frontend.php");


