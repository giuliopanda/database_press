<?php
/**
 * Il template della pagina amministrativa
 *
 * @package    database-press
 * @subpackage database-press/admin
 * 
 * @var String $render_content Il file dentro partial da caricare 
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
?>
<div class="wrap">
    <div id="dbp_container" style="display:none">
        <?php
        $table_bulk_ok = (@$table_model->table_status() != 'CLOSE' && count($table_model->get_pirmaries()) > 0 && $post->post_content['delete_params']->allow);
        ?>
        <div class="dbp-content-table js-id-dbp-content" >
            <h1 class="wp-heading-inline"><?php echo $list_title; ?></h1>
            <span class="page-title-action" onclick="dbp_edit_details_v2()"><?php  _e('Add New', 'database_press') ;?></span>
            <?php if (current_user_can('manage_options')) : ?>
                <a href="<?php echo admin_url("admin.php?page=dbp_list&section=list-sql-edit&dbp_id=".$post->ID); ?>" class="page-title-action" target="blank"><span class="dashicons dashicons-admin-generic" style="vertical-align: sub;"></span></a>
            <?php endif; ?>
            <?php wp_enqueue_media(); ?>
            <form id="table_filter" method="post" action="<?php echo admin_url("admin.php?page=".$_REQUEST['page']); ?>">
                <?php 
                do_action('dbp_admin_page_list_after_title', $id, (int)$table_model->total_items); ?>
                <p class="search-box">   
                    <input type="search" id="dbp_full_search" name="search" value="<?php echo esc_attr(stripslashes(dbp_fn::get_request('search'))); ?>">
                    <span class="button" onclick="dbp_submit_table_filter('search');">Search</span>
                    &nbsp; 
                    <?php if (count($table_model->tables_primaries) > 0) : ?>
                    <div class="dbp-submit" onclick="dbp_edit_details_v2()"><?php _e('Add New record','database_press'); ?></div>
                    <?php endif; ?>
                </p>
                
                <?php if ($table_model->last_error === false) : ?>
                    <?php 
                    $max_input_vars = (int)dbp_fn::get_max_input_vars();
                    ?>
                    <div class="dbp-table-footer-admin-menu">
                        <div class="tablenav-pages dbp-table-footer-left">
                            <div class="alignleft actions bulkactions">
                                <select id="dbp_bulk_action_selector_bottom">
                                <option value="-1"><?php _e('Bulk actions', 'database_press'); ?></option>
                                    <option value="download" class="hide-if-no-js"><?php _e('Download'); ?></option>
                                    <?php if  ($table_bulk_ok) : ?>
                                    <option value="delete"><?php _e('Delete'); ?></option>
                                    <?php endif; ?>
                                </select>
                                <input id="dbp_bulk_on_selector_bottom" type="hidden" value="checkboxes" >
                                <div class="button" onclick="dbp_bulk_actions()"><?php _e('Apply'); ?></div>
                            </div>
                        </div>
                     
                        <br class="clear">
                    </div>
                <?php endif; ?>

                <textarea style="display:none" id="sql_query_executed"><?php echo esc_textarea($table_model->get_current_query()); ?></textarea>
                <textarea style="display:none" id="sql_query_edit"><?php echo esc_textarea($table_model->get_default_query()); ?></textarea>
                <input type="hidden" name="page"  value="<?php echo esc_attr($_REQUEST['page']); ?>">
                <input type="hidden" name="action_query" id="dbp_action_query"  value="">
                <input type="hidden" id="dbp_table_sort_field" name="filter[sort][field]" value="<?php echo dbp_fn::esc_request('filter.sort.field'); ?>">
                <input type="hidden" id="dbp_table_sort_order"  name="filter[sort][order]" value="<?php echo dbp_fn::esc_request('filter.sort.order'); ?>">
                <input type="hidden" id="dbp_table_filter_limit_start" name="filter[limit_start]" value="<?php echo dbp_fn::esc_request($table_model->limit_start); ?>">
              
                <?php if ($table_model->last_error == false && $table_model->total_items > 0) : ?>
                    <div class="tablenav top dbp-tablenav-top">
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
            </form>
            <div class="clear"></div>
            <br><br>
            <?php if ($table_model->last_error === false) : ?>
               
                    <div class="tablenav-pages">
                        <?php dbp_fn::get_pagination($table_model->total_items, $table_model->limit_start, $table_model->limit); ?>
                    </div>
                    <br class="clear">
            
            <?php endif; ?>


            <?php 
            $list_of_tables_js = [];
            $list_of_tables = dbp_fn::get_table_list();
            foreach ($list_of_tables['tables'] as $lot) {
                $list_of_tables_js[] = $lot;
            }
            ?>
        </div>
        <div id="dbp_sidebar_popup" class="dbp-sidebar-popup">
            <div id="dbp_dbp_title" class="dbp-dbp-title">
                <div id="dbp_dbp_close" class="dbp-dbp-close" onclick="dbp_close_sidebar_popup()">&times;</div>
            </div>
            <div id="dbp_dbp_loader" ><div class="dbp-sidebar-loading"><div  class="dbp-spin-loader"></div></div></div>
            <div id="dbp_dbp_content" class="dbp-dbp-content"></div>
        </div>
    </div>
</div>

<br><br>