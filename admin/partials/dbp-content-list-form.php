<?php
/**
 * I campi della form
 * 
 * /admin.php?page=dbp_list&section=list-form&dbp_id=xxx
 *  (come deve essere gestito un campo? Ad esempio: se lo voglio lavorare come numero e quindi fare il cast se è un testo, oppure come un link, o come un serializzato, o ancora come un'immagine.)
 * @var $items Lo schema della tabella
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
$append = '<span class="dbp-submit" onclick="dbp_submit_list_form()">' . __('Save', 'database_press') . '</span>';

?>
<div class="dbp-content-header">
    <?php require(dirname(__FILE__).'/dbp-partial-tabs.php'); ?>
</div>
<div class="dbp-content-table js-id-dbp-content">
    <div style="float:right; margin:1rem">
            <?php _e('Shortcode: ', 'database_press'); ?>
            <b>[dbp_list id=<?php echo $post->ID; ?>]</b>
    </div>
    <?php if (dbp_fn::echo_html_title_box('list', $list_title, '', $msg, $msg_error, $append)) : ?>
        <div class="dbp-lf-container-table-pre">&nbsp;</div>
        <form id="list_form" method="POST" action="<?php echo admin_url("admin.php?page=dbp_list&section=list-form&dbp_id=".$id); ?>">
            <input type="hidden" name="action" value="list-form-save" />
            <input type="hidden" name="table" value="<?php echo (isset($import_table)) ? $import_table : ''; ?>" />
            <input type="hidden" name="dbp_id" value="<?php echo @$id; ?>" id="dbp_id_list" />
            <div class="dbp-content-margin">
                
                <div class="js-dragable-table">
                    <?php 
                    $count_fields = 0;
                    foreach ($tables as $key=>$table) : 
                        $table_options = $table['table_options'];//dbp_fn::get_dbp_option_table($table['table_name']);
                       // var_dump ($table);
                        $primary_key = dbp_fn::get_primary_key($table['table_name']);
                        ?>
                        <div class="dbp-lf-container-table js-lf-container-table">
                            <div class="js-dbp-lf-box-table-info">
                                <div class="dbp-lf-table-title"> <?php echo (isset($table['table_name'])) ? $table['table_name']." (".$key.")" : $key; ?>
                                    <span class="dbp-structure-toggle js-structure-toggle">
                                        <span class="js-lf-dbp-hide" onClick="dbp_lf_toggle_attr(this,0)" style="display: none;">Hide attributes</span>
                                        <span class="js-lf-dbp-show" onClick="dbp_lf_toggle_attr(this,1)" style="display: inline-block;">Show attributes</span>
                                        <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','class'); ?>
                                    </span>
                                    <?php if ($table_options->table_status == "CLOSE") : ?>
                                        <div class="dbp-alert-warning">
                                            <?php _e('The table can no longer be modified because it is in a closed state.', 'database_press'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="js-dbp-lf-box-attributes" style="display:none">
                                    <div class="dbp-structure-grid">

                                    <div class="dbp-form-row-column">
                                            <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Module Type','database_press'); ?></span>
                                                <?php
                                                $add_style = "";
                                                
                                                // TODO DIVENTA: form, view hide
                                                if ($table_options->table_status == "CLOSE") {
                                                    $module_type = "READONLY";
                                                    $add_style=' disabled="disabled';
                                                } else if (!$table_options->isset('module_type')) {
                                                   
                                                   $module_type =  ($table_options->isset($precompiled_primary_id)) ? 'HIDE' : 'EDIT';
                                                } else {
                                                    $module_type = $table_options->module_type;
                                                }
                                                echo dbp_fn::html_select(['EDIT'=>'Editable' ,'READONLY'=>'Read only',  'HIDE'=>'Hide'], true, 'name="table_module_type['. esc_attr($key) .']" class="js-module-type" onchange="change_select_module_type(this)" '.$add_style, $module_type); ?>
                                            </label>
                                        </div>



                                        <div class="dbp-form-row-column js-row-allow-create-record">
                                            <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('It allows you not to create the record','database_press'); ?></span>
                                                <?php echo dbp_fn::html_select(['SHOW'=>'Yes', 'HIDE'=>'No'], true, 'name="table_allow_create['. esc_attr($key) .']"', $table_options->allow_create); ?>
                                            </label>
                                        </div>

                                       
                                    </div>
                                 

                                    <div class="dbp-structure-grid">
                                        <div class="dbp-form-row-column">
                                            <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Show Title','database_press'); ?></span>
                                                    <?php echo dbp_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="table_show_title['. esc_attr($key) .']" onchange="dbp_select_change_toggle_form_title(this)"',$table_options->show_title); ?>
                                            </label>
                                        </div>
                                        <div class="dbp-form-row-column">
                                            <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Frame Style','database_press'); ?></span>
                                                <?php echo dbp_fn::html_select(['WHITE'=>'White', 'BLUE'=>'Blue', 'RED'=>'red', 'GREEN'=>'green', 'YELLOW'=>'yellow', 'PURPLE'=>'purple',  'BROWN'=>'brown', 'HIDDEN'=>'hidden'], true, 'name="table_frame_style['. esc_attr($key) .']"', $table_options->frame_style); ?>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="dbp-form-row dbp-label-grid js-form-row-title">
                                        <label><span class="dbp-form-label"><?php _e('Custom title','database_press'); ?></span></label>
                                        <input class="dbp-input" style="width:100%"  name="table_title[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($table_options->title); ?>">
                                    </div>

                                    <div class="dbp-form-row dbp-label-grid">
                                        <label><span class="dbp-form-label"><?php _e('Description','database_press'); ?></span></label>
                                        <textarea class="dbp-input" style="width:100%" rows="2" name="table_description[<?php echo esc_attr($key); ?>]"><?php echo esc_textarea($table_options->description); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="js-dragable-table">  
                                <?php require(__DIR__.'/dbp-content-list-form-single-table.php'); ?>
                            </div>
                            <?php if ($table_options->table_status == "DRAFT") : ?>
                                <div class="button" onClick="dbp_duplicate_field(this)"><?php _e('New Field', 'database_press'); ?></div>
                                <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','new_field'); ?>
                            <?php else: ?>
                                <div class="dbp-form-small-info">
                                <?php _e ('The table is in a published state and cannot be changed', 'database_press'); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    <?php endforeach ;?>
                </div>

                <?php if (is_countable($tables) && count($tables) > 0) : ?>
                    <div class="dbp-submit" onclick="dbp_submit_list_form();"><?php _e('Save','database_press'); ?></div>
                <?php else : ?>
                    <div class="dbp-alert-sql-error"><?php _e('Something is wrong, check the query if it is correct', 'database_press'); ?></div>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>
</div>
