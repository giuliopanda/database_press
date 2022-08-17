<?php
/**
* La grafica del tab import
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
 ?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
</div>
<div class="dbt-content-table js-id-dbt-content" id="dbt_content_table" >
    <?php if (@$this->last_error != "" && Dbt_fn::get_request('action', '', 'string') != 'create-table-csv-data') : ?>
        <div class="dtf-alert-sql-error"><?php echo $this->last_error; ?></div>
    <?php endif; ?>

    <?php if (@$this->msg != "" && Dbt_fn::get_request('action', '', 'string') != 'create-table-csv-data') : ?>
        <div class="dtf-alert-info"><?php echo $this->msg; ?></div>
    <?php endif; ?>

    <?php if (in_array($action, ['import-csv-file', 'execute-csv-data', 'create-table-csv-data', 'insert-csv-data']) ) : 
        /**
         * @var $csv_filename;
		 * @var $csv_delimiter;
		 * @var $csv_items;
         */
       
        if (is_countable($csv_items)) {
            $fields_name = array_values(reset($csv_items));
            $select_fields_name = array_merge([""], $fields_name);
            Dbt_fn::echo_pinacode_variables_script(['item'=>$fields_name]);
        }
        ?>
        <div id="first_block">
            <div class="dtf-alert-info">
                <?php _e('The csv file has been loaded, check if the data is correct.','database_tables'); ?>
                <a href="<?php echo add_query_arg(['section'=>'table-import'],  admin_url("admin.php?page=database_tables")); ?>"><?php _e('Upload a new file','database_tables'); ?></a>
            </div>
            <div class="dbt-content-margin">
                <div class="dbt-import-params-csv">
                    <form method="POST" action="<?php echo admin_url("admin.php?page=database_tables&section=table-import"); ?>" enctype="multipart/form-data" >
                        <input type="hidden" name="page" value="database_tables" />
                        <input type="hidden" name="section" value="table-import" />
                        <input type="hidden" name="table" value="<?php echo @$import_table; ?>" />
                        <input type="hidden" name="csv_name_of_file" value="<?php echo esc_attr($name_of_file); ?>" />
                        <input type="hidden" name="csv_temporaly_filename" value="<?php echo esc_attr($csv_filename); ?>" />
                        <input type="hidden" name="action" value="execute-csv-data" />
                        Delimiter <input type="text" name="csv_delimiter" value="<?php echo Dbt_fn::convert_char_to_special($csv_delimiter); ?>" />
                        <?php if ((isset($allow_use_first_row) && $allow_use_first_row == true) || !isset($allow_use_first_row)) : ?>
                            <label><input type="checkbox" name="csv_first_row_as_headers" value="1" <?php echo ($csv_first_row_as_headers) ? ' checked="checked"' : '';?>> <?php _e('Use first row as Headers', 'database_tables'); ?></label>
                        <?php else : ?>
                            <label><input type="checkbox"  disabled> <?php _e('Use first row as Headers', 'database_tables'); ?></label>
                        <?php endif; ?>   
                        <input type="hidden" name="allow_use_first_row" value="<?php echo (@$allow_use_first_row) ? 1 : 0; ?>">
                        <input type="submit" value="<?php _e('Update Preview', 'database_tables'); ?>" />
                        
                        <?php Dbt_fn::echo_html_icon_help('database_tables-table-import','delimiter'); ?>
                    </form>
                </div>
                <h4 class="dbt-subtitle"><?php _e('Preview', 'database-table'); ?></h4>
                <?php 
                $html_table   = new dbt_html_simple_table();
                $html_table->add_table_class('dbt-table-preview-csv');
                ?>
            
                <div class="dbt-import-table-csv-preview">
                    <?php echo $html_table->render($csv_items, false, false); ?>
                </div>
            </div>

            <div class="dbt-choose-import-csv-action">
                <select id="dbt_import_select_action" onchange="dbt_toggle_action_import(this)">
                    <option value=""><?php _e('Choose action', 'database_tables'); ?></option>
                    <option value="create_table" <?php echo (@$select_action == 'create_database') ? 'selected="selected"' : ''; ?>><?php _e('Create Table', 'database_tables'); ?></option>
                    <option value="insert_records" <?php echo ($action == 'insert-csv-data' || @$select_action == 'insert_records') ? 'selected="selected"' : ''; ?>><?php _e('Insert/Update Records', 'database_tables'); ?></option>
                </select>
                <?php Dbt_fn::echo_html_icon_help('database_tables-table-import','choose_action'); ?>
            </div>
            <?php if (Dbt_fn::get_request('action', '', 'string') == 'create-table-csv-data') : ?>
                <?php if ($this->last_error != "" ) : ?>
                    <div class="dtf-alert-sql-error"><?php echo $this->last_error; ?></div>
                <?php endif; ?>
                <?php if (isset( $this->msg) &&  $this->msg != "") : ?>
                    <div class="dtf-alert-info">
                        <?php echo $this->msg; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($sql) && $sql != "") : ?>
                    <div class="dbt-result-query js-dbt-mysql-query-text">
                        <?php echo $sql; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="js-dbt-import-content-toggle" id="dbt_content_create_table" style="display:none">
                <form method="POST" action="<?php echo admin_url("admin.php?page=database_tables&section=table-import"); ?>" id="dbt_create_table">
                    <input type="hidden" name="page" value="database_tables" />
                    <input type="hidden" name="section" value="table-import" />
                    <input type="hidden" name="table" value="<?php echo @$import_table; ?>" />
                    <input type="hidden" name="csv_temporaly_filename" value="<?php echo esc_attr($csv_filename); ?>" />
                    <input type="hidden" name="action" value="create-table-csv-data" />
                    <input type="hidden" name="csv_delimiter" value="<?php echo Dbt_fn::convert_char_to_special($csv_delimiter); ?>" />
                    <input type="hidden" name="csv_first_row_as_headers" value="<?php echo ($csv_first_row_as_headers) ? '1' : '0';?>">

                    <div id="dbt_create_table" class="dbt-import-content-create-table" style="<?php echo (Dbt_fn::get_request('action', '', 'string') == 'create-table-csv-data') ? '' : '' ; ?>">
                        <div class="dbt-import-table-name">
                            <label><?php _e('Table name', 'database-table'); ?></label>
                            <label id="dbt_wp_prefix" class="dbt-wp-prefix"><?php echo Dbt_fn::get_prefix(); ?></label><input type="text" name="csv_name_of_file" value="<?php echo esc_attr($name_of_file); ?>">
                            <label><input type="checkbox" name="use_prefix" value="1" checked="checked" onchange="dbt_use_prefix(this, 'dbt_wp_prefix')"><?php _e('Use wp prefix', 'database-table'); ?> </label>
                        </div>
                        <table class="wp-list-table widefat striped dbt-table-view-list js-dragable-table">
                            <thead>
                                <th><?php _e('Order','database_tables'); ?></th>
                                <th><?php _e('Table name','database_tables'); ?></th>
                                <th><?php _e('Preset','database_tables'); ?></th>
                                <th><?php _e('Import from CSV column','database_tables'); ?></th>
                                <th><?php _e('Action','database_tables'); ?></th>
                            
                            </thead>
                            <?php  $row = 1; ?>
                            <tr class="js-clore-master">
                                <td class="js-dragable-handle"><span class="dashicons dashicons-sort"></span></td>
                                <td><input type="text" name="form_create[field_name][]" value=""></td>
                                <td>
                                    <?php echo Dbt_fn::html_select(['varchar'=>'String (1 line)', 'text'=>'Text (Multiline)','int_signed'=>'Number', 'decimal'=>'Decimal (123.12)', 'date'=>'Date', 'datetime'=>'Date Time'], true, 'class="js-field-preselect" name="form_create[field_type][]"', 'varchar'); ?>  
                                </td>
                                <td>
                                    <?php echo Dbt_fn::html_select($select_fields_name, false, 'name="form_create[csv_name][]" class="js-create-table-type"', 'VARCHAR'); ?>
                                </td>

                                <td>
                                    <div class="button" onClick="dbt_import_csv_create_table_delete_row(this);"><?php _e('Delete Row' , 'database_tables'); ?></div>
                                </td>
                            </tr>
                            <?php
                            if (isset($csv_structure) && is_array($csv_structure)) {
                                foreach ($csv_structure as $cs) {
                                    ?>
                                    <tr class="js-dragable-tr">
                                        <td class="js-dragable-handle"><span class="dashicons dashicons-sort"></span></td>
                                        <td><input type="text" name="form_create[field_name][]" value="<?php echo Dbt_fn::convert_to_mysql_column_name($cs->field_name); ?>"></td>
                                        <td>
                                            <?php 
                                            if ($cs->preset == "pri") {
                                                ?>
                                                <input type="hidden" name="form_create[field_type][]" value="pri">
                                                PRIMARY KEY
                                                <?php
                                            } else {
                                                echo Dbt_fn::html_select(['varchar'=>'String (1 line)', 'text'=>'Text (Multiline)','int'=>'Number', 'decimal'=>'decimal (123.12)', 'date'=>'Date', 'datetime'=>'Date Time'], true, 'class="js-field-preselect" name="form_create[field_type][]"',  @$cs->preset);
                                                }
                                            ?>  
                                        </td>
                                        <td>
                                            <?php echo Dbt_fn::html_select($select_fields_name, false, 'name="form_create[csv_name][]" class="js-create-table-type"', @$cs->name); ?>
                                        </td>
                                        <td>
                                            <?php if ($cs->preset != "pri") : ?>
                                            <div class="button" onClick="dbt_import_csv_create_table_delete_row(this);"><?php _e('Delete Row' , 'database_tables'); ?></div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php 
                                    $row++;
                                    if ($row > $max_row_allowed && $max_row_allowed > 0)   break;
                                }
                            }
                            ?>
                            <tr>
                                <td colspan="5">
                                    <div onclick="dbt_create_table_add_row(this, '<?php echo @$max_row_allowed; ?>')" class="button"><?php _e('Add row', 'database_tables'); ?></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="dbt-box-create-table-box">
                        <div id="dbt_content_button_create_form" >
                            <input type="submit" class="dbt-submit" value="<?php _e('Create Table', 'database_tables'); ?>" /> 
                            <?php Dbt_fn::echo_html_icon_help('database_tables-table-import','create_table'); ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php /** IMPORTO I DATI */ ?>
        <div class="js-dbt-import-content-toggle" id="dbt_content_insert_records" style="display:none">
            <form id="dbt_import_csv_data_config" method="POST" action="<?php echo admin_url("admin.php?page=database_tables&section=table-import"); ?>" >
                <input type="hidden" name="page" value="database_tables" />
                <input type="hidden" name="section" value="table-import" />
                <input type="hidden" id="csv_import_original_table" name="table" value="<?php echo @$import_table; ?>" />
                <input type="hidden" id="csv_temporaly_filename" name="csv_temporaly_filename" value="<?php echo esc_attr($csv_filename); ?>" />
                <input type="hidden" id="csv_delimiter" name="csv_delimiter" value="<?php echo Dbt_fn::convert_char_to_special($csv_delimiter); ?>" />
                <input type="hidden" id="csv_first_row_as_headers" name="csv_first_row_as_headers" value="<?php echo ($csv_first_row_as_headers) ? '1' : '0';?>">
                <?php /** Qui vengono disegnate le tabelle in js con la configurazione per importare i dati di un csv */ ?>
                <div class="dbt-msg-unique-table-id-explane"><?php _e('Select one or more tables in which to insert data.<br>On each field choose which column of the csv to insert.<br>If you want to insert data on multiple tables, for example post and postmeta: First select the post table and associate the fields of your csv. At the bottom of the table you will find a string. This refers to the id of the records that will be created or updated.<br>Add the postmeta table and on the post_id field select the code in square brackets.<br>','database_tables'); ?></div>

                <div id="content_all_insert_fields_block" class="dbt-import-content-all-block">
                   
                </div>
                <div class="dbt-import-content-clone-block">
                    <div class="js-insert-fields-content-clone dbt-insert-fields-content">
                        <div class="dbt-import-params-csv">
                            <?php Dbt_fn::html_select(array_merge([''=>__('Select table', 'database_tables')],  $this->table_list['tables']), true, 'class="js-select-tables-import jsonchange-select-tables-import-clone"', @$current_table); ?>
                            <?php if (@$select_action == "insert_records" && isset($csv_structure_table_created) && is_countable($csv_structure_table_created) ) : ?>
                                <script>
                                    var csv_structure_table_created = <?php echo json_encode($csv_structure_table_created); ?>;
                                    jQuery(document).ready(function () {
                                        jQuery('.js-select-tables-import').first().change();
                                    });
                                </script>
                            <?php endif ; ?> 
                        <div class="js-immport-choose-table-remove-btn button" style="display:none"><?php _e('Delete','database_tables'); ?></div>
                        </div>
                        <div class="js-content-table-fields"></div>
                        <div class="js-msg-yes-pri-key dbt-msg-yes-no-pri-key" style="display:none">* <?php _e('Records with the same primary key will be updated.','database_tables'); ?></div>
                        <div class="js-msg-no-pri-key dbt-msg-yes-no-pri-key"  style="display:none">* <?php _e('All records will be inserted as new.','database_tables'); ?></div>
                        <div class="dbt-msg-unique-table-id" >* <?php _e('If you want to insert data into multiple tables, you can reference the newly created or updated record ID using the following code:','database_tables'); ?> <span class="js-unique-code"></span></div>
                    </div>
                </div>
              
                
            </form>
            <div class="dbt-import-content-all-block">
                <div id="dbt_import_csv_btns">
                    <div class="button" onclick="dbt_csv_test_import()"><?php _e('Test the Import', 'database_tables'); ?></div>
                    &nbsp; <div class="dbt-submit" onclick="dbt_csv_exec_import(0,0,0,0)"><?php _e("I'm feeling lucky! Import the data", 'database_tables'); ?></div>
                    <?php Dbt_fn::echo_html_icon_help('database_tables-table-import','insert_record'); ?>
                </div>
                <div id="dbt_result_import_box" class="dbt-insert-fields-content" style="display:none">
                    <div id="dbt_import_csv_alert" ></div>
                    <div id="dbt_result_test_import_csv"></div>

                    <table id="dbt_import_csv_exec_import" class="wp-list-table widefat striped dbt-table-view-list " style="display:none">
                        <tbody>
                            <tr>
                                <td><?php _e('Total row','database_tables'); ?></td>
                                <td id="dbt_result_import_csv_total_row"></td>
                            </tr> 
                            <tr>
                                <td><?php _e('Erorrs','database_tables'); ?></td>
                                <td id="dbt_result_import_csv_errors"></td>
                            </tr>   
                            <tr>
                                <td><?php _e('Insert','database_tables'); ?></td>
                                <td id="dbt_result_import_csv_insert"></td>
                            </tr>   
                            <tr>
                                <td><?php _e('Update','database_tables'); ?></td>
                                <td id="dbt_result_import_csv_update"></td>
                            </tr>   
                        </tbody>
                    </table>
                    <a  class="btn-csv-download" id="btn_csv_download" href="<?php echo add_query_arg(['section'=>'table-import', 'action'=>'dbt_download_csv_report','filename'=>$csv_filename],  admin_url("admin-post.php")); ?>" style="display:none;">Download csv with report</a>
                </div>
            </div>
        </div>

    <?php else: // in_array($action, ['import-csv-file', 'execute-csv-data', 'create-table-csv-data', 'insert-csv-data']

        /*
        * DEFAULT form di caricamento file
        * TODO upload di file di grandi dimensioni https://deliciousbrains.com/using-javascript-file-api-to-avoid-file-upload-limits/
        * Forse è meglio poter caricare zip e basta!
         */
        if (ini_get('upload_max_filesize') > 0 && ini_get('post_max_size') > 0) {
            $memory_limit = min(ini_get('upload_max_filesize'), ini_get('post_max_size'));
        } else if (ini_get('upload_max_filesize') > 0) {
            $memory_limit = ini_get('upload_max_filesize');
        } else {
            $memory_limit = "2Mb";
        }
        if (intval($memory_limit) != "") {
            $memory_limit = intval($memory_limit)*1024*1024;
        } else {
            $memory_limit = 2*1024*1024;
        }
        ?>
        <div class="dbt-content-margin">
            <h2 class="dbt-h2"><?php _e('File to import', 'database_tables'); ?></h2>
            <p class="dbt-p"><?php 
            _e('Upload an <b>sql</b> file with the queries to run', 'database_tables'); 
            Dbt_fn::echo_html_icon_help('database_tables-table-import','sql'); 
            ?></p>
            <form method="POST" action="<?php echo admin_url("admin.php?page=database_tables&section=table-import"); ?>" enctype="multipart/form-data" >
                <input type="hidden" name="page" value="database_tables" />
                <input type="hidden" name="section" value="table-import" />
                <input type="hidden" name="action" value="import-sql-file" />
                <input type="hidden" name="table" value="<?php echo @$import_table; ?>" />
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo  esc_attr($memory_limit); ?>" /> 
                <input id="fileupload" name="sql_file" type="file" />
                <input type="submit" class="dbt-submit" value="<?php _e('Execute import', 'database_tables'); ?>" />
            </form>
            <hr>
            <p class="dbt-p"><?php 
            _e('Upload a <b>csv</b> file and go ahead to configure the import', 'database_tables'); 
            Dbt_fn::echo_html_icon_help('database_tables-table-import','csv');
            ?></p>
            <form method="POST" action="<?php echo admin_url("admin.php?page=database_tables&section=table-import"); ?>" enctype="multipart/form-data" >
                <input type="hidden" name="action" value="import-csv-file">
                <input type="hidden" name="page" value="database_tables" />
                <input type="hidden" name="section" value="table-import" />
                <input type="hidden" name="table" value="<?php echo @$import_table; ?>" />
                <input id="fileupload" name="sql_file" type="file" />
                <input type="submit" class="dbt-submit" value="<?php _e('Go ahead', 'database_tables'); ?>" />
            </form>
           
            <?php 
            $max = Dbt_fn::get_max_upload_file();
            if ($max > 0) {
                ?>  <hr> <br><?php 
                printf(__("max upload files <b>%s</b>", 'database_tables'), $max); 
             
            }
             ?>
        </div>
    <?php endif; ?>

</div>