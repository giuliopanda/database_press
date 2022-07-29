<?php
/**
 * La grafica del tab list view formatting
 * /admin.php?page=dbt_list&section=list-structure&dbt_id=xxx
 * Tutte le configurazioni di una lista
 * 
 * @var $items Lo schema della tabella
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
$append = '<span class="dbt-submit" onclick="dbt_submit_list_structure()">' . __('Save', 'database_tables') . '</span>';

?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
</div>
<div class="dbt-content-table js-id-dbt-content">
    <?php if ($dtf::echo_html_title_box('list', $list_title, '', $msg, $msg_error, $append)) : ?>
        <form id="list_structure" method="POST" action="<?php echo admin_url("admin.php?page=dbt_list&section=list-structure&dbt_id=".$id); ?>">
            <input type="hidden" name="action" value="list-structure-save" />
            <input type="hidden" name="table" value="<?php echo @$import_table; ?>" />
            
            <div class="dbt-content-margin">
                <div class="js-clore-master" id="clone_master">
                    <div class="dbt-structure-title" >
                        
                        <span class="dbt-form-label js-dragable-handle"><span class="dashicons dashicons-sort"></span></span>
                        <input class="js-dragable-order" name="fields_order[]" value=""></label>
                        <span class="dbt-lf-edit-icon">
                            <span class="dashicons dashicons-edit js-structure-toggle" onclick="dbt_structure_toggle(this)"></span>
                        </span>
                        <b onclick="dbt_structure_toggle(this)"><?php _e('CUSTOM COLUMN', 'database_tables'); ?></b>

                        <span class="button"  onClick="dbt_list_structure_delete_row(this);"><?php _e('DELETE', 'database_tables'); ?></span>
                   
                        <span class="dbt-structure-title-label"><span><?php _e('Show in frontend','database_tables'); ?></span>
                        <?php echo Dbt_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="fields_toggle[]" onchange="dbt_change_toggle(this)"  class="js-toggle-row"'); ?>
                        
                    </div>
                    <div class="dbt-structure-content js-structure-content" style="display:none" >
                        <div class="dbt-structure-grid">
                            <div class="dbt-form-row-column">
                                <label><span class="dbt-form-label"><?php _e('Table title','database_tables'); 
                                Dbt_fn::echo_html_icon_help('dbt_list-list-structure','title');
                                ?>
                                </span>
                                    <input type="text" name="fields_title[]" value="" class="js-title dbt-input">
                                </label>
                                <input type="hidden" name="fields_origin[]" value="CUSTOM">
                            </div>
                            
                            <div class="dbt-form-row-column">
                                <label><span class="dbt-form-label"><?php _e('Name in url (for request)','database_tables'); ?></span>
                                    <input type="text" disabled value="" class="dbt-input">
                                    <input type="hidden" name="fields_name_request[]" value="" class=" dbt-input">
                                    </label>
                                </label>
                                <input type="hidden" name="fields_mysql_name[]" value="">
                            </div>

                            <div class="dbt-form-row-column">
                                <label><span class="dbt-form-label "><?php _e('Column dimension','database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select([''=>'', 'min'=>'Min','regular'=>'Regular','large' => 'Large', 'extra-large'=>'Extra large' ], true, 'name="fields_width[]" class="js-width-fields"'); ?>
                                </label>
                            </div>

                            <div class="dbt-form-row-column">
                                <label><span class="dbt-form-label "><?php _e('Searchable','database_tables'); 
                                Dbt_fn::echo_html_icon_help('dbt_list-list-structure','searchable');
                                    ?></span>
                                    <input type="text" disabled value="No" class="dbt-input">
                                    <input type="hidden" name="fields_searchable[]" value="no" class="dbt-input">
                                </label>
                            </div>
                        </div>
                        <div class="dbt-form-row js-form-row-custom-field">
                            <label><span class="dbt-form-label "><?php _e('Column type','database_tables'); 
                                Dbt_fn::echo_html_icon_help('dbt_list-list-structure','print');
                                ?></span>
                                <div style="display:inline-block; min-width:80%">
                                    <input type="hidden" name="fields_custom_view[]" class="js-type-fields" onchange="dbt_change_custom_type(this)" value="CUSTOM">
                                    <textarea name="fields_custom_code[]" class="js-type-custom-code dbt-input" rows="2" style="display:inline-block; width:80%"></textarea>
                                    <div><span class="dbt-link-click" onclick="show_pinacode_vars()">show shortcode variables</span></div>
                                </div>
                            </label>
                        </div>

                        <h3>column formatting</h3>
                        <div class="dbt-structure-grid">
                            <div class="dbt-form-row-column js-form-row-custom-field">
                                <label><span class="dbt-form-label" style="vertical-align:top"><?php _e('change values','database_tables'); Dbt_fn::echo_html_icon_help('dbt_list-list-structure','format'); ?></span>
                                </label>
                                <div style="display:inline-block; min-width:50%">
                                    <textarea name="fields_format_values[<?php echo esc_attr($item->name); ?>]" class="dbt-input" rows="4" style=" width:80%; min-width:250px"></textarea>
                                </div>
                            </div>
                            <div class="dbt-form-row-column js-form-row-custom-field">
                                <label><span class="dbt-form-label" style="vertical-align:top"><?php _e('change styles','database_tables'); Dbt_fn::echo_html_icon_help('dbt_list-list-structure','styles'); ?></span>
                                </label>
                                <div style="display:inline-block; min-width:50%">
                                    <textarea name="fields_format_styles[<?php echo esc_attr($item->name); ?>]" class="dbt-input" rows="4" style="width:80%; min-width:250px"></textarea>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="js-dragable-table">
                        
                    <?php $names = [];
                    foreach ($items as $key=>$item) : 
                        //print "<pre>";
                        //var_dump ($item);
                        //print "</pre>"; 
                        ?>
                        <div class="js-dragable-tr dbt-structure-card js-dbt-structure-card">
                            <div class="dbt-structure-title" >
                              
                                <span class="dbt-form-label js-dragable-handle"><span class="dashicons dashicons-sort"></span></span>
                                <input class="js-dragable-order" name="fields_order[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->order); ?>"></label>
                                <span class="dbt-lf-edit-icon">
                                <span class="dashicons dashicons-edit js-structure-toggle" onclick="dbt_structure_toggle(this)"></span>
                                </span>
                                <span onclick="dbt_structure_toggle(this)"><?php echo ($item->mysql_name) ? '<b>'.$item->title.'</b> - <span title="mysql column">'. $item->mysql_name.'</span>' : '<b>'.$item->title.'</b>'; ?></span>
                                
                                <span class="dbt-structure-type" onclick="dbt_structure_toggle(this)">(<?php echo $item->type; ?>)</span>
                                <?php if ($item->origin == "CUSTOM") : ?>
                                    <span class="button" onClick="dbt_list_structure_delete_row(this);"><?php _e('DELETE', 'database_tables'); ?></span>
                                <?php endif; ?>
                                <span class="dbt-structure-title-label"><span><?php _e('Show in frontend','database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="fields_toggle['. esc_attr($item->name) . ']" onchange="dbt_change_toggle(this)"  class="js-toggle-row"', $item->toggle); ?>
                               
                            </div>
                            <div class="dbt-structure-content js-structure-content" >
                                <div class="dbt-structure-grid">
                                    <div class="dbt-form-row-column">
                                        <label><span class="dbt-form-label"><?php _e('Table title','database_tables'); 
                                        Dbt_fn::echo_html_icon_help('dbt_list-list-structure','title');
                                        ?></span>
                                            <input type="text" name="fields_title[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->title); ?>" class="js-title dbt-input">
                                        </label>
                                        <input type="hidden" name="fields_origin[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->origin); ?>">
                                    </div>
                                    
                                    <div class="dbt-form-row-column">
                                        <label><span class="dbt-form-label"><?php _e('Name in url (for request)','database_tables'); ?></span>
                                            <input type="text" disabled value="<?php echo esc_attr(@$item->name_request); ?>" class="dbt-input">
                                            <input type="hidden" name="fields_name_request[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->name_request); ?>" class=" dbt-input">
                                            </label>
                                        </label>
                                        <input type="hidden" name="fields_mysql_name[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->mysql_name); ?>">
                                        <input type="hidden" name="fields_mysql_table[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->mysql_table); ?>">
                                    </div>

                                    <div class="dbt-form-row-column">
                                        <label><span class="dbt-form-label "><?php _e('Column dimension','database_tables'); ?></span>
                                        <?php echo Dbt_fn::html_select([''=>'', 'min'=>'Min','regular'=>'Regular','large' => 'Large', 'extra-large'=>'Extra large' ], true, 'name="fields_width['. esc_attr($item->name).']" class="js-width-fields"', $item->width); ?>
                                        </label>
                                    </div>

                                    <div class="dbt-form-row-column">
                                        <label><span class="dbt-form-label "><?php _e('Searchable','database_tables'); 
                                        Dbt_fn::echo_html_icon_help('dbt_list-list-structure','searchable');
                                        ?></span>
                                        <?php if ($item->mysql_name != "") : ?>
                                        <?php echo Dbt_fn::html_select(['no'=>'No', 'yes'=>'YES', 'LIKE' => 'the exact phrase as substring (%LIKE%)','='=>'the exact phrase as whole field (=)' ], true, 'name="fields_searchable['. esc_attr($item->name).']" class="js-width-fields"', $item->searchable); ?>
                                        <?php else : ?>
                                            <input type="text" disabled value="No" class="dbt-input">
                                            <input type="hidden" name="fields_searchable[<?php echo esc_attr($item->name); ?>]" value="no" class="dbt-input">
                                        <?php endif; ?>
                                        </label>
                                    </div>
                               
                                    
                                    <div class="dbt-form-row-column js-form-row-custom-field">
                                        <label><span class="dbt-form-label" style="vertical-align:top"><?php _e('Column type','database_tables'); 
                                        Dbt_fn::echo_html_icon_help('dbt_list-list-structure','print');
                                        ?></span>
                                        </label>
                                        <div style="display:inline-block; min-width:50%">
                                            <?php
                                            if ($item->origin == "FIELD") {
                                            echo Dbt_fn::html_select(['Standard field'=>[ 'TEXT'=>'Text', 'HTML'=>'Html', 'DATE'=>'Date', 'DATETIME'=>'Date Time', 'IMAGE'=>'Image','LINK'=>'External link', 'DETAIL_LINK' => 'Detail Link', 'SERIALIZE'=>'Serialiaze', 'JSON_LABEL'=>'Checkboxes'],'Special Fields' =>['CUSTOM'=>'Custom','LOOKUP'=>'Lookup','USER' => 'User', 'POST' => 'Post']] , true, 'name="fields_custom_view['. esc_attr($item->name).']" class="js-type-fields" onchange="dbt_change_custom_type(this)" style="display:'. (($item->view =='CUSTOM') ? 'none' :'inline-block').'"', $item->view); 
                                            } else {
                                                ?><input type="hidden" name="fields_custom_view[<?php echo esc_attr($item->name); ?>]" class="js-type-fields" onchange="dbt_change_custom_type(this)" value="CUSTOM"><?php
                                            }
                                            ?> 
                                            <textarea name="fields_custom_code[<?php echo esc_attr($item->name); ?>]" class="js-type-custom-code dbt-input" rows="2" style="display:<?php echo ($item->view =='CUSTOM') ? 'inline-block' :'none'; ?>; width:80%; min-width:250px"><?php echo esc_textarea($item->custom_code); ?></textarea>
                                            
                                            <?php if ($item->origin == "FIELD") : ?>
                                            <div class="dashicons dashicons-list-view dbt-input-button js-textarea-btn-cancel"  style="display:<?php echo ($item->view =='CUSTOM') ? 'inline-block' :'none'; ?>" onclick="dbt_custom_cancel(this)"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="dbt-form-row-column js-dbt-params-column">
                                        <label>
                                            <span class="dbt-form-label js-dbt-params-date"><?php _e('Date format','database_tables'); ?></span>
                                            <span class="dbt-form-label js-dbt-params-link"><?php _e('Link text','database_tables'); ?></span>
                                            <span class="dbt-form-label js-dbt-params-user"><?php _e('Show user attributes [%user.]','database_tables'); Dbt_fn::echo_html_icon_help('dbt_list-list-structure','user'); ?></span>
                                            <span class="dbt-form-label js-dbt-params-post"><?php _e('Show post attributes [%post.]','database_tables'); Dbt_fn::echo_html_icon_help('dbt_list-list-structure','post'); ?></span>
                                            <span class="dbt-form-label js-dbt-params-text"><?php _e('Max text length','database_tables'); ?></span>
                                            <input type="text" name="fields_custom_param[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->custom_param); ?>" class="js-input-parasm-custom dbt-input">
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="js-lookup-params" id="id<?php echo Dbt_fn::get_uniqid(); ?>">
                                    <h3 class="dbt-css-mb-1">Looup params</h3>
                                    <div class="dbt-form-row-column dbt-css-mb-1">
                                        <?php _e('Choose the list and column on which to compare the data','database_tables'); ?>
                                    </div>
                                    <?php
                                    if ( $item->lookup_id > 0) {
                                        $lookup_col_list = Dbt::get_list_columns($item->lookup_id, false);

                                    } else {
                                        $lookup_col_list = [];
                                    }
                                    ?>
                                    <div class="dbt-structure-grid">
                                        <div class="dbt-form-row-column">
                                            <div class="dbt-form-row-column dbt-css-mb-2">
                                                <label><span class="dbt-form-label" style="vertical-align:top"><?php _e('List:','database_tables'); ?></span>
                                                </label>
                                                <div style="display:inline-block; min-width:50%">
                                                <?php echo Dbt_fn::html_select(Dbt::get_lists_names(), true, 'name="fields_lookup_id[' . esc_attr($item->name) . ']" onchange="dbt_list_change_lookup_id(this)" class="js-select-fields-lookup"', $item->lookup_id); ?>
                                                </div>
                                                
                                                <input type="hidden"  name="fields_lookup_sel_val[<?php echo esc_attr($item->name); ?>]" value="<?php echo $item->lookup_sel_val; ?>" class="dbt-input js-lookup-select-value">
                                            </div>

                                        </div>
                                        <div class="dbt-form-row-column">
                                            <label><span class="dbt-form-label" style="vertical-align:top"><?php _e('Show','database_tables'); ?></span>
                                            </label>
                                            <div style="display:inline-block; min-width:50%">
                                                <?php echo Dbt_fn::html_select($lookup_col_list, true, 'name="fields_lookup_sel_txt[' . esc_attr($item->name) . ']" class="dbt-input js-lookup-select-text"', $item->lookup_sel_txt); ?>
                                            </div>
                                        </div>
                               
                                    </div>
                                </div>
                                    
                                <h3>column formatting</h3>
                                   
                                <div class="dbt-structure-grid">
                                    <div class="dbt-form-row-column js-form-row-custom-field">
                                        <label><span class="dbt-form-label" style="vertical-align:top"><?php _e('change values','database_tables'); Dbt_fn::echo_html_icon_help('dbt_list-list-structure','format');?></span>
                                        </label>
                                        <div style="display:inline-block; min-width:50%">
                                            <textarea name="fields_format_values[<?php echo esc_attr($item->name); ?>]" class="dbt-input" rows="4" style=" width:80%; min-width:250px"><?php echo esc_textarea($item->format_values); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="dbt-form-row-column js-form-row-custom-field">
                                        <label><span class="dbt-form-label" style="vertical-align:top"><?php _e('change styles','database_tables'); Dbt_fn::echo_html_icon_help('dbt_list-list-structure','styles') ?></span>
                                        </label>
                                        <div style="display:inline-block; min-width:50%">
                                            <textarea name="fields_format_styles[<?php echo esc_attr($item->name); ?>]" class="dbt-input" rows="4" style="width:80%; min-width:250px"><?php echo esc_textarea($item->format_styles); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                       
                    <?php endforeach ;?>
                    
                  
                  
                </div>

                <div style="margin:1rem; border-top:1px solid #dcdcde; padding-top:.5rem">
                <div onclick="dbt_list_structure_add_row(this)" class="button"><?php _e('Add row', 'database_tables'); ?></div>
                </div>
                    
                <hr>
                <h3 class="dbt-h3">General Setting</h3>
                <div class="dbt-form-row">
                    <label><span class="dbt-form-label "><?php echo _e('Max text length', 'database_tables'); ?></span>
                    <input type="number" name="list_general_setting[text_length]" class="dbt-input" value="<?php echo esc_attr($post->post_content['list_general_setting']['text_length']); ?>"></label>
                </div>
                <div class="dbt-form-row">
                    <label><span class="dbt-form-label "><?php echo _e('Object depth', 'database_tables'); ?></span>
                    <?php  echo Dbt_fn::html_select([1,2,3,4,5,6,7,8,9], false, 'name="list_general_setting[obj_depth]" class="js-type-fields" >', $post->post_content['list_general_setting']['obj_depth']); ?>
                    </label>
                </div>
                <div class="dbt-submit" onclick="dbt_submit_list_structure();"><?php _e('Save','database_tables'); ?></div>
            </div>
        </form>
    <?php endif; ?>
</div>
