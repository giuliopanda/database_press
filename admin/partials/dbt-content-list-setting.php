<?php

/**
 * Descrizione della pagina
 * @var $vars descrizione delle variabili ereditate
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
$append = '<span class="dbt-submit" onclick="dbt_submit_list_setting()">' . __('Save', 'database_tables') . '</span>';

?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__) . '/dbt-partial-tabs.php'); ?>
</div>

<div class="dbt-content-table js-id-dbt-content">

    <?php $dtf::echo_html_title_box('list', $list_title, '', $msg,  $msg_error, $append); ?>
    <div class="dbt-content-margin">
        <form id="list_setting_form" method="POST" action="<?php echo admin_url("admin.php?page=dbt_list&section=list-setting&dbt_id=" . $id); ?>" id="dbt_create_table">
            <input type="hidden" name="action" value="list-setting-save" />
           
            <h3 class="dbt-h3 dbt-margin-top">
                <?php _e('List of records', 'database_tables'); ?>
                <?php  Dbt_fn::echo_html_icon_help('dbt_list-list-setting','list_of_records');  ?>
            </h3>
            <p>
                <?php _e('You can publish the list in the frontend using this shortcode: ', 'database_tables'); ?>
                <b>[dbt_list id=<?php echo $post->ID; ?>]</b> <?php echo ($post->shortcode_param!= "") ? __('Attributes', 'database_tables').":<b>".$post->shortcode_param.'</b>' : ''; ?>
            </p>

            <div id="block_if">
                <div class="dbt-form-row dbt-show-if">
                    <label style="vertical-align: top;">
                        <span class="dbt-form-label"><input type="checkbox" name="frontend_view[checkif]" value="1" id="checkbox_show_if" onchange="dbt_checkif()" <?php echo (@$few['checkif'] == 1) ? 'checked="checked"' : ''; ?>)> <?php _e('Show IF', 'database_tables'); ?>  <?php  Dbt_fn::echo_html_icon_help('dbt_list-list-setting','show_if');  ?></span>
                    </label>
                        <div id="dbt_textarea_if"> 
                            <textarea class="dbt-form-textarea" rows="2" name="frontend_view[if_textarea]"><?php echo esc_textarea(stripslashes(@$few['if_textarea'])); ?></textarea>
                            <div ><span class="dbt-link-click" onclick="show_pinacode_vars()">show shortcode variables</span></div>
                        </div>
                
                    <?php if (@$few['checkif'] == 1) : ?>
                        <?php if ($errors_if_textarea == "") : ?>
                        <?php else : ?>
                        <span class="dashicons dashicons-warning dbt-dashicons-red" title="<?php echo esc_attr($errors_if_textarea); ?>"></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dbt-form-row dbt-list-setting-color-margin-left-first">
                <label>
                    <span class="dbt-form-label"><?php _e('List type', 'database_tables'); ?></span>
                    <?php echo Dbt_fn::html_select(['TABLE_BASE' => 'Table', 'EDITOR' => 'Editor'], true, 'name="frontend_view[type]" onchange="dbt_list_setting(this)" id="dbt_choose_type_frontend_view"', @$few['type']); ?>
                </label>
                <hr>
            </div>
            <div id="frontend_view_table">
                <div class="dbt-grid-3-columns dbt-list-setting-color-margin-left">
                    <div class="dbt-column">
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Style color', 'database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select(['blue' => 'BLue', 'green' => 'Green', 'red' => 'Red', 'pink' => 'Pink', 'yellow' => 'Yellow',  'gray' => 'Gray'], true, 'name="frontend_view[table_style_color]" onchange="dbt_update_css_table()" id="dbt_css_color"', @$few['table_style_color']); ?>
                            </label>
                        </div>
                        
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Pagination position', 'database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select(['' => 'None', 'down' => 'Down', 'up' => 'Up', 'both' => 'Both'], true, 'name="frontend_view[table_pagination_position]" onchange="dbt_update_position_pagination()" id="dbt_position_pagination"', @$few['table_pagination_position']); ?>
                            </label>
                        </div>
                        <div class="dbt-form-row" id="dbt_pagination_style_row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Pagination style', 'database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select(['select' => 'Select', 'numeric' => 'Numeric'], true, 'name="frontend_view[table_pagination_style]" onchange="dbt_update_position_pagination()" id="dbt_pagination_style"', @$few['table_pagination_style']); ?>
                            </label>
                        </div>
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Column sort', 'database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select(['' => 'No', 'icon1' => 'Yes'], true, 'name="frontend_view[table_sort]" onchange="dbt_update_column_sort()" id="dbt_table_sort"', @$few['table_sort']); ?>
                            </label>
                        </div>
                    </div>
                    <div class="dbt-column">
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Search', 'database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select(['' => 'no', 'simple' => 'Yes'], true, 'name="frontend_view[table_search]" onchange="dbt_update_search()" id="dbt_table_search"', @$few['table_search']); ?>
                            </label>
                        </div>
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Table Dimension', 'database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select(['xsmall' => 'X Small', 'small' => 'Small', '' => 'Normal' , 'big' => 'Big'], true, 'name="frontend_view[table_size]" onchange="dbt_update_css_table()" id="dbt_table_size"', @$few['table_size']); ?>
                            </label>
                        </div>
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label"><?php _e('Table update', 'database_tables');  Dbt_fn::echo_html_icon_help('dbt_list-list-setting','update'); ?></span>
                                <?php echo Dbt_fn::html_select(['get' => 'Get', 'post' => 'Post', 'ajax' => 'Ajax'], true, 'name="frontend_view[table_update]"', @$few['table_update']); ?>
                            </label>
                        </div>
                    </div>
                    <div class="dbt-column">
                        <h4><?php _e('Table example', 'database_tables'); ?></h4>
                        <div class="dbt-content-table dbt-admin-content-table" style="margin:0">
                            
                            <div id="dbt_content_table">
                                <div class="dbt-search-row" id="dbt_preview_table_search">
                                    <input type="text" class="dbt-search-input">
                                    <div class="dbt-search-button">Search</div>
                                </div>
                                <div class="dbt-pagination" id="dbt_pag_up">
                                    <div class="dbt-pagination-total">total: xx</div>
                                    <div class="dbt-pagination-btns">
                                        <a href="#">&laquo;</a>
                                    </div>
                                    <select style="font-size:1em; padding:.2em 2.4em .2em .8em"><option value="1">1</option><option value="2">2</option></select>
                                    <div class="dbt-pagination-btns">
                                        <a href="#">&raquo;</a>
                                    </div>
                                </div>
                                <div class="dbt-pagination" id="dbt_pag2_up">
                                    <div class="dbt-pagination-total">total: xx</div>
                                    <div class="dbt-pagination-btns">
                                        <a href="#">&laquo;</a>
                                        <a href="#">1</a>
                                        <a href="#" class="active">2</a>

                                    </div>
                                </div>
                                <table class="dbt-table-view-list" id="dbt_test_table">
                                    <thead>
                                        <tr>
                                            <th style="min-width:15em"><span class="js-no-order" >Column A</span><span class="js-order-link dbt-title-order-link" href="#">Column A &udarr;</span></th>
                                            <th style="min-width:15em"><span class="js-no-order">Column B</span><span class="js-order-link dbt-title-order-link" href="#">Column B &udarr;</span></th>
                                          
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Lorem Ipsum</td>
                                            <td>Lorem Ipsum</td>
                                        </tr>
                                        <tr>
                                            <td>Lorem Ipsum</td>
                                            <td>Lorem Ipsum</td>
                                        </tr>
                                        <tr>
                                            <td>Lorem Ipsum</td>
                                            <td>Lorem Ipsum</td>
                                        </tr>
                                        <tr>
                                            <td>Lorem Ipsum</td>
                                            <td>Lorem Ipsum</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="dbt-pagination" id="dbt_pag_down">
                                    <div class="dbt-pagination-total">total: xx</div>
                                    <div class="dbt-pagination-btns">
                                        <a href="#">&laquo;</a>
                                    </div>
                                    <select style="font-size:1em; padding:.2em 2.4em .2em .8em"><option value="1">1</option><option value="2">2</option></select>
                                    <div class="dbt-pagination-btns">
                                        <a href="#">&raquo;</a>
                                    </div>
                                </div>
                                <div class="dbt-pagination" id="dbt_pag2_down">
                                    <div class="dbt-pagination-total">total: xx</div>
                                    <div class="dbt-pagination-btns">
                                        <a href="#">&laquo;</a>
                                        <a href="#">1</a>
                                        <a href="#" class="active">2</a>
                                        <a href="#">3</a>
                                        <a href="#">&raquo;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="frontend_view_editor">
                <div class="dbt-list-setting-color-margin-left">
                    <div class="dbt-grid-3-columns">
                        <div class="dbt-form-row">
                            <label>
                                <span class="dbt-form-label">
                                    <?php _e('Table update', 'database_tables'); ?>
                                    <?php  Dbt_fn::echo_html_icon_help('dbt_list-list-setting','table_update');  ?>
                                </span>
                                <?php echo Dbt_fn::html_select(['none' => 'None', 'get' => 'Get', 'post' => 'Post', 'ajax' => 'Ajax', 'link'=>'Link'], true, 'name="editor_table_update" onchange="select_editor_table_update()" id="select_editor_table_upldate"', @$few['table_update']); ?>
                            </label>
                        </div>
                        <div class="dbt-form-row" id="dbt_pagination_style_row_2">
                            <label>
                                <span class="dbt-form-label"><?php _e('Pagination style', 'database_tables'); ?></span>
                                <?php echo Dbt_fn::html_select(['select' => 'Select', 'numeric' => 'Numeric'], true, 'name="editor_table_pagination_style"', @$few['table_pagination_style']); ?>
                            </label>
                        </div>
                    </div>
                    <div class="dbt-form-row">
                        <label>
                            <span class="dbt-form-label-long"><b><?php _e('Header (first special row)', 'database_tables'); ?></b></span>
                            <p class="dtf-alert-gray" style="margin-top:-.5rem">
                            Special variables: [%html.pagination], [%html.search], [%total_row], [%key], [%data]
                            </p>
                            <textarea id="editor_content_header" name="frontend_view[content_header]"><?php echo esc_textarea(@$few['content_header']); ?></textarea>
                            <span class="dbt-link-click" onclick="show_pinacode_vars()">show shortcode variables</span>
                        </label>
                    </div>
                  
                    <div class="dbt-form-row">
                        <label>
                            <span class="dbt-form-label-long"><b><?php _e('Loop the data', 'database_tables'); ?></b>
                                <?php  Dbt_fn::echo_html_icon_help('dbt_list-list-setting','loop_data');  ?>
                            </span>
                            <p class="dtf-alert-gray" style="margin-top:-.5rem">
                            If 'Detailed view' is active, you can create a link that opens the popup to show the details box. Example <?php echo htmlentities('<a href="[%data._popup_link]" class="js-dbt-popup">detail</a>'); ?> <br> Other special variables: [%key], [%data]</p>

                            <textarea id="editor_content" name="frontend_view[content]"><?php echo esc_textarea(@$few['content']); ?></textarea>
                            <span class="dbt-link-click" onclick="show_pinacode_vars()">show shortcode variables</span>
                        </label>
                    </div>

                    <div class="dbt-form-row">
                        <label>
                            <span class="dbt-form-label-long"><?php _e('Footer', 'database_tables'); ?></span>
                            <textarea id="editor_content_footer" name="frontend_view[content_footer]"><?php echo esc_textarea(@$few['content_footer']); ?></textarea>
                            <span class="dbt-link-click" onclick="show_pinacode_vars()">show shortcode variables</span>
                        </label>
                    </div>

                </div>
            </div>

            <div id="block_else" class="dbt_block_else">
                <div class="dbt-form-row dbt-show-if">
                    <label>
                        <span class="dbt-form-label"><?php _e('ELSE :', 'database_tables'); ?></span>
                    </label>
                </div>
                <div class="dbt-form-row dbt-list-setting-color-margin-left">
                    <textarea id="editor_else" name="frontend_view[content_else]" style="height:300px"><?php echo esc_textarea(@$few['content_else']); ?></textarea>
                </div>
            </div>
            <div id="no_result">
                <h3 class="dbt-h3 dbt-margin-top"><?php _e('No result', 'database_tables'); ?></h3>
                <p class="dtf-alert-gray" style="margin-top:-1rem">
                <?php _e('What appears if there are no results.','database_tables');  ?>
                </p>
                <div>
                    <div class="dbt-form-row">
                        <textarea class="dbt-form-textarea" id="editor_no_result" rows="2" name="frontend_view[no_result_custom_text]"><?php echo esc_textarea(stripslashes(@$few['no_result_custom_text'])); ?></textarea>
                    </div>
                </div>
            </div>

            <h3 class="dbt-h3 dbt-margin-top"><?php _e('Detailed view', 'database_tables'); ?></h3>
            <p class="dtf-alert-gray" style="margin-top:-1rem">
                <?php _e('You can choose to view the content detail on a page.','database_tables'); 
                Dbt_fn::echo_html_icon_help('dbt_list-list-setting','detail');
                ?>
            </p>
            <div>
                <div class="dbt-form-row">
                    <label><span class="dbt-form-label"><?php _e('Detail ', 'database_tables'); ?></span><?php  Dbt_fn::html_select(['no'=>'Deactivated',  'yes'=>'Active'], true, 'name="frontend_view[detail_type]" id="select_detail_toggle" onchange="detail_toggle()"', @$few['detail_type']); ?>
                    </label>
                </div>
                <div class="dbt-form-row" id="detail_text">
                    <span class="dbt-form-label"><?php _e('Detail Template', 'database_tables'); ?></span>
                    <textarea class="dbt-form-textarea" id="editor_detail_template" rows="2" name="frontend_view[detail_template]"><?php echo esc_textarea(stripslashes(@$few['detail_template'])); ?></textarea>
                    <span class="dbt-link-click" onclick="show_pinacode_vars()">show shortcode variables</span>
                </div>
            </div>

            <br class="clear">
            <div class="dbt-submit" onclick="dbt_submit_list_setting()"><?php _e('Save', 'database_tables'); ?></div>
        </form>
    </div>
</div>