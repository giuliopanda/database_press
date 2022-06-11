jQuery(document).ready(function () {
    dbt_list_sql_add_row ();
});
/**
 * Il bottone per creare nuove righe "FILTER" delle tabelle mysql
 */
 function dbt_list_sql_add_row () {
    $div = jQuery('#dbt_clone_master').clone(true);
    jQuery('#dbt_container_filter').append($div);
    $div.css('display','block');
    $div.removeAttr('id');
 }

 /**
  * Cancello la riga
  */
 function dbt_remove_sql_row(el) {
     jQuery(el).parents('.dbt-form-row').remove();
 }

 /**
  * FILTER: Setto il required sul filtro dal checkbox all'hidden value
  */
 function dbt_required_field(el) {
    jQuery(el).parent().find('.js-filter-required').val( ((jQuery(el).is(':checked'))? 1 : 0) );
 }

function dtf_submit_list_sql(el) {
    checkboxes =0;
    jQuery('.js-add-role-cap').each(function() {
        if (jQuery(this).is(':checked')) {
            checkboxes++;
        }
    })
    if (checkboxes == 0 && jQuery('#cb_show_admin_menu').is(':checked')) {
        alert("You must select at least one role among the permissions");
        return ;
    }
    if ( document.getElementById('sql_query_edit') != null) {
        code = document.getElementById('sql_query_edit').dtf_editor_sql;
        if (typeof code != 'undefined' && code != null) {
            jQuery('#sql_query_edit').value = code.codemirror.getValue();
        }
        jQuery('#sql_query_edit').parents('form').submit();
    } else {
        jQuery(el).parents('form').submit();
    }
   
}

/**
 * 
 * @param DOM el 
 */
function cb_change_toggle_options(el) {
    if (jQuery(el).is(':checked')) {
        jQuery('#admin_menu_options_box').css('display','block');
        jQuery('.js-add-role-cap').first().prop('checked',true);
    } else {
        jQuery('#admin_menu_options_box').css('display','none');
    }
}