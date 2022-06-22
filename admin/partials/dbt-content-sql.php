<?php
/**
 * Scrive una query
 * 
 * Per il rendering delle tabelle chiama: dirname(__FILE__)."/ddbt-content-table-without-filter.php" 
 * 
 * @var Boolean $ajax_continue 
 * @var Array $info
 * @var $queries

 * @package    database-table
 * @subpackage database_tables/admin
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
  
</div>
<div class="dbt-content-table js-id-dbt-content" >
    <form id="table_filter" method="post" action="<?php echo admin_url("admin.php?page=database_tables"); ?>">
        <input type="hidden" name="section" value="table-browse">
        <input type="hidden" name="action_query" value="custom_query">
        <?php Dbt_html_sql::render_sql_from($table_model, true); ?>
    </form>
</div>
    