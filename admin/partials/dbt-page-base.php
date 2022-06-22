<?php
/**
 * Il template della pagina amministrativa
 *
 * @package    database-table
 * @subpackage database-table/admin
 * 
 * @var String $render_content Il file dentro partial da caricare 
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="wrap">
   
    <div id="dbt_container" class="dbt-grid-container" style="display:none; position:fixed; width: inherit;">
        <div class="dbt-column-content">
            <?php require (dirname(__FILE__).$render_content); ?>
        </div>
        <div class="dbt-column-tables-list" id="dbt_column_sidebar">
            <?php require (dirname(__FILE__)."/dbt-partial-sidebar.php"); ?>
        </div>
        <div id="dbt_sidebar_popup" class="dbt-sidebar-popup">
            <div id="dbt_dbp_title" class="dbt-dbp-title">
                <div id="dbt_dbp_close" class="dbt-dbp-close" onclick="dbt_close_sidebar_popup()">&times;</div>
            </div>
            <div id="dbt_dbp_loader" ><div class="dbt-sidebar-loading"><div  class="dbt-spin-loader"></div></div></div>
            <div id="dbt_dbp_content" class="dbt-dbp-content"></div>
        </div>
    </div>
   
</div>
<?php require (dirname(__FILE__)."/../js/database-table-footer-script.php");