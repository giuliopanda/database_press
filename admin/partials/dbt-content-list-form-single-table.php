<?php
/**
 * Carico una singola tabella in list-form
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
foreach ($table['fields'] as $item) {
    if ($item->name == "_dbt_alias_table_") continue;
    $count_fields++;
    $label = (@$item->label) ? @$item->label : $item->name;
   
    $bool_precompiled = ($item->where_precompiled == 1 && $item->custom_value != '' );
    ?>
    <div class="js-dragable-fields dbt-lf-field_box js-dbt-lf-form-card">
        <div class="dbt-lf-field-title" >
            <span class="dbt-lf-handle js-dragable-handle"><span class="dashicons dashicons-sort"></span></span>
            <input type="hidden" class="js-dragable-order" name="fields_order[<?php echo  absint($count_fields); ?>]" value="<?php echo esc_attr(@$item->order); ?>">
            <span class="dbt-lf-edit-icon js-lf-edit-icon">
            <span class="dashicons dashicons-edit js-dashicon-edit" onclick="dbt_lf_form_toggle(this)"></span>
            </span> 
            
            <span class="js-title-field">
                <?php if ($item->field_name == $primary_key) : ?>
                    <span class="dashicons dashicons-admin-network" style="color:#e2c447; vertical-align: text-top;" title="Primary"></span>
                <?php endif; ?>
                <?php echo $label . ' <span style="font-size:.9rem">('.$item->js_rif.')</span>'; ?>
                <?php echo (@$item->js_script != '') ? '<span class="dbt-jsicon">JS</span>' : ''; ?>
            </span>
            <?php 
            if ($bool_precompiled) {
                ?><span class="dtf-alert-warning"><?php _e('Automatically filled by the query','database_tables'); ?></span><?php
            }
            ?>
            <input type="hidden" class="js-hidden-field-name" name="fields_name[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr($item->name); ?>">
            <input type="hidden" class="js-hidden-field-table" name="fields_table[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr($key); ?>">
            <input type="hidden" name="fields_orgtable[<?php echo absint($count_fields); ?>]" class="js-hidden-field-orgtable" value="<?php echo esc_attr($table['table_name']); ?>">
            <div style="margin-left:1rem; display: inline-block;">
                <?php echo Dbt_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="fields_edit_view['. absint($count_fields) .']" class="js-show-hide-select"', @$item->edit_view); ?>
            </div>
            <?php if ($table_options->table_status == "DRAFT") : ?>
                <div style="display:none;  vertical-align: middle; margin-left:1rem; cursor:pointer;" class="js-cancel-delete button" onclick="dbt_form_cancel_remove_field(this)"><?php _e('Restore the deleted field', 'database_tables'); ?></div>
            <?php endif; ?>
        </div>
        <div class="dbt-structure-content js-lf-form-content" style="display:none">
            <?php if ($bool_precompiled ) : ?>
                <div class="js-structure-content-before">
                    <label class="dtf-alert-warning" style="display:block; margin-left:1rem;"><input type="checkbox" name="where_precompiled[<?php echo absint($count_fields); ?>]" value="1" checked="checked" onchange="dbt_where_precompiled(this)"><?php printf(__('Automatically calculate the value from the query. This is the formula entered: %s.','database_tables'), @$item->custom_value); ?></label>
                </div>
            <?php endif; ?>
            <div class="js-structure-content-inside" <?php echo ($bool_precompiled) ? 'style="display:none"' : ''; ?>>
                <div class="dbt-structure-grid">
                    <div class="dbt-form-row-column">
                        <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Field Type','database_tables'); ?></span>
                            <?php echo Dbt_fn::html_select(['Standard fields'=>['VARCHAR'=>'Text (single line)', 'TEXT'=>'Text (multi line)', 'DATE'=>'Date', 'DATETIME'=>'Date time' , 'NUMERIC'=>'Number', 'SELECT'=>'Multiple Choice - Drop-down List (Single Answer)', 'RADIO'=>'Multiple Choice - Radio Buttons (Single Answer)', 'CHECKBOX'=>'Checkbox (Single Answer)','CHECKBOXES'=>'Checkboxes (Multiple Answers)'], 'Special fields'=>['READ_ONLY'=>'Read only','EDITOR_CODE'=>'Editor Code','EDITOR_TINYMCE'=>'Classic text editor', 'CREATION_DATE'=>'Record creation date', 'LAST_UPDATE_DATE'=>'Last update date', 'RECORD_OWNER'=>'Author (who created the record)', 'MODIFYING_USER'=>'Modifying user', 'CALCULATED_FIELD'=>'calculated field', 'LOOKUP'=>'lookup', 'UPLOAD_FIELD' => 'Upload files in a custom dir'], 'Wordpress field' => ['POST'=>'post','USER'=>'user', 'MEDIA_GALLERY' => 'Media Gallery']], true, 'name="fields_form_type['. absint($count_fields) . ']" onchange="dbt_lf_select_type_change(this)" class="js-fields-field-type"', @$item->form_type); ?>
                        </label>
                    </div>
                    <div class="dbt-form-row-column" style="position: relative;">
                        <?php if ($table_options->table_status == "DRAFT" && $item->field_name != $primary_key) : ?>
                            <input type="hidden" class="js-delete-field" name="fields_delete_column[<?php echo absint($count_fields); ?>]" value="">
                            <span class="dbt-warning-link" style="vertical-align:middle" onclick="dbt_form_remove_field(this)"><?php _e('DELETE FIELD', 'database_tables'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            
                <div class="dbt-structure-grid">
                    <div class="dbt-form-row-column">
                        <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Field Label','database_tables'); ?></span>
                            <input type="text" name="fields_label[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr( $label ); ?>" class="dbt-input">
                        </label>
                    </div>

                    <div class="dbt-form-row-column">
                        <label class="js-label-required" <?php echo (in_array(@$item->form_type, ['CREATION_DATE','LAST_UPDATE_DATE','RECORD_OWNER', 'MODIFYING_USER', 'CALCULATED_FIELD'])) ? 'style="display:none"' : '' ; ?>><span class="dbt-form-label"><?php _e('Required?','database_tables'); ?></span>
                            <input type="checkbox" name="fields_required[<?php echo absint($count_fields); ?>]" value="1" <?php echo ($item->required) ? 'checked="checked"' : ''; ?> class="dbt-input js-input-required">
                        </label>
                    </div>
                </div>

                <div class="dbt-structure-grid">
                    <div class="dbt-form-row-column">
                        <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Default Value','database_tables'); ?></span>
                            <input type="text" name="fields_default_value[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr($item->default_value); ?>" class="dbt-input">
                        </label>
                    </div>

                    <div class="dbt-form-row-column">
                        <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Custom css class','database_tables'); ?></span>
                            <input type="text" name="fields_custom_css_class[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr($item->custom_css_class); ?>" class="dbt-input">
                        </label>
                    </div>
                </div>

                <div class="dbt-form-row dbt-label-grid">
                    <label><span class="dbt-form-label"><?php _e('Field Note (optional)','database_tables'); ?></span></label>
                    <textarea class="dbt-input" style="width:100%" rows="1" name="fields_note[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea($item->note); ?></textarea>
                </div>

                <div class="dbt-structure-grid js-lf-options-content" style="display:<?php echo (in_array($item->form_type, ['SELECT','CHECKBOXES','RADIO'])) ? 'grid' : 'none'; ?>">
                    <div class="dbt-form-row-column">
                        <label><span class="dbt-form-label"><?php _e('Choices (one choice per line)','database_tables'); ?></span></label>
                        <textarea class="dbt-input" style="width:100%" rows="6" name="fields_options[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea(Dbt_fn::stringify_csv_options($item->options)); ?></textarea>
                    </div>
                    <div class="dbt-form-row-column"> 
                    <br>
                    <p>You may manually define teh coded value for each choice by entering the coded number and a comma before the chiuce label.
                    <pre>
        0, Not reported
        1, Male
        2, Female
        </pre> 
                    </p>
                    </div>
                </div>
                
                <div class="dbt-structure-grid js-lf-checkbox-value" style="display:<?php echo (in_array(@$item->form_type, ['CHECKBOX'])) ? 'grid' : 'none'; ?>">
                    <div class="dbt-form-row-column">
                        <label><span class="dbt-form-label"><?php _e('Checkbox value','database_tables'); ?></span></label>
                        <input class="dbt-input" style="width:100%" rows="6" name="fields_custom_value_checkbox[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr(@$item->custom_value); ?>">
                    </div>
                </div>

                <div class="dbt-structure-grid js-calculated-field-block" style="display:<?php echo (in_array(@$item->form_type, ['CALCULATED_FIELD'])) ? 'grid' : 'none'; ?>">
                    <div class="dbt-form-row-column">
                        <label><span class="dbt-form-label"><?php _e('Calculated Field: formula','database_tables'); ?></span></label>
                        <textarea class="dbt-input js-fields-custom-value-calc" style="width:100%" rows="3" name="fields_custom_value_calc[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea(@$item->custom_value); ?></textarea>
                        <div><span class="dbt-link-click" onclick="show_pinacode_vars()">show shortcode variables</span></div>
                    </div>
                    <div class="dbt-form-row-column"> 
                    <br>
                    <p>
                        <?php if ($total_row > 0) : ?>
                            <div class="dbt-form-row-column" style="margin-bottom:.5rem">
                            <label>Choose the record: <?php echo Dbt_fn::html_select($select_array_test, true, ' class="js-choose-test-row"'); ?>
                            </label>
                            <div class="button js-test-formula" onClick="click_dbt_test_formula(this);"><?php _e('Test formula', 'database_tables'); ?></div>
                        </div>
                            <div class="button" id="dbt_<?php  echo Dbt_fn::get_uniqid(); ?>" onClick="click_dbt_recalculate_formula(jQuery(this).prop('id'), 0, <?php echo $total_row ; ?>);"><?php _e('Recalculate and save all records', 'database_tables'); ?></div>
                        <?php endif; ?>
                    </p>
                    </div>
                </div>
                <?php
                if ($item->lookup_id > 0) {
                    $lookup_col_list = Dbt::get_list_columns($item->lookup_id);
                } else {
                    $lookup_col_list = [];
                }
                ?>
                <div class="js-dbt-lookup-data"<?php echo (@$item->form_type != 'LOOKUP') ? ' style="display:none"' : ''; ?> id="id<?php echo Dbt_fn::get_uniqid(); ?>">
                    <h3><?php _e('Lookup params','database_tables'); ?></h3>
                    <div class="dbt-structure-grid">
                        <div class="dbt-form-row-column">
                            <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Choose List','database_tables'); ?></span>
                            <?php echo Dbt_fn::html_select(Dbt::get_lists_names(), true, 'name="fields_lookup_id['. absint($count_fields) . ']" onchange="dbt_change_lookup_id(this)" class="js-select-fields-lookup"', @$item->lookup_id); ?>
                            </label>
                        </div>
                        <div class="dbt-form-row-column">
                            <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Label','database_tables'); ?></span>
                            <?php echo Dbt_fn::html_select($lookup_col_list, true, 'name="fields_lookup_sel_txt['. absint($count_fields) . ']"  class="js-lookup-select-text"', @$item->lookup_sel_txt); ?>
                            </label>
                            <input type="hidden" name="fields_lookup_sel_val[<?php echo absint($count_fields); ?>]" class="js-lookup-select-value" value="<?php echo esc_attr(@$item->lookup_sel_val); ?>">
                        </div>
                    </div>

                    <hr>
                </div>
                
                <div class="dbt-structure-grid js-dbt-post-data"<?php echo (@$item->form_type != 'POST') ? ' style="display:none"' : ''; ?>>
                    <div class="dbt-form-row-column">
                        <label class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Choose post types','database_tables'); ?></span>
                        <?php echo Dbt_fn::html_select($post_types, true, 'name="fields_post_types['. absint($count_fields) . ']" ', @$item->post_types); ?>
                        </label>
                    </div>
                    <div class="dbt-form-row-column">
                        Categories:<br>
                        <div class="dbt-form-box-cat">
                            <?php echo Dbt_functions_list::form_categ_tree(0, 0,absint($count_fields), @$item->post_cats); ?>
                        </div>
                    </div>
                </div>

                <div class="dbt-structure-grid js-dbt-user-data"<?php echo (@$item->form_type != 'USER') ? ' style="display:none"' : ''; ?>>
                    <div class="dbt-form-row-column">
                        <div class="dbt-label-grid dbt-css-mb-0"><span class="dbt-form-label"><?php _e('Roles','database_tables'); ?></span>
                       
                            <div class="dbt-form-box-cat">
                                <?php echo Dbt_functions_list::form_user_roles(absint($count_fields), @$item->user_roles); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dbt-structure-grid js-javascript-script-block"  style="display:<?php echo (!in_array(@$item->form_type, ['CALCULATED_FIELD'])) ? 'grid' : 'none'; ?>">
                    <div class="dbt-form-row-column">
                        <label><span class="dbt-form-label"><?php _e('JS Script','database_tables'); ?></span></label>
                        <textarea class="dbt-input js-field-js-script" style="width:100%" rows="3" name="fields_js_script[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea(@$item->js_script); ?></textarea>
                    </div>
                    <div class="dbt-form-row-column"> 
                    <br>
                    <p>Aggiungi uno script js. Questo viene invocato ogni volta che un elemento della form viene modificato. puoi usare la variabile field per il campo corrente. Guarda la guida per maggiori informazioni</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
}