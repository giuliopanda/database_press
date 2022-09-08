<?php
/**
 * Carico una singola tabella in list-form
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
foreach ($table['fields'] as $item) {
   
    if ($item->name == "_dbp_alias_table_") continue;
    $count_fields++;
    $label = (@$item->label) ? @$item->label : $item->name;
    $item_type_txt = dbp_fn::h_type2txt($item->type, false);
    $form_type_fields = ['Standard fields'=>['VARCHAR'=>'Text (single line)', 'TEXT'=>'Text (multi line)', 'DATE'=>'Date', 'DATETIME'=>'Date time' , 'NUMERIC'=>'Number', 'DECIMAL'=>'Decimal (9,2)', 'SELECT'=>'Multiple Choice - Drop-down List (Single Answer)', 'RADIO'=>'Multiple Choice - Radio Buttons (Single Answer)', 'CHECKBOX'=>'Checkbox (Single Answer)','CHECKBOXES'=>'Checkboxes (Multiple Answers)'], 'Special fields'=>['READ_ONLY'=>'Read only','EDITOR_CODE'=>'Editor Code','EDITOR_TINYMCE'=>'Classic text editor', 'CREATION_DATE'=>'Record creation date', 'LAST_UPDATE_DATE'=>'Last update date', 'RECORD_OWNER'=>'Author (who created the record)', 'MODIFYING_USER'=>'Modifying user', 'CALCULATED_FIELD'=>'calculated field', 'LOOKUP'=>'lookup', 'UPLOAD_FIELD' => 'Upload files in a custom dir'], 'Wordpress field' => ['POST'=>'post','USER'=>'user', 'MEDIA_GALLERY' => 'Media Gallery']];

    if ($item_type_txt == "DATE" || $item_type_txt == "DATETIME") {
        $form_type_fields = ['Standard fields'=>['VARCHAR'=>'Text (single line)', 'DATE'=>'Date', 'DATETIME'=>'Date time'], 'Special fields'=>['READ_ONLY'=>'Read only','CREATION_DATE'=>'Record creation date', 'LAST_UPDATE_DATE'=>'Last update date', 'CALCULATED_FIELD'=>'calculated field']];
    }
    if ($item_type_txt == "STRING") {
        $form_type_fields = ['Standard fields'=>['VARCHAR'=>'Text (single line)', 'DATE'=>'Date', 'DATETIME'=>'Date time' , 'NUMERIC'=>'Number', 'DECIMAL'=>'Decimal (9,2)','SELECT'=>'Multiple Choice - Drop-down List (Single Answer)', 'RADIO'=>'Multiple Choice - Radio Buttons (Single Answer)', 'CHECKBOX'=>'Checkbox (Single Answer)','CHECKBOXES'=>'Checkboxes (Multiple Answers)'], 'Special fields'=>['READ_ONLY'=>'Read only', 'CREATION_DATE'=>'Record creation date', 'LAST_UPDATE_DATE'=>'Last update date', 'RECORD_OWNER'=>'Author (who created the record)', 'MODIFYING_USER'=>'Modifying user', 'CALCULATED_FIELD'=>'calculated field', 'LOOKUP'=>'lookup', 'UPLOAD_FIELD' => 'Upload files in a custom dir'], 'Wordpress field' => ['POST'=>'post','USER'=>'user', 'MEDIA_GALLERY' => 'Media Gallery']];
    }
    if ($item_type_txt == "NUMBER") {
        $form_type_fields = ['Standard fields'=>['VARCHAR'=>'Text (single line)',  'NUMERIC'=>'Number', 'DECIMAL'=>'Decimal (9,2)', 'SELECT'=>'Multiple Choice - Drop-down List (Single Answer)', 'RADIO'=>'Multiple Choice - Radio Buttons (Single Answer)', 'CHECKBOX'=>'Checkbox (Single Answer)'], 'Special fields'=>['READ_ONLY'=>'Read only', 'RECORD_OWNER'=>'Author (who created the record)', 'MODIFYING_USER'=>'Modifying user', 'CALCULATED_FIELD'=>'calculated field', 'LOOKUP'=>'lookup'], 'Wordpress field' => ['POST'=>'post','USER'=>'user', 'MEDIA_GALLERY' => 'Media Gallery']];
    }
    if ($item_type_txt == "TINY") {
        $form_type_fields = ['Standard fields'=>['VARCHAR'=>'Text (single line)',  'NUMERIC'=>'Number', 'SELECT'=>'Multiple Choice - Drop-down List (Single Answer)', 'RADIO'=>'Multiple Choice - Radio Buttons (Single Answer)', 'CHECKBOX'=>'Checkbox (Single Answer)'], 'Special fields'=>[ 'CALCULATED_FIELD'=>'calculated field', 'LOOKUP'=>'lookup']];
    }
    if ($item->is_pri) {
        $form_type_fields = ['PRI' => 'Primary value', 'VARCHAR'=>'Text (single line)', 'NUMERIC'=>'Number', 'READ_ONLY'=>'Read only'];
    }
    $bool_precompiled = ($item->where_precompiled == 1 && $item->custom_value != '' );
    ?>
    <div class="js-dragable-fields dbp-lf-field_box js-dbp-lf-form-card<?php echo (@$item->edit_view=="HIDE") ? ' dbp-form-hide-field' : ''; ?>">
        <div class="dbp-lf-field-title">
            <span class="dbp-lf-handle js-dragable-handle"><span class="dashicons dashicons-sort"></span></span>
            <input type="hidden" class="js-dragable-order" name="fields_order[<?php echo  absint($count_fields); ?>]" value="<?php echo esc_attr(@$item->order); ?>">
            <span class="dbp-lf-edit-icon js-lf-edit-icon">
            <span class="dashicons dashicons-edit dbp-edit-icon js-dashicon-edit" onclick="dbp_lf_form_toggle(this)"></span>
            </span> 
            
            <span class="js-title-field">
                <?php if ($item->is_pri) : ?>
                    <span class="dashicons dashicons-admin-network" style="color:#e2c447; vertical-align: text-top;" title="Primary"></span>
                <?php endif;   ?>
                <?php echo '<b>'.$item->name . '</b> <span style="font-size:.9rem">('.$item_type_txt .')</span>'; ?>
                <?php echo (@$item->js_script != '') ? '<span class="dbp-jsicon">JS</span>' : ''; ?>
            </span>
            <?php 
            if ($bool_precompiled) {
                ?><span class="dbp-alert-warning"><?php _e('Automatically filled by the query','database_press'); ?></span><?php
            }
            ?>
            <input type="hidden" class="js-hidden-field-name" name="fields_name[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr($item->name); ?>">
            <input type="hidden" class="js-hidden-field-table" name="fields_table[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr($key); ?>">
            <input type="hidden" name="fields_orgtable[<?php echo absint($count_fields); ?>]" class="js-hidden-field-orgtable" value="<?php echo esc_attr($table['table_name']); ?>">
            <div style="margin-left:1rem; display: inline-block;">
                <?php echo dbp_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="fields_edit_view['. absint($count_fields) .']" class="js-show-hide-select" onChange="dbp_lf_select_onchange_toggle_field(this)"', @$item->edit_view); ?>
                <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','toggle'); ?>
            </div>
            <?php if ($table_options->table_status == "DRAFT") : ?>
                <div style="display:none;  vertical-align: middle; margin-left:1rem; cursor:pointer;" class="js-cancel-delete button" onclick="dbp_form_cancel_remove_field(this)"><?php _e('Restore the deleted field', 'database_press'); ?></div>
            <?php endif; ?>
        </div>
        <div class="dbp-structure-field-example js-lf-form-field-example ">
            <div class="js-dbp-example dbp-form-example-field dbp-form-edit-row"> </div>
        </div>
        <div class="dbp-structure-content js-lf-form-content" style="display:none">
            <?php if ($bool_precompiled ) : ?>
                <div class="js-structure-content-before">
                    <label class="dbp-alert-warning" style="display:block; margin-left:1rem;"><input type="checkbox" name="where_precompiled[<?php echo absint($count_fields); ?>]" value="1" checked="checked" onchange="dbp_where_precompiled(this)"><?php printf(__('Automatically calculate the value from the query. This is the formula entered: %s.','database_press'), @$item->custom_value); ?></label>
                </div>
            <?php endif; ?>
            <div class="js-structure-content-inside" <?php echo ($bool_precompiled) ? 'style="display:none"' : ''; ?>>
                <div class="dbp-structure-grid">
                    <div class="dbp-form-row-column">
                        <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Field Type','database_press'); ?></span>
                            <?php echo dbp_fn::html_select($form_type_fields, true, 'name="fields_form_type['. absint($count_fields) . ']" onchange="dbp_lf_select_type_change(this)" class="js-fields-field-type"', @$item->form_type); ?>
                        </label>
                    </div>
                    <div class="dbp-form-row-column" style="position: relative;">
                        <?php if ($table_options->table_status == "DRAFT" && $item->field_name != $primary_key) : ?>
                            <input type="hidden" class="js-delete-field" name="fields_delete_column[<?php echo absint($count_fields); ?>]" value="">
                            <span class="dbp-warning-link" style="vertical-align:middle" onclick="dbp_form_remove_field(this)"><?php _e('DELETE FIELD', 'database_press'); ?></span>
                            <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','delete'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            
                <div class="dbp-structure-grid">
                    <div class="dbp-form-row-column">
                        <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Field Label','database_press'); ?></span>
                            <input type="text" name="fields_label[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr( $label ); ?>" class="dbp-input js-fields-label">
                        </label>
                    </div>

                    <div class="dbp-form-row-column">
                        <label class="js-label-required" <?php echo (in_array(@$item->form_type, ['CREATION_DATE','LAST_UPDATE_DATE','RECORD_OWNER', 'MODIFYING_USER', 'CALCULATED_FIELD'])) ? 'style="display:none"' : '' ; ?>><span class="dbp-form-label"><?php _e('Required?','database_press'); ?></span>
                            <input type="checkbox" name="fields_required[<?php echo absint($count_fields); ?>]" value="1" <?php echo ($item->required) ? 'checked="checked"' : ''; ?> class="dbp-input js-input-required">
                        </label>
                    </div>
                </div>

                <div class="dbp-structure-grid">
                    <div class="dbp-form-row-column">
                        <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Default Value','database_press'); ?>
                        <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','default'); ?>
                        </span>
                            <input type="text" name="fields_default_value[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr($item->default_value); ?>" class="dbp-input">
                        </label>
                    </div>

                    <div class="dbp-form-row-column">
                        <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Custom css class','database_press'); ?>
                        <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','class'); ?>
                        </span>
                            <input type="text" name="fields_custom_css_class[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr($item->custom_css_class); ?>" class="dbp-input">
                        </label>
                    </div>
                </div>

                <div class="dbp-form-row dbp-label-grid">
                    <label><span class="dbp-form-label"><?php _e('Field Note (optional)','database_press'); ?></span></label>
                    <textarea class="dbp-input js-lf-fields-note" style="width:100%" rows="1" name="fields_note[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea($item->note); ?></textarea>
                </div>

                <div class="dbp-structure-grid js-lf-options-content" style="display:<?php echo (in_array($item->form_type, ['SELECT','CHECKBOXES','RADIO'])) ? 'grid' : 'none'; ?>">
                    <div class="dbp-form-row-column">
                        <label><span class="dbp-form-label"><?php _e('Choices (one choice per line)','database_press'); ?></span></label>
                        <textarea class="dbp-input js-fields-options" style="width:100%" rows="6" name="fields_options[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea(dbp_fn::stringify_csv_options($item->options)); ?></textarea>
                    </div>
                    <div class="dbp-form-row-column"> 
                    <br>
                    <p>You can manually define the encoded value for each choice by inserting the encoded number and a comma before the choice's label
                    <pre>
        0, Not reported
        1, Male
        2, Female
        </pre> 
                    </p>
                    </div>
                </div>
                
                <div class="dbp-structure-grid js-lf-checkbox-value" style="display:<?php echo (in_array(@$item->form_type, ['CHECKBOX'])) ? 'grid' : 'none'; ?>">
                    <div class="dbp-form-row-column">
                        <label><span class="dbp-form-label"><?php _e('Checkbox value','database_press'); ?></span></label>
                        <input class="dbp-input" style="width:100%" rows="6" name="fields_custom_value_checkbox[<?php echo absint($count_fields); ?>]" value="<?php echo esc_attr(@$item->custom_value); ?>">
                    </div>
                </div>

                <div class="dbp-structure-grid js-calculated-field-block" style="display:<?php echo (in_array(@$item->form_type, ['CALCULATED_FIELD'])) ? 'grid' : 'none'; ?>">
                    <div class="dbp-form-row-column">
                        <div>
                            <label><span class="dbp-form-label"><?php _e('Calculated Field: formula','database_press'); ?>
                            <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','calc_field'); ?></span></label>
                            <textarea class="dbp-input js-fields-custom-value-calc" style="width:100%" rows="3" name="fields_custom_value_calc[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea(@$item->custom_value); ?></textarea>
                            <div><span class="dbp-link-click" onclick="show_pinacode_vars()">show shortcode variables</span></div>
                        </div>
                        <div style="margin-top:1rem">
                        <?php echo dbp_fn::html_select(['EMPTY'=>'Calculate the formula only when the field is empty.','EVERY_TIME'=>'Recalculate the formula each time you save'], true, ' name="fields_custom_value_calc_when['.absint($count_fields).']"', @$item->custom_value_calc_when); ?>
                    
                        </label>
                        </div>
                    </div>
                    <div class="dbp-form-row-column"> 
                    <br>
                    <p>
                        <?php if ($total_row > 0) : ?>
                            <div class="dbp-form-row-column" style="margin-bottom:.5rem">
                            <label>Choose the record: <?php echo dbp_fn::html_select($select_array_test, true, ' class="js-choose-test-row"'); ?>
                            </label>
                            <div class="button js-test-formula" onClick="click_dbp_test_formula(this);"><?php _e('Test formula', 'database_press'); ?></div>
                        </div>
                            <div class="button" id="dbp_<?php  echo dbp_fn::get_uniqid(); ?>" onClick="click_dbp_recalculate_formula(jQuery(this).prop('id'), 0, <?php echo $total_row ; ?>);"><?php _e('Recalculate and save all records', 'database_press'); ?></div>
                        <?php endif; ?>
                    </p>
                    </div>
                </div>
                <?php
                /*
                if ($item->lookup_id > 0) {
                    $lookup_col_list = Dbp::get_list_columns($item->lookup_id);
                } else {
                    $lookup_col_list = [];
                }
                */

                if ( $item->lookup_id != '') {
                    $lookup_col_list = Dbp_fn::get_table_structure($item->lookup_id, true);
                    $primary = Dbp_fn::get_primary_key($item->lookup_id);
                    $pos = array_search($primary, $lookup_col_list);
                    if ($pos !== false) {
                        unset($lookup_col_list[$pos]);
                    }

                } else {
                    $lookup_col_list = [];
                }
                $list_of_tables = Dbp_fn::get_table_list();
                ?>
                <div class="js-dbp-lookup-data"<?php echo (@$item->form_type != 'LOOKUP') ? ' style="display:none"' : ''; ?> id="id<?php echo dbp_fn::get_uniqid(); ?>">
                    <h3><?php _e('Lookup params','database_press'); ?>
                    <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','lookup'); ?>
                    </h3>
                    <div class="dbp-structure-grid">
                        <div class="dbp-form-row-column">
                            <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Choose Table','database_press'); ?></span>
                            <?php echo Dbp_fn::html_select($list_of_tables['tables'], true, 'name="fields_lookup_id['. absint($count_fields) . ']" onchange="dbp_change_lookup_id(this)" class="js-select-fields-lookup"', @$item->lookup_id); ?>
                            </label>
                        </div>
                        <div class="dbp-form-row-column">
                            <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Label','database_press'); ?></span>
                            <?php echo Dbp_fn::html_select($lookup_col_list, false, 'name="fields_lookup_sel_txt['. absint($count_fields) . ']"  class="js-lookup-select-text"', @$item->lookup_sel_txt); ?>
                            </label>
                            <input type="hidden" name="fields_lookup_sel_val[<?php echo absint($count_fields); ?>]" class="js-lookup-select-value" value="<?php echo esc_attr(@$item->lookup_sel_val); ?>">
                        </div>
                    </div>
                    <div class="dbp-form-row dbp-label-grid">
                        <label><span class="dbp-form-label"><?php _e('Query WHERE part (optional)','database_press'); ?></span></label>
                        <div>
                        <textarea class="dbp-input js-lookup-where" style="width:100%; margin-bottom:.5rem" rows="1" name="fields_lookup_where[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea(@$item->lookup_where); ?></textarea>
                        <?php $id_test_lookup = 'dbpl_' . Dbp_fn::get_uniqid() ;?>
                        <span class="dbp-link-click" onclick="btn_lookup_test_query(this,'<?php echo esc_attr($id_test_lookup); ?>')"><?php _e('Query test','database_press'); ?></span>
                        <span id="<?php echo esc_attr($id_test_lookup); ?>" style="margin-left:1rem"></span>
                        </div>
                    </div>
                    <hr>
                </div>
                
                <div class="dbp-structure-grid js-dbp-post-data"<?php echo (@$item->form_type != 'POST') ? ' style="display:none"' : ''; ?>>
                    <div class="dbp-form-row-column">
                        <label class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Choose post types','database_press'); ?></span>
                        <?php echo dbp_fn::html_select($post_types, true, 'name="fields_post_types['. absint($count_fields) . ']" ', @$item->post_types); ?>
                        </label>
                    </div>
                    <div class="dbp-form-row-column">
                        Categories:<br>
                        <div class="dbp-form-box-cat">
                            <?php echo dbp_functions_list::form_categ_tree(0, 0,absint($count_fields), @$item->post_cats); ?>
                        </div>
                    </div>
                </div>

                <div class="dbp-structure-grid js-dbp-user-data"<?php echo (@$item->form_type != 'USER') ? ' style="display:none"' : ''; ?>>
                    <div class="dbp-form-row-column">
                        <div class="dbp-label-grid dbp-css-mb-0"><span class="dbp-form-label"><?php _e('Roles','database_press'); ?></span>
                       
                            <div class="dbp-form-box-cat">
                                <?php echo dbp_functions_list::form_user_roles(absint($count_fields), @$item->user_roles); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dbp-structure-grid js-javascript-script-block"  style="display:<?php echo (!in_array(@$item->form_type, ['CALCULATED_FIELD'])) ? 'grid' : 'none'; ?>">
                    <div class="dbp-form-row-column">
                        <label>
                            <span class="dbp-form-label"><?php _e('JS Script','database_press'); ?>
                                <?php dbp_fn::echo_html_icon_help('dbp_list-list-form','js'); ?>
                            </span>
                        </label>
                        <textarea class="dbp-input js-field-js-script" style="width:100%" rows="3" name="fields_js_script[<?php echo absint($count_fields); ?>]"><?php echo esc_textarea(@$item->js_script); ?></textarea>
                    </div>
                    <div class="dbp-form-row-column"> 
                    <br>
                    <p>Add a javascript script to choose whether to show or hide the field or to validate its content.<a href="<?php echo admin_url("admin.php?page=dbp_docs&section=js-controller-form") ?>" target="_blank">Read the guide for more information</a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
}