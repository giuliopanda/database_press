<?php
/**
 * I campi della form
 * 
 * /admin.php?page=dbt_list&section=list-form&dbt_id=xxx
 *  (come deve essere gestito un campo? Ad esempio: se lo voglio lavorare come numero e quindi fare il cast se è un testo, oppure come un link, o come un serializzato, o ancora come un'immagine.)
 * @var $items Lo schema della tabella
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
$append = '<span class="dbt-submit" onclick="dbt_submit_list_form()">' . __('Save', 'database_tables') . '</span>';

?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
</div>
<div class="dbt-content-table js-id-dbt-content">
    <div style="float:right; margin:1rem">
            <?php _e('Shortcode: ', 'database_tables'); ?>
            <b>[dbt_list id=<?php echo $post->ID; ?>]</b>
    </div>
    <?php if (Dbt_fn::echo_html_title_box('list', $list_title, '', $msg, $msg_error, $append)) : ?>
        <div class="dbt-lf-container-table-pre">&nbsp;</div>
        <form id="list_form" method="POST" action="<?php echo admin_url("admin.php?page=dbt_list&section=list-form&dbt_id=".$id); ?>">
            <input type="hidden" name="action" value="list-form-save" />
            <input type="hidden" name="table" value="<?php echo (isset($import_table)) ? $import_table : ''; ?>" />
            <input type="hidden" name="dbt_id" value="<?php echo @$id; ?>" id="dbt_id_list" />
            <div class="dbt-content-margin">
                
                <div class="js-dragable-table">
                    <?php 
                    $count_fields = 0;
                    foreach ($tables as $key=>$table) : 
                        $table_options = $table['table_options'];//Dbt_fn::get_dbt_option_table($table['table_name']);
                       // var_dump ($table);
                        $primary_key = Dbt_fn::get_primary_key($table['table_name']);
                        ?>
                        <div class="dbt-lf-container-table js-lf-container-table">
                            <div class="js-dbt-lf-box-table-info">
                                <div class="dbt-lf-table-title"> <?php echo (isset($table['table_name'])) ? $table['table_name']." (".$key.")" : $key; ?>
                                    <span class="dbt-structure-toggle js-structure-toggle">
                                        <span class="js-lf-dbt-hide" onClick="dbt_lf_toggle_attr(this,0)" style="display: none;">Hide attributes</span>
                                        <span class="js-lf-dbt-show" onClick="dbt_lf_toggle_attr(this,1)" style="display: inline-block;">Show attributes</span>
                                        <?php Dbt_fn::echo_html_icon_help('dbt_list-list-form','class'); ?>
                                    </span>
                                    <?php if ($table_options->table_status == "CLOSE") : ?>
                                        <div class="dtf-alert-warning">
                                            <?php _e('The table can no longer be modified because it is in a closed state.', 'database_tables'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="js-dbt-lf-box-attributes" style="display:none">
                                    <div class="dbt-structure-grid">

                                    <div class="dbt-form-row-column">
                                            <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Module Type','database_tables'); ?></span>
                                                <?php
                                                $add_style = "";
                                                // TODO DIVENTA: form, view hide
                                                if ($table_options->table_status == "CLOSE") {
                                                    $module_type = "READONLY";
                                                    $add_style=' disabled="disabled';
                                                } else if (empty($table_options->module_type)) {
                                                    //TODO se c'è un collegamento con la chiave primaria allora è hide di default.
                                                   $module_type =  (isset($table_options->precompiled_primary_id)) ? 'HIDE' : 'EDIT';
                                                } else {
                                                    $module_type = $table_options->module_type;
                                                }
                                                
                                                echo Dbt_fn::html_select(['EDIT'=>'Editable' ,'READONLY'=>'Read only',  'HIDE'=>'Hide'], true, 'name="table_module_type['. esc_attr($key) .']" class="js-module-type"'.$add_style, $module_type); ?>
                                            </label>
                                        </div>



                                        <div class="dbt-form-row-column">
                                            <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('It allows you not to create the record','database_tables'); ?></span>
                                                <?php echo Dbt_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="table_allow_create['. esc_attr($key) .']"', $table_options->allow_create); ?>
                                            </label>
                                        </div>

                                       
                                    </div>
                                 

                                    <div class="dbt-structure-grid">
                                        <div class="dbt-form-row-column">
                                            <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Show Title','database_tables'); ?></span>
                                                    <?php echo Dbt_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="table_show_title['. esc_attr($key) .']" onchange="dbt_select_change_toggle_form_title(this)"',$table_options->show_title); ?>
                                            </label>
                                        </div>
                                        <div class="dbt-form-row-column">
                                            <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Frame Style','database_tables'); ?></span>
                                                <?php echo Dbt_fn::html_select(['WHITE'=>'White', 'BLUE'=>'Blue', 'RED'=>'red', 'GREEN'=>'green', 'YELLOW'=>'yellow', 'PURPLE'=>'purple',  'BROWN'=>'brown', 'HIDDEN'=>'hidden'], true, 'name="table_frame_style['. esc_attr($key) .']"', $table_options->frame_style); ?>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="dbt-form-row dbt-label-grid js-form-row-title">
                                        <label><span class="dbt-form-label"><?php _e('Custom title','database_tables'); ?></span></label>
                                        <input class="dbt-input" style="width:100%"  name="table_title[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($table_options->title); ?>">
                                    </div>

                                    <div class="dbt-form-row dbt-label-grid">
                                        <label><span class="dbt-form-label"><?php _e('Description','database_tables'); ?></span></label>
                                        <textarea class="dbt-input" style="width:100%" rows="2" name="table_description[<?php echo esc_attr($key); ?>]"><?php echo esc_textarea($table_options->description); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="js-dragable-table">  
                                <?php require(__DIR__.'/dbt-content-list-form-single-table.php'); ?>
                            </div>
                            <?php if ($table_options->table_status == "DRAFT") : ?>
                                <div class="button" onClick="dbt_duplicate_field(this)"><?php _e('New Field', 'database_tables'); ?></div>
                                <?php Dbt_fn::echo_html_icon_help('dbt_list-list-form','new_field'); ?>
                            <?php else: ?>
                                <div class="dbt-form-small-info">
                                <?php _e ('The table is in a published state and cannot be changed', 'database_tables'); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    <?php endforeach ;?>
                </div>

                <?php if (is_countable($tables) && count($tables) > 0) : ?>
                    <div class="dbt-submit" onclick="dbt_submit_list_form();"><?php _e('Save','database_tables'); ?></div>
                <?php else : ?>
                    <div class="dtf-alert-sql-error"><?php _e('Something is wrong, check the query if it is correct', 'database_tables'); ?></div>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>
</div>
