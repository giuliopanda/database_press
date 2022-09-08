/**
 * Il bottone per salvare una query
 */
function dbp_create_list_show_form( from_sql ) {
    dbp_open_sidebar_popup('save_list');
    dbp_close_sidebar_loading();
    let $form = jQuery('<form class="dbp-form-save-query dbp-form-edit-row" id="dbp_form_save_new_query" action="'+dbp_admin_post+'"></form>');

    $form.append('<input type="hidden" name="page" value="database_press"><input type="hidden" name="section" value="list-add"><input type="hidden" name="action" value="dbp_create_list">');
    
    if (from_sql) {
        $form.append('<p class="dbp-alert-gray">Save the query. Then you will have the shortcode to view the table on the website.</p>');
    } else {
        $form.append('<p class="dbp-alert-gray">Create a new list. This way you can extract data from a table and show it on your website.</p>'); 
    }

    $field_row = jQuery('<div class="dbp-form-row"></div>');
    $field_row.append('<label><span class="dbp-form-label">Name</span></label><input type="text" class="form-input-edit" name="new_title" id="dbp_name_create_list">');
    $form.append($field_row);
    new_title = "query_"+ dbp_uniqid();
    if (document.getElementById('sql_query_edit')) {
        code = document.getElementById('sql_query_edit').dbp_editor_sql;
        
        let get_first_row = jQuery('#sql_query_edit').val().toLowerCase();
        if (typeof(code) != "undefined") {
            get_first_row = code.codemirror.getValue().toLowerCase();
        }
    
        let temp_name = get_first_row.split("from");
   
       
        if (temp_name.length > 1) {
            let temp_name2 = temp_name[1].trim().split(" ");
            if (temp_name2.length > 0 && temp_name2[0].length > 2) {
                new_title = temp_name2[0].trim().replace(/ .*/,'').replaceAll('`','').substring(0,20);
            } else {
                new_title = temp_name[1].trim().replace(/ .*/,'').replaceAll('`','').substring(0,20);
            }
        }
    }
    $field_row.find('#dbp_name_create_list').val(new_title);

    $field_row  = jQuery('<div class="dbp-form-row"><label><span class="dbp-form-label">Description</span><textarea  class="form-textarea-edit" name="new_description"></textarea></label></div>');
    $form.append($field_row);

    if (from_sql) {
        $form.append('<div class="dbp-form-row"><label><span class="dbp-form-label">Choose with query use</span>');
        $form.append('<div class="dbp-dropdown-line-flex"><span style="margin-right:.5rem"><input name="choose_tables_query" type="radio" checked="checked" value="sql_query_executed"></span><div class="dbp-xmp">If you used filters to limit your data, save the query with the filtered results</div></div>');
        $form.append('<div class="dbp-dropdown-line-flex"><span style="margin-right:.5rem"><input name="choose_tables_query" type="radio" value="sql_query_edit"></span><div class="dbp-xmp">Show all data without filters</div></div>');
        $form.append('<textarea style="display:none" id="dbp_sql_new_list" name="new_sql"></textarea>');
    } else {
        $form.append('<div class="dbp-form-row"><label><span class="dbp-form-label">Connect MySql Table</span>');
        
        $content_radio = jQuery('<div class="dbp-dropdown-line-flex"></div>');
        $content_radio.append('<div class="dbp-dropdown-line-flex" style="margin-right:1rem"><span style="margin-right:.5rem"><input name="table_choose" type="radio" class="js-radio-table-choose" checked="checked" value="create_new_table"></span><div class="dbp-xmp">Create a new Table</div></div>');
        $content_radio.append('<div class="dbp-dropdown-line-flex"><span style="margin-right:.5rem"><input name="table_choose" type="radio" class="js-radio-table-choose"  value="choose_table_from_db"></span><div class="dbp-xmp">Choose an existing table</div></div>');
        $form.append($content_radio);
      
        $select_tables = jQuery('<select name="mysql_table_name"></select>');
        dbp_tables.sort();
        for (x in dbp_tables ) {
            $select_tables.append('<option value="'+dbp_tables[x]+'">'+dbp_tables[x]+'</option>');
        }
        $form_row = jQuery('<div class="dbp-form-row" id="dbp_sql_select_tables" style="display:none"></div>');
        $form_row.append('<label><span class="dbp-form-label">Choose existing table</span></label>');
        $form_row.append($select_tables);
        $form.append($form_row);
    }

    jQuery('#dbp_dbp_content').append($form);
    $form.find('#dbp_name_create_list').select();
    $field_row.append('<input type="hidden" class="form-input-edit" name="new_sort_field" value="'+jQuery('#dbp_table_sort_field').val()+'">');
    $field_row.append('<input type="hidden" class="form-input-edit" name="new_sort_order" value="'+jQuery('#dbp_table_sort_order').val()+'">');



    jQuery('#dbp_dbp_title > .dbp-edit-btns').remove();
    jQuery('#dbp_dbp_title').append('<div class="dbp-edit-btns"><h3>New List</h3><div id="dbp-bnt-edit-query" class="dbp-submit" onclick="dbp_save_sql_query()">Save</div></div>');

    if (!from_sql) {
        jQuery(".js-radio-table-choose:radio").change(function() {
            selected_value = jQuery(".js-radio-table-choose:checked").val();
            if (selected_value == 'create_new_table') {
                jQuery('#dbp_sql_select_tables').css('display','none');
            } else {
                jQuery('#dbp_sql_select_tables').css('display','block');
            }
        });
    }
}
/**
 * Invio la form per la creazione di una nuova lista
 */
function dbp_save_sql_query() {
   let sql_id =  jQuery("input:radio[name=choose_tables_query]:checked").val();
   let sql = jQuery('#'+sql_id).val();
   jQuery('#dbp_sql_new_list').val(sql);
   if (jQuery('#dbp_name_create_list').val() == "") {
       alert("Name is required");
       jQuery('#dbp_name_create_list').focus();
       return false;
   }
   jQuery('#dbp_form_save_new_query').submit();
}