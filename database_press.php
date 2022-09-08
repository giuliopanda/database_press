<?php
/**
 * Database table
 * 
 *
 * @package          Database Press
 *
 * @wordpress-plugin
 * Plugin Name:       Database Press
 * Plugin URI:        https://github.com/giuliopanda/database_press
 * Description:       Database Press is a tool designed to manage the administration and publication of new MySQL tables.
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.2
 * Author:            Giulio Pandolfelli
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: 	  database_press
 * Domain Path: 	  /languages
 */
namespace DatabasePress;

if (!defined('WPINC')) die;
define('database_press_VERSION', '1.0.0');

define('dbp_DIR',   __DIR__ ."/");

require_once(dbp_DIR . "includes/dbp-loader.php");
require_once(dbp_DIR . "includes/dbp-functions.php");
require_once(dbp_DIR . "includes/dbp-functions-import.php");
require_once(dbp_DIR . "includes/dbp-functions-structure.php");
require_once(dbp_DIR . "includes/dbp-list-functions.php");
require_once(dbp_DIR . "includes/dbp-functions-items-setting.php");
require_once(dbp_DIR . "includes/dbp-shortcode.php");
require_once(dbp_DIR . "includes/dbp-facade.php");
require_once(dbp_DIR . "includes/pinacode/pinacode-init.php");


// frontend
//require_once(dbp_DIR . "includes/dbp-html-table-frontend.php");
require_once(dbp_DIR . "includes/dbp-html-search-frontend.php");


/**
 * Activate the plugin.
 */
function dbp_activate($h) { 
    //echo "Grazie per aver installato questo plugin!";
    update_option( '_dbp_activete_info', ['date'=>date('Y-m-d'), 'voted'=>'no'], false );
}
\register_activation_hook( __FILE__, '\DatabasePress\dbp_activate' );
