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
    <div id="dbt_container" style="display:none">
        <?php
        $table_bulk_ok = (@$table_model->table_status() != 'CLOSE' && count($table_model->get_pirmaries()) > 0 && $post->post_content['delete_params']->allow);
        ?>
        <div class="dbt-content-table js-id-dbt-content" >
            <h1 class="wp-heading-inline"><?php echo $list_title; ?></h1>
            <span class="page-title-action" onclick="dbt_edit_details_v2()"><?php  _e('Add New', 'database_tables') ;?></span>
            <form id="table_filter" method="post" action="<?php echo admin_url("admin.php?page=".$_REQUEST['page']); ?>">

                <?php if ($table_model->last_error === false) : ?>
                    <?php 
                    $max_input_vars = (int)Dbt_fn::get_max_input_vars();
                    ?>
                    <div class="dtf-table-footer-admin-menu">
                        <div class="tablenav-pages dtf-table-footer-left">
                            <div class="alignleft actions bulkactions">
                                <select id="dbt_bulk_action_selector_bottom">
                                <option value="-1"><?php _e('Bulk actions', 'database_tables'); ?></option>
                                    <option value="download" class="hide-if-no-js"><?php _e('Download'); ?></option>
                                    <?php if  ($table_bulk_ok) : ?>
                                    <option value="delete"><?php _e('Delete'); ?></option>
                                    <?php endif; ?>
                                </select>
                                <input id="dbt_bulk_on_selector_bottom" type="hidden" value="checkboxes" >
                                <div class="button" onclick="dbt_bulk_actions()"><?php _e('Apply'); ?></div>
                            </div>
                        </div>
                     
                        <br class="clear">
                    </div>
                <?php endif; ?>

                <textarea style="display:none" id="sql_query_executed"><?php echo esc_textarea($table_model->get_current_query()); ?></textarea>
                <textarea style="display:none" id="sql_query_edit"><?php echo esc_textarea($table_model->get_default_query()); ?></textarea>
                <input type="hidden" name="page"  value="<?php echo esc_attr($_REQUEST['page']); ?>">
                <input type="hidden" name="action_query" id="dtf_action_query"  value="">
                <input type="hidden" id="dtf_table_sort_field" name="filter[sort][field]" value="<?php echo Dbt_fn::esc_request('filter.sort.field'); ?>">
                <input type="hidden" id="dtf_table_sort_order"  name="filter[sort][order]" value="<?php echo Dbt_fn::esc_request('filter.sort.order'); ?>">
                <input type="hidden" id="dtf_table_filter_limit_start" name="filter[limit_start]" value="<?php echo Dbt_fn::esc_request($table_model->limit_start); ?>">
                
                <?php if ($table_model->last_error == false && $table_model->total_items > 0) : ?>
                    <div class="tablenav top dbt-tablenav-top">
                        <span class="displaying-num">Show <?php echo count($table_items) -1; ?> of <?php echo $table_model->total_items; ?> items</span>
                        <span class="" >Element per page: </span>
                        <input type="number" name="filter[limit]" id="Element_per_page" class="dtf-pagination-input" value="<?php echo absint($table_model->limit); ?>" style="width:3.4rem; padding-right:0;" min="1" max="500">
                        <div name="change_limit_start" class="button action dtf-pagination-input"  onclick="dtf_submit_table_filter('change_limit')" >Apply</div>
                        <?php Dbt_fn::get_pagination($table_model->total_items, $table_model->limit_start, $table_model->limit); ?>
                        <div id="dbt-bnt-clear-filter-query" class="button"  onclick="dbt_clear_filter()"><?php _e('Clear Filter','database_tables'); ?></div>
                    
                        <br class="clear">
                    </div>
                <?php endif; ?>
                <?php echo $html_content; ?>
            </form>
            <div class="clear"></div>
            <br><br>
            <?php if ($table_model->last_error === false) : ?>
               
                    <div class="tablenav-pages">
                        <?php Dbt_fn::get_pagination($table_model->total_items, $table_model->limit_start, $table_model->limit); ?>
                    </div>
                    <br class="clear">
            
            <?php endif; ?>


            <?php 
            $list_of_tables_js = [];
            $list_of_tables = Dbt_fn::get_table_list();
            foreach ($list_of_tables['tables'] as $lot) {
                $list_of_tables_js[] = $lot;
            }
            ?>
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
<br><br>