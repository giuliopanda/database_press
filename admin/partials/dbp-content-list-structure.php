<?php
/**
 * La grafica del tab list view formatting
 * /admin.php?page=dbp_list&section=list-structure&dbp_id=xxx
 * Tutte le configurazioni di una lista
 * 
 * @var $items Lo schema della tabella
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
$append = '<span class="dbp-submit" onclick="dbp_submit_list_structure()">' . __('Save', 'database_press') . '</span>';

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
        <form id="list_structure" method="POST" action="<?php echo admin_url("admin.php?page=dbp_list&section=list-structure&dbp_id=".$id); ?>">
            <input type="hidden" name="action" value="list-structure-save" />
            <input type="hidden" name="table" value="<?php echo (isset($import_table)) ? $import_table : ''; ?>" />
            
            <div class="dbp-content-margin">
                <div class="js-clore-master" id="clone_master">
                    <div class="dbp-structure-title" >
                        
                        <span class="dbp-form-label js-dragable-handle"><span class="dashicons dashicons-sort"></span></span>
                        <input class="js-dragable-order" name="fields_order[]" value=""></label>
                        <span class="dbp-lf-edit-icon">
                            <span class="dashicons dashicons-edit js-structure-toggle" onclick="dbp_structure_toggle(this)"></span>
                        </span>
                        <b onclick="dbp_structure_toggle(this)"><?php _e('CUSTOM COLUMN', 'database_press'); ?></b>

                        <span class="button"  onClick="dbp_list_structure_delete_row(this);"><?php _e('DELETE', 'database_press'); ?></span>
                   
                        
                        <?php echo dbp_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="fields_toggle[]" onchange="dbp_change_toggle(this)"  class="js-toggle-row"'); ?>
                        
                    </div>
                    <div class="dbp-structure-content js-structure-content" style="display:none" >
                        <div class="dbp-structure-grid">
                            <div class="dbp-form-row-column">
                                <label><span class="dbp-form-label"><?php _e('Table title','database_press'); 
                                dbp_fn::echo_html_icon_help('dbp_list-list-structure','title');
                                ?>
                                </span>
                                    <input type="text" name="fields_title[]" value="" class="js-title dbp-input">
                                </label>
                                <input type="hidden" name="fields_origin[]" value="CUSTOM">
                            </div>
                            
                            <div class="dbp-form-row-column">
                                <label><span class="dbp-form-label"><?php _e('Name in url (for request)','database_press'); ?></span>
                                    <input type="text" disabled value="" class="dbp-input">
                                    <input type="hidden" name="fields_name_request[]" value="" class=" dbp-input">
                                    </label>
                                </label>
                                <input type="hidden" name="fields_mysql_name[]" value="">
                            </div>

                            <div class="dbp-form-row-column">
                                <label><span class="dbp-form-label "><?php _e('Column dimension','database_press'); ?></span>
                                <?php echo dbp_fn::html_select(['extra-small'=>'Extra small', 'small'=>'Small','regular'=>'Regular','large' => 'Large', 'extra-large'=>'Extra large'], true, 'name="fields_width[]" class="js-width-fields"'); ?>
                                </label>
                            </div>

                            <div class="dbp-form-row-column">
                                <label><span class="dbp-form-label "><?php _e('Searchable','database_press'); 
                                dbp_fn::echo_html_icon_help('dbp_list-list-structure','searchable');
                                    ?></span>
                                    <input type="text" disabled value="No" class="dbp-input">
                                    <input type="hidden" name="fields_searchable[]" value="no" class="dbp-input">
                                </label>
                            </div>
                        </div>
                        <div class="dbp-form-row js-form-row-custom-field">
                            <label><span class="dbp-form-label "><?php _e('Column type','database_press'); 
                                dbp_fn::echo_html_icon_help('dbp_list-list-structure','print');
                                ?></span>
                                <div style="display:inline-block; min-width:80%">
                                    <input type="hidden" name="fields_custom_view[]" class="js-type-fields" onchange="dbp_change_custom_type(this)" value="CUSTOM">
                                    <textarea name="fields_custom_code[]" class="js-type-custom-code dbp-input" rows="2" style="display:inline-block; width:80%"></textarea>
                                    <div><span class="dbp-link-click" onclick="show_pinacode_vars()">show shortcode variables</span></div>
                                </div>
                            </label>
                        </div>

                        <h3>column formatting</h3>
                        <div class="dbp-structure-grid">
                            <div class="dbp-form-row-column js-form-row-custom-field">
                                <label><span class="dbp-form-label" style="vertical-align:top"><?php _e('change values','database_press'); dbp_fn::echo_html_icon_help('dbp_list-list-structure','format'); ?></span>
                                </label>
                                <div style="display:inline-block; min-width:50%">
                                    <textarea name="fields_format_values[<?php echo esc_attr($item->name); ?>]" class="dbp-input" rows="4" style=" width:80%; min-width:250px"></textarea>
                                </div>
                            </div>
                            <div class="dbp-form-row-column js-form-row-custom-field">
                                <label><span class="dbp-form-label" style="vertical-align:top"><?php _e('change styles','database_press'); dbp_fn::echo_html_icon_help('dbp_list-list-structure','styles'); ?></span>
                                </label>
                                <div style="display:inline-block; min-width:50%">
                                    <textarea name="fields_format_styles[<?php echo esc_attr($item->name); ?>]" class="dbp-input" rows="4" style="width:80%; min-width:250px"></textarea>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="js-dragable-table">
                        
                    <?php $names = [];
                    foreach ($items as $key=>$item) : 
                        $pri = ($item->table != "" && isset($primaries[$item->orgtable]) && strtolower($primaries[$item->orgtable]) == strtolower($item->orgname));
                        ?>
                        <div class="js-dragable-tr dbp-structure-card js-dbp-structure-card">
                            <div class="dbp-structure-title" >
                                <span class="dbp-form-label js-dragable-handle"><span class="dashicons dashicons-sort"></span></span>
                                <input class="js-dragable-order" name="fields_order[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->order); ?>"></label>
                              
                                <span class="dbp-lf-edit-icon">
                                <span class="dashicons dashicons-edit js-structure-toggle" onclick="dbp_structure_toggle(this)"></span>
                                </span>
                                <?php if ($pri) : ?><span class="dashicons dashicons-admin-network" title="Primary"></span><?php endif; ?>
                                <span onclick="dbp_structure_toggle(this)"><?php echo ($item->mysql_name) ? '<b>'.$item->title.'</b> - <span title="mysql column: '.esc_attr($item->mysql_name).'" >'. substr($item->name,0,70 - strlen($item->title)).'</span>' : '<b>'.$item->title.'</b>'; ?></span>  
                                <span class="dbp-structure-type" onclick="dbp_structure_toggle(this)">(<?php echo $item->type; ?>)</span>
                                <?php if ($item->origin == "CUSTOM") : ?>
                                    <span class="button" onClick="dbp_list_structure_delete_row(this);"><?php _e('DELETE', 'database_press'); ?></span>
                                <?php endif; ?>
                                <span class="dbp-structure-title-label">
                                <?php echo dbp_fn::html_select(['SHOW'=>'Show', 'HIDE'=>'Hide'], true, 'name="fields_toggle['. esc_attr($item->name) . ']" onchange="dbp_change_toggle(this)"  class="js-toggle-row"', $item->toggle); ?>
                               
                            </div>
                            <div class="dbp-structure-content js-structure-content" >
                                <div class="dbp-structure-grid">
                                    <div class="dbp-form-row-column">
                                        <label><span class="dbp-form-label"><?php _e('Table title','database_press'); 
                                        dbp_fn::echo_html_icon_help('dbp_list-list-structure','title');
                                        ?></span>
                                            <input type="text" name="fields_title[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->title); ?>" class="js-title dbp-input">
                                        </label>
                                        <input type="hidden" name="fields_origin[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->origin); ?>">
                                    </div>
                                    
                                    <div class="dbp-form-row-column">
                                        <label><span class="dbp-form-label"><?php _e('Name in url (for request)','database_press'); ?></span>
                                            <input type="text" disabled value="<?php echo esc_attr(@$item->name_request); ?>" class="dbp-input">
                                            <input type="hidden" name="fields_name_request[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->name_request); ?>" class=" dbp-input">
                                            </label>
                                        </label>
                                        <input type="hidden" name="fields_mysql_name[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->mysql_name); ?>">
                                        <input type="hidden" name="fields_mysql_table[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->mysql_table); ?>">
                                    </div>

                                    <div class="dbp-form-row-column">
                                        <label><span class="dbp-form-label "><?php _e('Column dimension','database_press'); ?></span>
                                        <?php echo dbp_fn::html_select(['extra-small'=>'Extra small', 'small'=>'Small','regular'=>'Regular','large' => 'Large', 'extra-large'=>'Extra large'], true, 'name="fields_width['. esc_attr($item->name).']" class="js-width-fields"', $item->width); ?>
                                        </label>
                                    </div>

                                    <div class="dbp-form-row-column">
                                        <label><span class="dbp-form-label "><?php _e('Searchable','database_press'); 
                                        dbp_fn::echo_html_icon_help('dbp_list-list-structure','searchable');
                                        ?></span>
                                        <?php if ($item->mysql_name != "") : ?>
                                        <?php echo dbp_fn::html_select(['no'=>'No', 'yes'=>'YES', 'LIKE' => 'the exact phrase as substring (%LIKE%)','='=>'the exact phrase as whole field (=)' ], true, 'name="fields_searchable['. esc_attr($item->name).']" class="js-width-fields"', $item->searchable); ?>
                                        <?php else : ?>
                                            <input type="text" disabled value="No" class="dbp-input">
                                            <input type="hidden" name="fields_searchable[<?php echo esc_attr($item->name); ?>]" value="no" class="dbp-input">
                                        <?php endif; ?>
                                        </label>
                                    </div>
                               
                                    
                                    <div class="dbp-form-row-column js-form-row-custom-field">
                                        <label><span class="dbp-form-label" style="vertical-align:top"><?php _e('Column type','database_press'); 
                                        dbp_fn::echo_html_icon_help('dbp_list-list-structure','print');
                                        ?></span>
                                        </label>
                                        <div style="display:inline-block; min-width:50%">
                                            <?php
                                            if ($item->origin == "FIELD" && !$pri) {
                                            echo dbp_fn::html_select(['Standard field'=>[ 'TEXT'=>'Text', 'HTML'=>'Html', 'DATE'=>'Date', 'DATETIME'=>'Date Time', 'IMAGE'=>'Image','LINK'=>'External link', 'DETAIL_LINK' => 'Detail Link', 'SERIALIZE'=>'Serialiaze', 'JSON_LABEL'=>'Show Checkbox values (Json label)'],'Special Fields' =>['CUSTOM'=>'Custom','LOOKUP'=>'Lookup','USER' => 'User', 'POST' => 'Post', 'MEDIA_GALLERY' => 'Media Gallery']] , true, 'name="fields_custom_view['. esc_attr($item->name).']" class="js-type-fields" onchange="dbp_change_custom_type(this)" style="display:'. (($item->view =='CUSTOM') ? 'none' :'inline-block').'"', $item->view); 
                                            } else if ($pri) {
                                                ?><input type="hidden" name="fields_custom_view[<?php echo esc_attr($item->name); ?>]" class="js-type-fields" onchange="dbp_change_custom_type(this)"  value="TEXT"><span style="line-height: 1.7rem;">Text</span><?php
                                            } else {
                                                ?><input type="hidden" name="fields_custom_view[<?php echo esc_attr($item->name); ?>]" class="js-type-fields" onchange="dbp_change_custom_type(this)"  value="CUSTOM"><span style="line-height: 1.7rem;">Custom</span><?php
                                            }
                                            ?> 
                                            <textarea name="fields_custom_code[<?php echo esc_attr($item->name); ?>]" class="js-type-custom-code dbp-input" rows="2" style="display:<?php echo ($item->view =='CUSTOM') ? 'inline-block' :'none'; ?>; width:80%; min-width:250px"><?php echo esc_textarea($item->custom_code); ?></textarea>
                                            
                                            <?php if ($item->origin == "FIELD") : ?>
                                            <div class="dashicons dashicons-list-view dbp-input-button js-textarea-btn-cancel"  style="display:<?php echo ($item->view =='CUSTOM') ? 'inline-block' :'none'; ?>" onclick="dbp_custom_cancel(this)"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="dbp-form-row-column">
                                        <label><span class="dbp-form-label "><?php _e('Align','database_press'); ?></span>
                                        <?php echo dbp_fn::html_select(['top-left'=>'Top Left', 'top-center'=>'Top Center','top-right'=>'Top Right', 'center-left'=>'Center Left', 'center-center'=>'Center Center','center-right'=>'Center Right', 'bottom-left'=>'Bottom Left', 'bottom-center'=>'Bottom Center','bottom-right'=>'Bottom Right'], true, 'name="fields_align['. esc_attr($item->name).']" class="js-width-fields"', $item->align); ?>
                                        </label>
                                    </div>
                                   
                                    <div class="dbp-form-row-column js-dbp-params-column">
                                        <label<?php echo ($pri) ? ' style="display:none"' : ''; ?>>
                                            <span class="dbp-form-label js-dbp-params-date"><?php _e('Date format','database_press'); ?></span>
                                            <span class="dbp-form-label js-dbp-params-link"><?php _e('Link text','database_press'); ?></span>
                                            <span class="dbp-form-label js-dbp-params-user"><?php _e('Show user attributes [%user.]','database_press'); dbp_fn::echo_html_icon_help('dbp_list-list-structure','user'); ?></span>
                                            <span class="dbp-form-label js-dbp-params-post"><?php _e('Show post attributes [%post.]','database_press'); dbp_fn::echo_html_icon_help('dbp_list-list-structure','post'); ?></span>
                                            <span class="dbp-form-label js-dbp-params-text"><?php _e('Max text length','database_press'); ?></span>
                                            <input type="text" name="fields_custom_param[<?php echo esc_attr($item->name); ?>]" value="<?php echo esc_attr($item->custom_param); ?>" class="js-input-parasm-custom dbp-input">
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="js-lookup-params" id="id<?php echo dbp_fn::get_uniqid(); ?>">
                                    <h3 class="dbp-css-mb-1">Looup params</h3>
                                    <div class="dbp-form-row-column dbp-css-mb-1">
                                        <?php _e('Choose the list and column on which to compare the data','database_press'); ?>
                                    </div>
                                    <?php
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
                                    <div class="dbp-structure-grid" <?php echo ($pri) ? ' style="display:none"' : ''; ?>>
                                        <div class="dbp-form-row-column">
                                            <div class="dbp-form-row-column dbp-css-mb-2">
                                                <label><span class="dbp-form-label" style="vertical-align:top"><?php _e('Table:','database_press'); ?></span>
                                                </label>
                                                <div style="display:inline-block; min-width:50%">
                                                <?php echo dbp_fn::html_select($list_of_tables['tables'], true, 'name="fields_lookup_id[' . esc_attr($item->name) . ']" onchange="dbp_list_change_lookup_id(this)" class="js-select-fields-lookup"', $item->lookup_id); ?>
                                                </div>
                                                
                                                <input type="hidden"  name="fields_lookup_sel_val[<?php echo esc_attr($item->name); ?>]" value="<?php echo $item->lookup_sel_val; ?>" class="dbp-input js-lookup-select-value">
                                            </div>
                                        </div>
                                        <div class="dbp-form-row-column">
                                            <label><span class="dbp-form-label" style="vertical-align:top"><?php _e('Show','database_press'); ?></span>
                                            </label>
                                            <div style="display:inline-block; min-width:50%">
                                                <?php echo dbp_fn::html_select($lookup_col_list, false, 'name="fields_lookup_sel_txt[' . esc_attr($item->name) . '][]" class="dbp-input js-lookup-select-text" multiple', $item->lookup_sel_txt); ?>
                                            </div>
                                        </div>
                               
                                    </div>
                                </div>
                                    
                                <h3<?php echo ($pri) ? ' style="display:none"' : ''; ?>>column formatting</h3>
                                   
                                <div class="dbp-structure-grid" <?php echo ($pri) ? ' style="display:none"' : ''; ?>>
                                    <div class="dbp-form-row-column js-form-row-custom-field">
                                        <label><span class="dbp-form-label" style="vertical-align:top"><?php _e('change values','database_press'); dbp_fn::echo_html_icon_help('dbp_list-list-structure','format');?></span>
                                        </label>
                                        <div style="display:inline-block; min-width:50%">
                                            <textarea name="fields_format_values[<?php echo esc_attr($item->name); ?>]" class="dbp-input" rows="4" style=" width:80%; min-width:250px"><?php echo esc_textarea($item->format_values); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="dbp-form-row-column js-form-row-custom-field">
                                        <label><span class="dbp-form-label" style="vertical-align:top"><?php _e('change styles','database_press'); dbp_fn::echo_html_icon_help('dbp_list-list-structure','styles') ?></span>
                                        </label>
                                        <div style="display:inline-block; min-width:50%">
                                            <textarea name="fields_format_styles[<?php echo esc_attr($item->name); ?>]" class="dbp-input" rows="4" style="width:80%; min-width:250px"><?php echo esc_textarea($item->format_styles); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                       
                    <?php endforeach ;?>
                    
                </div>

                <div style="margin:1rem; border-top:1px solid #dcdcde; padding-top:.5rem">
                <div onclick="dbp_list_structure_add_row(this)" class="button"><?php _e('Add Custom column', 'database_press'); ?></div>
                <div id="dbp-bnt-columns-query" class="button js-show-only-select-query" onclick="dbp_columns_sql_query_edit()"><?php _e('Choose column to show*', 'database_press'); ?></div>

                <div style="display:none">
                    <?php dbp_html_sql::render_sql_from($table_model, false); ?>
                  
                </div>
                <p>* <?php _e('After modifying the query columns, the form will be saved automatically to allow you to view the modifications made', 'database_press'); ?></p>
                </div>
                    
                <hr>
                <h3 class="dbp-h3">General Setting</h3>
                <div class="dbp-form-row">
                    <label><span class="dbp-form-label "><?php echo _e('Max text length', 'database_press'); ?></span>
                    <input type="number" name="list_general_setting[text_length]" class="dbp-input" value="<?php echo esc_attr($post->post_content['list_general_setting']['text_length']); ?>"></label>
                </div>
                <div class="dbp-submit" onclick="dbp_submit_list_structure();"><?php _e('Save','database_press'); ?></div>
            </div>
        </form>
    <?php endif; ?>
</div>
