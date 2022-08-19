<?php
/**
 * Scrive una query
 * 
 * Per il rendering delle tabelle chiama: dirname(__FILE__)."/dbt-content-table-without-filter" 
 * 
 * @var Boolean $ajax_continue 
 * @var Array $info
 * @var $queries

 * @package    database-tables
 * @subpackage database_tables/admin
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;

$append = '<span class="dbt-submit" onclick="dtf_submit_list_sql()">' . __('Save', 'database_tables') . '</span>';

?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
</div>
<div class="dbt-content-table js-id-dbt-content" >
    <div style="float:right; margin:1rem">
            <?php _e('Shortcode: ', 'database_tables'); ?>
            <b>[dbt_list id=<?php echo $post->ID; ?>]</b>
    </div>
    <?php Dbt_fn::echo_html_title_box('list', $list_title, '', $msg,  $msg_error, $append); ?>
    <form id="table_filter" method="post" action="<?php echo admin_url("admin.php?page=dbt_list&section=list-sql-edit&dbt_id=".esc_attr($id)); ?>">
        <div class="dbt-content-margin">
            <input type="hidden" name="section" value="list-sql-edit">
            <input type="hidden" name="action" value="list-sql-save">
            <input type="hidden" name="dbt_id" value="<?php echo  esc_attr($id); ?>">

            <h3 class="dbt-h3 dbt-margin-top"><?php _e('List settings', 'database_tables'); ?></h3>
            <div class="dbt-form-row">
                <label>
                    <span class="dbt-form-label"><?php _e('Title', 'database_tables'); ?></span>
                    <input name="post_title" class="dbt-input-long" value="<?php echo esc_attr($list_title); ?>">
                </label>
            </div>
            <div class="dbt-form-row">
                <label>
                    <span class="dbt-form-label-top"><?php _e('Descriprion', 'database_tables'); ?></span>
                    <textarea name="post_excerpt" class="dbt-form-textarea"><?php echo esc_textarea($post_excerpt); ?></textarea>
                </label>
            </div>


            <h3 class="dbt-h3 dbt-margin-top"><?php _e('Admin sidebar menu', 'database_tables'); ?></h3>
            <p class="dtf-alert-gray" style="margin-top:-1rem">
                <?php _e('Add the list in the sidebar menu.','database_tables'); 
                Dbt_fn::echo_html_icon_help('dbt_list-list-sql-edit','admin_sidebar_menu');
                ?>
            </p>
            <div class="dbt-form-row">
                <label>
                    <input type="checkbox" name="show_admin_menu" id="cb_show_admin_menu" value="1"  <?php echo (@$dbt_admin_show['show'] == 1) ? 'checked="checked"' : ''; ?> onchange="cb_change_toggle_options(this)">
                    <span class="dbt-form-label"><?php _e('Show in admin menu', 'database_tables'); ?></span>
                </label>
            </div>
            <div id="admin_menu_options_box"  style="display:<?php echo (@$dbt_admin_show['show'] == 1) ? 'block' : 'none'; ?>">
                <div class="dbt-structure-grid">
                    <div class="dbt-form-row-column">
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Title', 'database_tables'); ?></span>
                                <?php $menu_title = (isset($dbt_admin_show['menu_title']) && $dbt_admin_show['menu_title'] != "") ? $dbt_admin_show['menu_title'] : $list_title; ?>
                                <input name="menu_title" class="dbt-input-long" value="<?php echo esc_attr($menu_title); ?>">
                            </label>
                        </div>
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Add custom icon', 'database_tables');    Dbt_fn::echo_html_icon_help('dbt_list-list-sql-edit','admin_sidebar_menu_icon'); ?></span>
                                <input name="menu_icon" class="dbt-input-long" value="<?php echo esc_attr(@$dbt_admin_show['menu_icon']); ?>">
                            </label>
                        </div>
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Position (number)', 'database_tables'); Dbt_fn::echo_html_icon_help('dbt_list-list-sql-edit','admin_sidebar_menu_position'); ?></span>
                                <input name="menu_position" class="dbt-input-long" value="<?php echo esc_attr(@$dbt_admin_show['menu_position']); ?>">
                            </label>
                        </div>
                    </div>
                    <div class="dbt-form-row-column" style="padding-left:2rem">
                        
                        <label><?php 
                        _e('Permissions', 'database_tables'); 
                        Dbt_fn::echo_html_icon_help('dbt_list-list-sql-edit','admin_sidebar_menu_permissions'); 
                        ?></label>
                        <?php foreach($wp_roles->get_names() as $role_key=>$role_name): ?>
                            <?php $role = get_role( $role_key ); ?>
                        
                                <div class="dbt-form-row">
                                    <label>
                                        <input type="checkbox" class="js-add-role-cap" name="add_role_cap[]" value="<?php echo $role_key; ?>" <?php echo ($role->has_cap('dbt_manage_'.$id)) ? 'checked="checked"' : ''; ?>>
                                        <span class="dbt-form-label-top"><?php echo $role_name;  ?></span>
                                    </label>
                                </div>
                        
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <h3 class="dbt-h3 dbt-margin-top"><?php _e('Query', 'database_tables'); ?></h3>
            <p class="dtf-alert-gray" style="margin-top:-1rem">
                <?php _e('How the data is extracted','database_tables'); 
                Dbt_fn::echo_html_icon_help('dbt_list-list-sql-edit','admin_query');
                ?>
            </p>
            <?php Dbt_html_sql::render_sql_from($table_model, $show_query); ?>

            <div class="dbt-content-margin">
                <h3 class="dbt-h3"><?php _e('Options', 'database_tables'); ?></h3>
                <div class="dbt-form-row">
                    <label>
                        <span class="dbt-form-label "><?php _e('Elements per page', 'database_tables'); ?></span>
                        <input name="sql_limit" class="dbt-input" value="<?php echo absint($sql_limit); ?>">
                    </label>
                </div>
                <div class="dbt-form-row">
                    <label>
                        <span class="dbt-form-label "><?php _e('Default Order', 'database_tables'); ?></span>
                        <?php echo Dbt_fn::html_select(array_merge([''=>'-'],$info_rows), true, 'name="sql_order[field]"', $sql_order['field']); ?>
                        <?php echo Dbt_fn::html_select(['ASC','DESC'], false, 'name="sql_order[sort]"', $sql_order['sort']); ?>
                    </label>
                </div>

                <h3 class="dbt-h3"><?php _e('Filter (in frontend And admin plugin)', 'database_tables'); ?></h3>
                <p class="dtf-alert-gray" style="margin-top:-1rem"><?php _e('Add filters from external data', 'database_tables');
                Dbt_fn::echo_html_icon_help('dbt_list-list-sql-edit','admin_filter');?></p>
                <?php if (count($info_rows) > 0) : ?>
                    <div class="dbt-form-row" id="dbt_clone_master" style="display:none">
                        <label>
                            <?php echo Dbt_fn::html_select($info_rows, true, 'name="sql_filter_field[]" style="max-width:25%"'); ?>
                            <?php echo Dbt_fn::html_select($info_ops, true, 'name="sql_filter_op[]" style="max-width:15%"'); ?>
                            <textarea class="dbt-input" name="sql_filter_val[]" rows="1" style="min-width:200px"></textarea>
                            <span> required <input type="checkbox" onchange="dbt_required_field(this)"><input type="hidden" name="sql_filter_required[]" class="js-filter-required"> </span>
                        
                            <div class="button" onclick="dbt_remove_sql_row(this)"><?php _e('DELETE','database_tables'); ?></div>
                        </label>
                    </div>
                    <div id="dbt_container_filter">
                        <?php foreach ($sql_filter as $filter) : ?>
                        <div class="dbt-form-row">
                            <label>
                                <?php echo Dbt_fn::html_select($info_rows, true, 'name="sql_filter_field[]"', $filter['column']); ?>
                                <?php echo Dbt_fn::html_select($info_ops, true, 'name="sql_filter_op[]"', $filter['op']); ?>
                                <textarea class="dbt-input" name="sql_filter_val[]" rows="1" style="min-width:250px"><?php echo esc_textarea(stripslashes($filter['value'])); ?></textarea>
                                <span>  required <input type="checkbox" onchange="dbt_required_field(this)"<?php echo esc_attr($filter['required']) ? ' checked="checked"' : ''; ?>><input type="hidden" name="sql_filter_required[]" class="js-filter-required" value="<?php echo esc_attr($filter['required']); ?>"> </span>
                                <div class="button" onclick="dbt_remove_sql_row(this)"><?php _e('DELETE','database_tables'); ?></div>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="dbt-form-row">
                        <label>
                            <div onclick="dbt_list_sql_add_row()" class="button"><?php _e('Add row', 'database_tables'); ?></div>
                        </label>
                    </div>
                <?php else : ?>
                <?php _e('Salva la query prima di impostare i filtri','database_tables'); ?>
                <?php endif; ?>
        
                <br>
                <h3 class="dbt-h3"><?php _e('Delete options', 'database_tables'); ?></h3>
                <p class="dtf-alert-gray" style="margin-top:-1rem"><?php _e('When one or more records are deleted, you choose which tables in the query you want to be deleted.', 'database_tables');
                Dbt_fn::echo_html_icon_help('dbt_list-list-sql-edit','delete_options');?></p>
                <?php
                /** @var array $delete_tables [[table, as, where, la parte di stringa elaborata], ...] */
                $delete_tables = $table_model->get_partial_query_from(true);
                if (is_countable($delete_tables) && count($delete_tables) > 0) {
                    foreach ($delete_tables as $k=>$dt) {
                        if (!array_key_exists($dt[1], $post_allow_delete)) {
                            $post_allow_delete[$dt[1]] = 1;
                        }
                        ?>
                        <?php if (! ($k%2) ) : ?><div class="dbt-structure-grid"><?php endif; ?>
                            <div class="dbt-form-row-column">
                                <label>
                                    <span class="dbt-form-label "><?php echo $dt[0].' AS '.$dt[1].' '; ?></span>
                                    <?php echo Dbt_fn::html_select(['1'=>'Yes', 0=>'No'], true, 'name="remove_tables_alias['.$dt[1].']"', @$post_allow_delete[$dt[1]]); ?>
                                </label>
                            </div>
                        <?php if ($k%2) : ?></div><?php endif; 
                    }
                    if (! ($k%2) ) : ?></div><?php endif;
                }
                ?>
                

                <br><br><hr>
                <div class="dbt-submit" onclick="dtf_submit_list_sql(this)">Save</div>
                <br><br>
            </div>
        </div>
    </form>
</div>
    