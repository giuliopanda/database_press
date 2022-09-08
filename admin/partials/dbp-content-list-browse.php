<?php 
/**
 * L'elenco dei dati di una lista 
 * 
 * @var Class $dbp function
 * @var Array $table_items
 * @var Array $list_of_tables

 * @var database_press_model_base $table_model  
 */
namespace DatabasePress;
if (!defined('WPINC')) die;

$table_bulk_ok = ($table_model->table_status() != 'CLOSE' && count($table_model->get_pirmaries()) > 0 && $post->post_content['delete_params']->allow);
?>
<div class="dbp-content-header">
    <?php require(dirname(__FILE__).'/dbp-partial-tabs.php'); ?>
    
</div>
 <div class="dbp-content-table js-id-dbp-content" >
    <?php 
    if ($table_bulk_ok) {
        $append = '<span class="page-title-action" onclick="dbp_edit_details_v2()">' . __('Add new content', 'database_press') . '</span>';
    } else {
        $append = '';
    }
    if (dbp_fn::echo_html_title_box('list', $list_title, $description, '',  $msg_error, $append)) :
        wp_enqueue_media();
        ?>
        <form id="table_filter" method="post" action="<?php echo admin_url("admin.php?page=dbp_list&section=list-browse&dbp_id=".$id); ?>">
            <textarea style="display:none" id="sql_query_executed"><?php echo esc_textarea($table_model->get_current_query()); ?></textarea>
            <textarea style="display:none" id="sql_query_edit"><?php echo esc_textarea($table_model->get_default_query()); ?></textarea>
            <input type="hidden" name="page"  value="dbp_list">
            <input type="hidden" name="action_query" id="dbp_action_query"  value="">
            <input type="hidden" id="dbp_table_sort_field" name="filter[sort][field]" value="<?php echo dbp_fn::esc_request('filter.sort.field'); ?>">
            <input type="hidden" id="dbp_table_sort_order"  name="filter[sort][order]" value="<?php echo dbp_fn::esc_request('filter.sort.order'); ?>">
            <input type="hidden" id="dbp_table_filter_limit_start" name="filter[limit_start]" value="<?php echo dbp_fn::esc_request($table_model->limit_start); ?>">
           
            <?php if ($table_model->last_error == false && $table_model->total_items > 0) : ?>
                
                <div class="tablenav top dbp-tablenav-top">

                    <p class="search-box">   
                        <input type="search" id="dbp_full_search" name="search" value="<?php echo esc_attr(stripslashes(dbp_fn::get_request('search'))); ?>">
                        <span class="button" onclick="dbp_submit_table_filter('search');">Search</span>
                        &nbsp; 
                    </p>
                    <span class="displaying-num">Show <?php echo count($table_items) -1; ?> of <?php echo $table_model->total_items; ?> items</span>
                    <span class="" >Element per page: </span>
                    <input type="number" name="filter[limit]" id="Element_per_page" class="dbp-pagination-input" value="<?php echo absint($table_model->limit); ?>" style="width:3.4rem; padding-right:0;" min="1" max="500">
                    <div name="change_limit_start" class="button action dbp-pagination-input"  onclick="dbp_submit_table_filter('change_limit')" >Apply</div>
                    <?php dbp_fn::get_pagination($table_model->total_items, $table_model->limit_start, $table_model->limit); ?>
                    <?php if (dbp_fn::is_query_filtered())  : ?>
                        <div id="dbp-bnt-clear-filter-query" class="button"  onclick="dbp_clear_filter()"><?php _e('Clear Filter','database_press'); ?></div>
                    <?php endif; ?>
                
                    <br class="clear">
                </div>
            <?php endif; ?>
           
            <?php echo $html_content; ?>
            <?php if ($table_model->last_error === false) : ?>
                <?php 
                $max_input_vars = (int)dbp_fn::get_max_input_vars();
                ?>
                <div class="dbp-table-footer">
                    <div class="tablenav-pages dbp-table-footer-left">
                        <div class="alignleft actions bulkactions">
                            <select id="dbp_bulk_action_selector_bottom">
                            <option value="-1"><?php _e('Bulk actions', 'database_press'); ?></option>
                                <option value="download" class="hide-if-no-js"><?php _e('Download'); ?></option>
                                <?php if  ($table_bulk_ok) : ?>
                                <option value="delete"><?php _e('Delete'); ?></option>
                                <?php endif; ?>
                            </select>
                            <select id="dbp_bulk_on_selector_bottom">
                            <?php if ($max_input_vars-50 > count($table_model->items) && $table_bulk_ok) : ?>
                                <option value="checkboxes" class="hide-if-no-js"><?php _e('On selected records','database_press'); ?></option>
                                <?php endif;?>
                                <option value="sql"><?php _e('Query results operations','database_press'); ?></option>
                            </select>
                        
                            <div class="button" onclick="dbp_bulk_actions()"><?php _e('Apply'); ?></div>
                        </div>
                    </div>
                    <div class="tablenav-pages dbp-table-footer-right">
                        <?php dbp_fn::get_pagination($table_model->total_items, $table_model->limit_start, $table_model->limit); ?>
                    </div>
                    <br class="clear">
                </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
    <?php 
    $list_of_tables_js = [];
    $list_of_tables = dbp_fn::get_table_list();
    foreach ($list_of_tables['tables'] as $lot) {
        $list_of_tables_js[] = $lot;
    }
    ?>

</div>
