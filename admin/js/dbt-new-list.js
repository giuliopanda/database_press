/**
 * Il bottone per salvare una query
 */
function dbt_create_list_show_form( from_sql ) {
    dbt_open_sidebar_popup('save_list');
    dbt_close_sidebar_loading();
    let $form = jQuery('<form class="dbt-form-save-query dbt-form-edit-row" id="dbt_form_save_new_query" action="'+dbt_admin_post+'"></form>');

    $form.append('<input type="hidden" name="page" value="database_tables"><input type="hidden" name="section" value="list-add"><input type="hidden" name="action" value="dbt_create_list">');
    
    if (from_sql) {
        $form.append('<p class="dtf-alert-gray">Save the query. Then you will have the shortcode to view the table on the website.</p>');
    } else {
        $form.append('<p class="dtf-alert-gray">Create a new list. This way you can extract data from a table and show it on your website.</p>'); 
    }

    $field_row = jQuery('<div class="dbt-form-row"></div>');
    $field_row.append('<label><span class="dbt-form-label">Name</span></label><input type="text" class="form-input-edit" name="new_title" id="dbt_name_create_list">');
    $form.append($field_row);
    new_title = "query_"+ dbt_uniqid();
    if (document.getElementById('sql_query_edit')) {
        code = document.getElementById('sql_query_edit').dtf_editor_sql;
        
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
    $field_row.find('#dbt_name_create_list').val(new_title);

    $field_row  = jQuery('<div class="dbt-form-row"><label><span class="dbt-form-label">Description</span><textarea  class="form-textarea-edit" name="new_description"></textarea></label></div>');
    $form.append($field_row);

    if (from_sql) {
        $form.append('<div class="dbt-form-row"><label><span class="dbt-form-label">Choose with query use</span>');
        $form.append('<div class="dbt-dropdown-line-flex"><span style="margin-right:.5rem"><input name="choose_tables_query" type="radio" checked="checked" value="sql_query_executed"></span><div class="dbt-xmp">If you used filters to limit your data, save the query with the filtered results</div></div>');
        $form.append('<div class="dbt-dropdown-line-flex"><span style="margin-right:.5rem"><input name="choose_tables_query" type="radio" value="sql_query_edit"></span><div class="dbt-xmp">Show all data without filters</div></div>');
        $form.append('<textarea style="display:none" id="dbt_sql_new_list" name="new_sql"></textarea>');
    } else {
        $form.append('<div class="dbt-form-row"><label><span class="dbt-form-label">Connect MySql Table</span>');
        
        $content_radio = jQuery('<div class="dbt-dropdown-line-flex"></div>');
        $content_radio.append('<div class="dbt-dropdown-line-flex" style="margin-right:1rem"><span style="margin-right:.5rem"><input name="table_choose" type="radio" class="js-radio-table-choose" checked="checked" value="create_new_table"></span><div class="dbt-xmp">Create a new Table</div></div>');
        $content_radio.append('<div class="dbt-dropdown-line-flex"><span style="margin-right:.5rem"><input name="table_choose" type="radio" class="js-radio-table-choose"  value="choose_table_from_db"></span><div class="dbt-xmp">Choose an existing table</div></div>');
        $form.append($content_radio);
      
        $select_tables = jQuery('<select name="mysql_table_name"></select>');
        dtf_tables.sort();
        for (x in dtf_tables ) {
            $select_tables.append('<option value="'+dtf_tables[x]+'">'+dtf_tables[x]+'</option>');
        }
        $form_row = jQuery('<div class="dbt-form-row" id="dbt_sql_select_tables" style="display:none"></div>');
        $form_row.append('<label><span class="dbt-form-label">Choose existing table</span></label>');
        $form_row.append($select_tables);
        $form.append($form_row);
    }

    jQuery('#dbt_dbp_content').append($form);

    $field_row.append('<input type="hidden" class="form-input-edit" name="new_sort_field" value="'+jQuery('#dtf_table_sort_field').val()+'">');
    $field_row.append('<input type="hidden" class="form-input-edit" name="new_sort_order" value="'+jQuery('#dtf_table_sort_order').val()+'">');



    jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
    jQuery('#dbt_dbp_title').append('<div class="dbt-edit-btns"><h3>New List</h3><div id="dbt-bnt-edit-query" class="dbt-submit" onclick="dbt_save_sql_query()">Save</div></div>');

    if (!from_sql) {
        jQuery(".js-radio-table-choose:radio").change(function() {
            selected_value = jQuery(".js-radio-table-choose:checked").val();
            if (selected_value == 'create_new_table') {
                jQuery('#dbt_sql_select_tables').css('display','none');
            } else {
                jQuery('#dbt_sql_select_tables').css('display','block');
            }
        });
    }
}
/**
 * Invio la form per la creazione di una nuova lista
 */
function dbt_save_sql_query() {
   let sql_id =  jQuery("input:radio[name=choose_tables_query]:checked").val();
   let sql = jQuery('#'+sql_id).val();
   jQuery('#dbt_sql_new_list').val(sql);
   if (jQuery('#dbt_name_create_list').val() == "") {
       alert("Name is required");
       jQuery('#dbt_name_create_list').focus();
       return false;
   }
   jQuery('#dbt_form_save_new_query').submit();
}