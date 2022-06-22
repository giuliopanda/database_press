<?php 
/**
* La struttura di una tabella. 
* @var String $table
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
</div>
<div class="dbt-content-table js-id-dbt-content" >
    <div class="dbt-content-margin">
        <h2 class="dbt-h2-inline dbt-content-margin"><?php _e(sprintf('Table %s', $table),'database_tables'); ?></h2>

        <?php if ($table != "") : ?>
            <ul class="dbt-submenu" style="display: inline-block;">
                <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == "show_create_structure" || $table == "") : ?>
                    <li><a  href="<?php echo \admin_url("admin.php?page=database_tables&section=table-structure&table=".$table); ?>"><?php _e('Go Back','database_tables'); ?></a></li>
                <?php elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == "structure-edit" ) : ?>
                    <li><a  href="<?php echo \admin_url("admin.php?page=database_tables&section=table-structure&table=".$table); ?>"><?php _e('Go Back','database_tables'); ?></a></li>
                    <li><a href="<?php echo \admin_url("admin.php?page=database_tables&section=table-structure&action=structure-edit&table=".$table); ?>"><?php _e('Reload','database_tables'); ?></a></li>
                <?php else: ?>
                    <?php if (count($table_model->items) < $max_row_allowed && !$table_model->error_primary &&  $table_options['status'] == 'DRAFT' ) : ?>
                        <li><a class="dbt-submit" href="<?php echo admin_url("admin.php?page=database_tables&section=table-structure&action=structure-edit&table=".$table); ?>"><?php _e('Edit table structure','database_tables'); ?></a></li>
                    <?php elseif ( $table_options['status'] != 'DRAFT' ) : ?>
                        <li><span class="dbt-submit dbt-btn-disabled" onclick="alert('<?php echo esc_attr(__('You must first set the table in draft mode', 'database_tables')); ?>');" ><?php _e('Edit table structure','database_tables'); ?></span></li>
                    <?php elseif ( count($table_model->items) >= $max_row_allowed ) : ?>
                        <li><span class="dbt-submit dbt-btn-disabled" onclick="alert('<?php echo esc_attr(__('The max_input_vars value is sufficient to edit this form', 'database_tables')); ?>');" ><?php _e('Edit table structure','database_tables'); ?></span></li>
                    <?php elseif($table_model->error_primary ) : ?>
                        <li><span class="dbt-submit dbt-btn-disabled" onclick="alert('<?php echo esc_attr(__('Set the primary key through queries before changing the structure', 'database_tables')); ?>');" ><?php _e('Edit table structure','database_tables'); ?></span></li>
                    <?php endif; ?>
                    <li><a href="<?php echo admin_url("admin.php?page=database_tables&section=table-structure&action=show_create_structure&table=".$table); ?>"><?php _e('Show sql','database_tables'); ?></a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
        
       

        <?php if ($msg != "") : ?>
            <div class="dtf-alert-info"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if (@$msg_error != ""): ?>
            <div class="dtf-alert-sql-error"><?php echo $msg_error; ?></div>
        <?php endif ; ?>

        <?php if ($this->last_error != "" && $dtf::get_request('action', '', 'string') != 'create-table-csv-data') : ?>
            <div class="dtf-alert-sql-error"><?php echo $this->last_error; ?></div>
        <?php endif; ?>
        <?php if ($action == 'show_create_structure') : ?>
            <div class="dbt-result-query js-dbt-mysql-query-text">
                <?php 
                $temp_sql =str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;", nl2br(htmlentities($sql_sctructure)));
                echo $temp_sql;
                ?>
            </div>
        <?php elseif ($action == 'structure-edit') : ?>
            <h3 id="dbt_result_alert_table_title" style="display:none">
                <?php _e('Executing...', 'database_tables'); ?>
            </h3>
            <div id="dbt_result_alert_table"></div>
            <div id="dbt_link_return"></div>
            <div  class="js-hide-after-save" id="dbt_content_structure_table">
                <form method="POST" id="dbt_create_table">
                    <input type="hidden" name="page" value="database_tables" />
                    <?php // La sezione deve sempre esistere perché serve a caricare il loader giusto ?>
                    <input type="hidden" name="section" value="table-structure" />
                    <input type="hidden" name="table" value="<?php echo $table; ?>" />
                    <div id="edit_options">
                        <hr>
                        <?php if ($table != "") : ?>
                        <div class="dbt-form-row">
                            <label><span class="dbt-form-label"><?php  _e('Status', 'database_tables'); ?></span><?php $dtf::html_select(['DRAFT'=>'Draft','PUBLISH'=>'Publish','CLOSE'=>'Close'], true, 'name="options[status]"', $table_options['status']); ?></label>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="options[status]" value="DRAFT">
                        <?php endif; ?>
                        <?php if ($table_options['status'] == "DRAFT") : ?>
                            <div class="dbt-form-row">
                                <label class="dbt-form-label-top"><?php _e('Description', 'database_tables'); ?></label><textarea class="dbt-form-textarea" name="options[description]"> <?php echo (esc_textarea(stripslashes(@$table_options['description']))); ?></textarea>
                            </div>
                        <?php else: ?>
                            <?php echo _e('Description', 'database_tables'); ?>: <?php echo esc_attr(@$table_options['description']); ?>
                            <textarea style="display:none" name="options[description]"><?php echo esc_textarea(stripslashes(@$table_options['description'])); ?></textarea>
                            </div>
                        <?php endif; ?>
                        <hr>
                    </div>
                    <?php if ($table_options['status'] == "DRAFT") : ?>
                        <?php require (dirname(__FILE__).'/dbt-partial-alter-table.php'); ?>
                        <div class="dbt-box-create-table-box">
                            <?php if ($table != "") : ?>
                            <div id="dbt_content_button_create_form" style="display:none">
                                <div onclick="dbt_submit_test_edit_structure()" class="dbt-submit"><?php _e('Analysis of changes', 'database_tables'); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php else : // table_options[satus] == DRAFT ?>
                        <div class="dtf-alert-sql-error js-hide-after-save">
                            <?php if ($table_options['status'] == 'CLOSE') : ?>
                                <?php _e('A Close table cannot be modified! You must first change the status to DRAFT.', 'database_tables'); ?>
                                <?php else : ?>
                                    <?php _e('A table in production cannot be modified! You must first change the status to DRAFT.', 'database_tables'); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div onclick="dbt_submit_edit_structure('<?php echo ($table != '') ? 'dbt_update_table_structure' : 'dbt_create_table_structure'; ?>')" class="dbt-submit js-hide-after-save"><?php _e('Save', 'database_tables'); ?></div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            <h3 id="dbt_result_alert_table_title_test" class="dbt-structure-result-alert-table-title-test js-hide-after-save">
                <?php _e('Analysis in progress', 'database_tables'); ?>
            </h3>
            <div id="dbt_result_alert_table_test"></div>
            <hr>
            
            <div id="dbt_execute_query_command" style="display:none" >
                <?php if ($table_options['status'] == "DRAFT") : ?>
                    <div onclick="dbt_submit_edit_structure('<?php echo ($table != '') ? 'dbt_update_table_structure' : 'dbt_create_table_structure'; ?>')" class="dbt-submit js-hide-after-save"><?php _e(( ($table != '') ?'Alter table' : 'Create table'), 'database_tables'); ?></div>
                <?php endif; ?>    
            </div>
            <div id="dbt_msg_fix_error_before" style="display:none"class="dtf-alert-sql-error" ><?php _e('One or more queries have failed. Correct them before editing the table.', 'database_tables'); ?></div>
        <?php elseif (!isset($table)) : ?>
            ?><div class="dtf-alert-sql-error"><?php _e('No table was selected','database_tables'); ?></div>
        <?php else : ?>
            <hr>
            <div id="dbt_show_metadata" class="dbt-css-mb-1">
                <?php if (isset($table_options['description']) && $table_options['description'] != "") : ?>
                    <div><?php  echo esc_textarea(stripslashes($table_options['description'])); ?> </div>
                <?php endif; ?>
                <?php if (isset($table_options['status']) && $table_options['status'] != "") : ?>
                    <div><?php _e('Status', 'database_tables'); ?>: <?php  echo $table_options['status']; 
                    Dbt_fn::echo_html_icon_help('database_tables-table-structure','status'); ?> </div>
                <?php endif; ?>
             
            </div>
            <div id="dbt_edit_metadata" style="display:none">
                <form method="POST" id="dbt_edit_metadata_form"  action="<?php echo admin_url("admin.php?page=database_tables&section=table-structure"); ?>">
                    <input type="hidden" name="table" value="<?php echo $table; ?>" />
                    <input type="hidden" name="action" value="save_metadata" />
                    <div class="dbt-form-row">
                        <label><span class="dbt-form-label"><?php  _e('Status', 'database_tables'); ?></span><?php $dtf::html_select(['DRAFT'=>'Draft','PUBLISH'=>'Publish','CLOSE'=>'Close'], true, 'name="options[status]"', $table_options['status']); ?></label>
                    </div>
                    <div class="dbt-form-row">
                        <label class="dbt-form-label-top"><?php _e('Description', 'database_tables'); ?></label><textarea class="dbt-form-textarea" name="options[description]"> <?php echo (esc_textarea(stripslashes(@$table_options['description']))); ?></textarea>
                    </div>
                    <div class="dbt-form-row">
                        <div onclick="dbt_submit_edit_metadata()" class="dbt-submit "><?php _e('Save', 'database_tables'); ?></div>
                        <div onclick="dbt_cancel_edit_metadata()" class="button"><?php _e('Cancel', 'database_tables'); ?></div>
                    </div>
                </form>
            </div>
            <div id="dbt_edit_metadata_btn">
                <?php if ($table_options['external_filter']) : ?>
                  
                <?php else : ?>
                <div onclick="dbt_show_edit_metadata()" class="dbt-submit-style-link"><?php _e('Edit Status & Description', 'database_tables'); ?></div>
                <?php endif ; ?>

            </div>
            <hr>
            <?php
            $html_table   = new dbt_html_simple_table();
            $html_table->add_table_class('wp-list-table widefat striped dbt-table-view-list');
           // echo $html_table->template_render($table_model);
            require (dirname(__FILE__).'/dbt-partial-structure-show-table.php');
            echo '<h3>'.__('Indexes','database_tables').'</h3>';
            echo '<p>'.__('Indexes can be used to improve query performance. Instead, you can use unique keys to avoid duplicate fields within one or more columns.','database_tables');
            Dbt_fn::echo_html_icon_help('database_tables-table-structure','indexes');
            echo '</p>';
            if (!$table_model->error_primary && $table_options['status'] == "DRAFT") {
                ?><a class="dbt-submit" href="<?php echo add_query_arg(['section'=>'table-structure','table'=>$table,'action'=>'edit-index','dbt_id'=>''], admin_url("admin.php?page=database_tables")); ?>"><?php _e('Add index','database_tables'); ?></a><br ><br ><?php
            }
            if (is_countable($indexes) && count($indexes) > 0) {
                echo $html_table->render($indexes);
            }
        endif; ?>
    </div>
</div>