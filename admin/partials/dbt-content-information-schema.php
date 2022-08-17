<?php
/**
 * Il template della pagina amministrativa
 * Lo spazio dei grafici è impostato qui, e poi verrà disegnato in javascript
 * l'html del setup e del resize bulk invece è caricato sui due html a parte
 * 
 * @since      1.1.0
 *
 * @package    database-table
 * @subpackage bulk-image-resizer/admin
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
</div>

<div class="dbt-content-table js-id-dbt-content" >
   <div class="dbt-content-margin">

    <h2 class="dbt-h2-inline dbt-content-margin">DATABASE: <?php echo $wpdb->dbname; ?></h2>
    <a class="dbt-submit" href="<?php echo add_query_arg(['section'=>'table-structure','action'=>'structure-edit','dbt_id'=>''], admin_url("admin.php?page=database_tables")); ?>"><?php _e('Create new table'); ?></a>
    <hr>

    <form id="table_filter" method="post" action="<?php echo admin_url("admin.php"); ?>">
        <input type="hidden" name="page"  value="database_tables">
        <input type="hidden" name="action_query" id="dtf_action_query"  value="">
        <input type="hidden" id="dtf_table_sort_field" name="filter[sort][field]" value="<?php echo Dbt_fn::esc_request('filter.sort.field'); ?>">
        <input type="hidden" id="dtf_table_sort_order"  name="filter[sort][order]" value="<?php echo Dbt_fn::esc_request('filter.sort.order'); ?>">
        <?php $html_table->render($table_model->items, false); ?>
    </form>
    
    </div>
</div>
<script>
 /**
  * Mostra nasconde il label del prefisso della tabella
  * @param DOM el 
  * @param String id 
  */
function dbt_use_prefix(el, id) {
    if (jQuery(el).is(':checked')) {
        jQuery('#'+id).css('visibility','visible');
    } else {
        jQuery('#'+id).css('visibility','hidden');
    }
}
</script>