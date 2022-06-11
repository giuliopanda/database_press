/**
 * Caricato su table-browse (visualizzazione tabelle (e list-browse forse))
 */
jQuery(document).ready(function ($) {
    /**
     * PAGINAZIONE
     */
    $('.js-dtf-pagination-first-page').click(function() {
        if (!$(this).hasClass('js-dtf-pag-disabled')) {
            $('#dtf_table_filter_limit_start').val(0);
            dtf_submit_table_filter('limit_start');
        }
    });
    $('.js-dtf-pagination-prev-page').click(function () {
        let pag = $(this).data('currentpage');
        if (!$(this).hasClass('js-dtf-pag-disabled')) {
            $('#dtf_table_filter_limit_start').val(pag);
            dtf_submit_table_filter('limit_start');
        }
    });

    $('.js-dtf-pagination-last-page').click(function () {
        let pag = $(this).data('currentpage');
        if (!$(this).hasClass('js-dtf-pag-disabled')) {
            $('#dtf_table_filter_limit_start').val(pag);
            dtf_submit_table_filter('limit_start');
        }
    });
    $('.js-dtf-pagination-next-page').click(function () {
        let pag = $(this).data('currentpage');
        if (!$(this).hasClass('js-dtf-pag-disabled') && pag > 0) {
            $('#dtf_table_filter_limit_start').val(pag);
            dtf_submit_table_filter('limit_start');
        }
    });
    // FINE PAGINAZIONE
    /**
     * ORDINAMENTO
     */
    $('.js-dtf-table-sort').click(function () {
        let sort_key = $(this).data('dtf_sort_key');
        let sort_order = $(this).data('dtf_sort_order');
        $('#dtf_table_sort_field').val(sort_key);
        $('#dtf_table_sort_order').val(sort_order);
        dtf_submit_table_filter('order');
    });
    // FINE ORDINAMENTO
    /**
     * MOSTRA SUBMENU AL CLICK
     */
    $('.js-dtf-table-show-dropdown').click(function() {
        let rif = $(this).data('fieldkey');
        if ($('#dbt_background_dropdown').length == 1) {
            $('.js-dbt-dropdown-header').css('display', 'none');
            $('#dbt_background_dropdown').remove();
        } else if ($('#dbt_dropdown_' + rif).css('display') != "block") {
            ajax_load_distinct_values(rif);
            $('.js-dbt-dropdown-header').css('display', 'none');
            let $bg = $('<div id="dbt_background_dropdown" class="dbt-bg-dropdown"></div>');
            let $js_content = $(this).parents('.js-id-dbt-content');
            $js_content.prepend($bg);
            $bg.css({'height':$js_content[0].scrollHeight+"px", 'width':$js_content[0].scrollWidth+"px"});
            $bg.click(function() {
                $('.js-dbt-dropdown-header').css('display', 'none');
                $(this).remove();
            });
            $('#dbt_dropdown_' + rif).css('display','block');
            let container_height = $('#dbt_container .js-id-dbt-content').height() - 40;
            $('#dbt_dropdown_'+rif).css('max-height', container_height+'px');
            $('#dbt_dropdown_'+rif+" > ,dbt-dropdown-container-scroll").css('max-height', container_height+'px');

        } else {
            $('.js-dbt-dropdown-header').css('display', 'none');
        }
    });
    $('.js-dbt-dropdown-btn-cancel').click(function() {
        $('.js-dbt-dropdown-header').css('display', 'none');
        $('#dbt_background_dropdown').remove();
    });
    // FINE SUBMENU AL CLICK
    /**
     * Gestione della ricerca all'interno del dropdown 
     * Quando inserisco un valore nel campo di ricerca
     */
    $('.js-table-filter-input-value').change(function() {
        let rif = $(this).data('rif');
        let select_op = $('#dbt_dropdown_' + rif + ' .js-table-filter-select-op').val();
        let field_type = $('#filter_search_type_' + rif).val();
        let new_val = "";
        if (field_type == "DATE") {
            new_val = $(this).val().replace("T"," ");
        } else {
            new_val = $(this).val();
        }
        if (select_op == "BETWEEN" || select_op ==  "NOT BETWEEN") {
            between_val = $('#dbt_input_value2_box_'+rif+' input').val();
            if (field_type == "DATE") {
                new_val =  new_val + "#AND#" + between_val.replace("T"," ");
            } else {
                new_val =  new_val + "#AND#" + between_val;
            }
        } 
        $("#dbt_dropdown_search_value_" + rif).val(new_val);
    });
    $('.js-table-filter-input-value').blur(function() {
        $(this).change();
    });
    /**
     * Quando inserisco un valore nel secondo campo di ricerca per il Between
     */
    $('.js-table-filter-input-value2').change(function() {
        let rif = $(this).data('rif');
        let select_op = $('#dbt_dropdown_' + rif + ' .js-table-filter-select-op').val();
        let field_type = $('#filter_search_type_' + rif).val();
        let new_val = "";
        let between_val = "";
        if (field_type == "DATE") {
            new_val = $(this).val().replace("T"," ");
        } else {
            new_val = $(this).val();
        }
        if (select_op == "BETWEEN" || select_op ==  "NOT BETWEEN") {
            between_val = $('#dbt_input_value_box_'+rif+' input').val();
            if (field_type == "DATE") {
                new_val = between_val.replace("T"," ") + "#AND#" + new_val ;
            } else {
                new_val = between_val  + "#AND#" + new_val;
            }
        } 
        $("#dbt_dropdown_search_value_" + rif).val(new_val);
        
    });
    $('.js-table-filter-input-value2').blur(function() {
        $(this).change();
    });

    $('.js-table-filter-input-value, .js-table-filter-input-value2, .js-table-filter-input_filter_checkboxes').keyup(function(ev) {
        if (ev.keyCode === 13) {
            let rif = jQuery(this).data('rif');
            let op = jQuery('#filter_'+ rif + '_op').val();
            if (op == "IN" || op == "NOT IN") {
                let free_search = jQuery('#dbt_input_filter_checkboxes_'+rif).val();
                $('#dbt_input_value_box_'+rif+' input').first().val(free_search);
                let ck1 = $('#dbt_checkboxes_ul_'+rif+' [type=checkbox]:checked').length;
                let ck2 = $('#dbt_checkboxes_ul_'+rif+' [type=checkbox]').length;
                if (($('#dbt_dropdown_search_value_'+rif).val() == "" && free_search != "") || (ck1 == ck2 && free_search != "")) {
                    $('#filter_'+rif+'_op').val('LIKE');
                    $('#dbt_dropdown_search_value_'+rif).val(free_search);
                }
            }
            dtf_submit_table_filter('filter');
        }
    })

    $('.js-dbt-btn-search').click(function() {
        let rif = jQuery(this).data('rif');
        let op = jQuery('#filter_'+ rif + '_op').val();
        if (op == "IN" || op == "NOT IN") {
            let free_search = jQuery('#dbt_input_filter_checkboxes_'+rif).val();
            $('#dbt_input_value_box_'+rif+' input').first().val(free_search);
            let ck1 = $('#dbt_checkboxes_ul_'+rif+' [type=checkbox]:checked').length;
            let ck2 = $('#dbt_checkboxes_ul_'+rif+' [type=checkbox]').length;
            if (($('#dbt_dropdown_search_value_'+rif).val() == "" && free_search != "") || (ck1 == ck2 && free_search != "")) {
                $('#filter_'+rif+'_op').val('LIKE');
                $('#dbt_dropdown_search_value_'+rif).val(free_search);
            }
        }
        dtf_submit_table_filter('filter');
    });
    /**
     * se cambia uno dei due select con il tipo di ricerca:
     * Inserisco nel campo nascosto il tipo di ricerca da fare e poi eseguo dropdown_filter_select_op 
     */
    $('.js-table-filter-select-op-partial').change(function() {
        $('#filter_'+ $(this).data('rif') + '_op').val($(this).val());
        dropdown_filter_select_op($(this).val(), $(this).data('rif'));
    });
    // quando cambio il tipo di ricerca 
    function dropdown_filter_select_op(val, rif, clean = true) {
        jQuery('#filter_'+rif+'_op').val(val);
        if (val == "IN" || val == "NOT IN") {
            $('#radio_'+ rif+'_2').prop("checked", true);
            $('#dbt_choose_values_box_'+ rif).css('display','block');
            jQuery('#dbt_input_filter_checkboxes_'+rif).css('display','block').focus();
            jQuery('#js_tf_select_op_'+rif+'_1').css('display','none');
            jQuery('#js_tf_select_label_'+rif).css('display','block');
        } else {
            $('#radio_'+ rif+'_1').prop("checked", true);
            $('#dbt_choose_values_box_'+ rif).css('display','none');
            jQuery('#dbt_input_filter_checkboxes_'+rif).css('display','none');
            jQuery('#js_tf_select_op_'+rif+'_1').css('display','block');
            jQuery('#js_tf_select_label_'+rif).css('display','none');
        }

        //$('#dbt_dropdown_search_value_'+ rif).val();
        if (clean) {
            jQuery('#dbt_dropdown_search_value_'+rif).val('');
        }
        if (val != "NULL" && jQuery('#dbt_input_value_'+rif).val() == '##EMPTYVALUES##' ) {
            jQuery('#dbt_input_value_'+rif).val('');
        }
        
        $('#dbt_input_value_box_'+rif).css('display','none');
        $('#dbt_input_value2_box_'+rif).css('display','none');
        
        if (val == "IN" || val == "NOT IN") {
            // mostro i checkboxes
            $('#dbt_choose_values_box_'+rif).css('display','block');
          //  ajax_load_distinct_values(rif);
        } else if (val == "NULL" || val == "NOT NULL") { 
            jQuery('#dbt_dropdown_search_value_'+rif).val('##EMPTYVALUES##');
        }  else if (val == "BETWEEN" || val == "NOT BETWEEN") { 
            $('#dbt_input_value_box_'+rif).css('display','flex');
            $('#dbt_input_value_box_'+rif+' input').change();
            $('#dbt_input_value2_box_'+rif).css('display','flex');
            $('#dbt_input_value_box_'+rif+' input').change();
        }  else {
            //mostro l'input 
            $('#dbt_input_value_box_'+rif).css('display','flex');
            $('#dbt_input_value_box_'+rif+' input').change();
            
        }
    }

    // all'apertura della pagina vedo tutti i Choose Values e avvio l'ajax
    $('.js-table-filter-select-op-partial').each(function() {
        let val = $('#filter_'+ $(this).data('rif') +'_op').val();
        dropdown_filter_select_op(val, $(this).data('rif'), false);
    })
    

    /**
     * Gestisco i radio per scegliere quale gruppo di filtri usare
     */
     $('.js-filter-search-radio').change(function() {
       // choose_filter_search_radio();
        let rif = $(this).data('rif');
        let radio_val = jQuery(this).val();
        id = '#js_tf_select_op_'+rif+'_'+$(this).val();
        dropdown_filter_select_op($(id).val(), rif);
        if (radio_val == 2) {
            ajax_load_distinct_values(rif);
        }

     })

     /**
      * Rimuovi il filtro di una colonna 
      */
     $('.js-remove-filter').click(function() {
        let rif = $(this).data('rif');
        $('#dbt_dropdown_'+rif).remove();
        dtf_submit_table_filter('filter');
     })
     $('.js-click-dashicons-filter').click(function() {
        let rif = $(this).data('rif');
        $('#dbt_dropdown_'+rif).remove();
        dtf_submit_table_filter('filter');
     })

     /**
      * Filtro i checkbox all'interno del dropdown
      */
     $('.js-table-filter-input_filter_checkboxes').keyup(function() {
        let rif = jQuery(this).data('rif');
        ajax_load_distinct_values(rif);
     })

     /**
      * SEleziono tutti i ckeckbox o li deseleziono
      */
     jQuery('.js-dropdown-select-all-checkboxes').click(function() {
        let rif = jQuery(this).data('rif');
        jQuery('#dbt_checkboxes_value_' + rif +' > ul .js-dbt-cb').prop('checked', true);
        add_serialize_checkboxes( jQuery('#dbt_checkboxes_value_' + rif +' > ul .js-dbt-cb').get(0));
    });
    jQuery('.js-dropdown-deselect-all-checkboxes').click(function() {
        let rif = jQuery(this).data('rif');
        jQuery('#dbt_checkboxes_value_' + rif +' > ul .js-dbt-cb').prop('checked', false);
        add_serialize_checkboxes( jQuery('#dbt_checkboxes_value_' + rif +' > ul .js-dbt-cb').get(0));
    });
    // FINE GESTIONE RICERCA ALL'INTERNO DEL DROPDOWN (alcune funzioni stanno sotto)


});


/**
 * Popolo i checkbox per la ricerca nei dropdown 
 * la stringa che ricevo è [{c:la colonna, p:la primary key, n:il count}]
 * Per filtrare userò o il testo o la primary key preceduta da # per select oppure per filtrare per primary key direttamente ^ (se il count = 1)
 * se la primary key == -1 filtro per il testo altrimenti per la primary key
 */
function populate_drowdown_checkboxes($ul_box, response) {
    let def_val = jQuery('#dbt_dropdown_search_value_'+ response.rif).val();
    let checkbox_selected_count = 0;
    let def_values = [];
    if (def_val != "") {
        def_values = def_val.split(',');
    }
    // i checkbox checked non trovati poi li devo mostrare
    let left_checkbox = def_values;
   // console.log(response.result);
    for (let i in response.result) {
        // resposne.rif sul change lo prendo da ul.data.rif
        let checked = "";
        var value =  response.result[i].c;
       
        if (response.result[i].p != -1) {
            if (response.result[i].n == 1) {
                value = "^"+response.result[i].p;
            } else {
                value = "#"+response.result[i].p;
            }
        } 
        if (def_values.indexOf(value) > -1 ) {
            checked = ' checked="checked"';
            checkbox_selected_count++;
           left_checkbox.splice(left_checkbox.indexOf(value),1);
        }
        let print_count = (response.result[i].n == 1) ? '' : ' ('+response.result[i].n+')';
        
        if (response.result[i].c.length > 55) {
            label_response = response.result[i].c.substring(0,50)+" ... "+print_count;
        } else {
            label_response = response.result[i].c+print_count;
        }
        if (value == '_##Empty values##_') {
            label_response = "Empty "+print_count;
        } else {
            label_response = String(label_response).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        $add = jQuery('<li><label><input type="checkbox" class="js-dbt-cb" onchange="add_serialize_checkboxes(this)" '+checked+'>' +  label_response + '<label></li>');
        $add.data('dbt_memory',  response.result[i].c);
        $add.find('input').val(value);
        $ul_box.append($add);
    }
   
    for (add_li in left_checkbox) {
        checkbox_selected_count++;
        value = left_checkbox[add_li];
        label_response = left_checkbox[add_li];
        if (value.substring(0,1) == '^') {
            label_response = "primary key "+value.substring(1);
        }
        if (value == '_##Empty values##_') {
            label_response = "Empty";
        } else {
            label_response = String(label_response).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        $add = jQuery('<li><label class="dbt-selected-search-checkboxes"><input type="checkbox" class="js-dbt-cb " onchange="add_serialize_checkboxes(this)" checked="checked">' +   label_response + '<label></li>');
        $add.find('input').val(left_checkbox[add_li]);
        $ul_box.append($add);
    }
    return checkbox_selected_count;
}

/**
 * Aggiorna il footer dei checkboxes dentro il drowdown
 */
function update_dropdown_footer_checkboxes(rif, text_selected, text_total) {
    jQuery('#dbt_dd_count-cb_'+rif+" > .js-dbt-cb-count-selected").text(text_selected);
    jQuery('#dbt_dd_count-cb_'+rif+" > .js-dbt-cb-count-total").text(text_total);
}

/**
 * SERIALIZE DEI CLICK SUI CHECKBOX DEI DROPDOWN
 */
 function add_serialize_checkboxes (el) {
    //console.log ('add_serialize_checkboxes'+el);
    let $ul_box =  jQuery(el).parents('ul');
  
    let count = 0;
    if ($ul_box) {
        let rif = $ul_box.data('rif');
        result = [];
        $ul_box.find('.js-dbt-cb').each(function() {
            if (jQuery(this).is(':checked')) {
                result.push(jQuery(this).val());
            }
            count++;
        });
       
        if (count == result.length && jQuery('#dbt_input_filter_checkboxes_'+rif).val() == '') {
            jQuery('#dbt_dropdown_search_value_'+rif).val('');
        } else {
            jQuery('#dbt_dropdown_search_value_'+rif).val(result.join(','));
        }
        jQuery('#dbt_dd_count-cb_'+rif+" > .js-dbt-cb-count-selected").text(result.length);
    }

}

// disegno i checkbox dentro il dropdown
function ajax_load_distinct_values(rif) {
    //console.log('ajax_load_distinct_values '+rif);
    let sql = jQuery('#sql_query_executed').val();
    if (jQuery('#filter_search_filter_'+rif).val() == 1) {
        sql = jQuery('#sql_query_edit').val();
    } 
    let $ul =  jQuery('#dbt_checkboxes_value_' + rif + ' > ul');
    let = val_filter = jQuery('#dbt_input_filter_checkboxes_'+rif).val();
    
    let field =  jQuery('#filter_search_original_column'+rif).val();
    update_dropdown_footer_checkboxes(rif, 0, 'loading...');
    $ul.parent().find('.js-dbt-ul-msg').remove();
    //$ul.data('dbt_loaded_distinct', 't');
    $ul.data('dbt_distinct_filter', val_filter);

    memory_name = jQuery('#dbt_input_filter_checkboxes_' + rif).data('dbt_memory_name');
    let $ul_box = jQuery('#dbt_checkboxes_value_' + rif + ' > ul');
    if (typeof(memory_name) != 'undefined' && ((memory_name != '' && val_filter.substring(0, memory_name.length) == memory_name) || memory_name == '_#ALL#_')) {
        $ul_box.find('li').each(function() {
            if ( (typeof(jQuery(this).data('dbt_memory')) != 'undefined' && jQuery(this).data('dbt_memory').toLowerCase().indexOf(val_filter.toLowerCase()) > -1) || jQuery(this).find('input').is(':checked')) {
                jQuery(this).removeClass('li-hide').addClass('li-show');
             } else {
                jQuery(this).removeClass('li-show').addClass('li-hide');
             }
        });
        update_dropdown_footer_checkboxes(rif, 0, $ul_box.find('li.li-show').length);
    } else {
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : ajaxurl,
            data : {'sql':sql, 'rif':rif, 'column':field, 'action': 'dbt_distinct_values', 'table': jQuery('#filter_search_orgtable_'+rif).val(),'filter_distinct':val_filter},
            success: function(response) {
                let $ul_box = jQuery('#dbt_checkboxes_value_' + response.rif + ' > ul');
                $ul_box.find('li').remove();
                let checkbox_selected_count = 0;
                if (response.error != "") {
                    $ul_box.after('<div class="dtf-alert-ul-msg js-dbt-ul-msg">'+response.error+'</div>');
                    $ul_box.data('loaded', 'f');
                    //console.log("ERROR?! "+response.error);
                    // C'è qualche tipo di errore, disabilito i select-all deselect-all
                    jQuery('#dbt_choose_values_box_'+ response.rif).find('.js-dropdown-select-all-checkboxes').removeClass('dbt-dropdown-line-click').addClass('dbt-dropdown-de-select-disable');
                    jQuery('#dbt_choose_values_box_'+ response.rif).find('.js-dropdown-deselect-all-checkboxes').removeClass('dbt-dropdown-line-click').addClass('dbt-dropdown-de-select-disable');

                } else { 
                    if (parseInt(response.count) < 1000) {
                        if (response.filter_distinct == "") {
                            jQuery('#dbt_input_filter_checkboxes_'+rif).data('dbt_memory_name', '_#ALL#_');
                        } else {
                            jQuery('#dbt_input_filter_checkboxes_'+rif).data('dbt_memory_name', response.filter_distinct);
                        }
                    } else {
                        jQuery('#dbt_input_filter_checkboxes_'+rif).data('dbt_memory_name', '');
                    }
                    $ul_box.parent().find('.js-dbt-ul-msg').remove();
                    // popolo i checkbox
                    checkbox_selected_count = populate_drowdown_checkboxes($ul_box, response);
                
                    if (response.count > 0 ) {
                        jQuery('#dbt_choose_values_box_'+ response.rif).find('.js-dropdown-select-all-checkboxes').removeClass('dbt-dropdown-de-select-disable').addClass('dbt-dropdown-de-select-click');
                        jQuery('#dbt_choose_values_box_'+ response.rif).find('.js-dropdown-deselect-all-checkboxes').removeClass('dbt-dropdown-de-select-disable').addClass('dbt-dropdown-de-select-click');
                    } else {
                        jQuery('#dbt_choose_values_box_'+ response.rif).find('.js-dropdown-select-all-checkboxes').removeClass('dbt-dropdown-de-select-click').addClass('dbt-dropdown-de-select-disable');
                        jQuery('#dbt_choose_values_box_'+ response.rif).find('.js-dropdown-deselect-all-checkboxes').removeClass('dbt-dropdown-de-select-click').addClass('dbt-dropdown-de-select-disable');
                    }
                }
                update_dropdown_footer_checkboxes(rif, checkbox_selected_count, response.count);
                jQuery('#dbt_checkboxes_value_' + response.rif).data('loaded_data', 't');
            }
        });
    }

}


// FINE DROPDOWN

/**
 * SUBMIT FORM
 */
function dtf_submit_table_filter(action) {
    if (action == 'custom_query') {
        if (typeof (document.getElementById('sql_query_edit').dtf_editor_sql) != "undefined") {
            code = document.getElementById('sql_query_edit').dtf_editor_sql;
            jQuery('#sql_query_edit').value = code.codemirror.getValue();
        }
        jQuery('#dtf_table_filter_limit_start').val(0);
    } else {
        // ripristino la query di default
        jQuery('#sql_query_edit').val(jQuery('#sql_default_query').val());
    }
    if (action == 'filter' || action == 'order') {
        jQuery('#dtf_table_filter_limit_start').val(0);
    }
    if (action == 'delete_from_sql') {
        let tq = jQuery('input[name="remove_tables_query"]:checked').val(); 
        jQuery('#sql_query_executed').prop('name', 'sql_query_executed'); 
        jQuery('#table_filter').append('<input type="hidden" name="remove_table_query" value="'+tq+'">');
    }
    if (action == 'delete_rows') {
        $add_form = jQuery('#dbt_form_deletes_rows').clone();
        $add_form.find('.dbt-xmp').remove();
        $add_form.css('display','none');
        jQuery('#table_filter').append($add_form);
    }
    jQuery('#dtf_action_query').val(action);
    jQuery('#table_filter').submit();
}


/**
 * Cancella i filtri di ricerca
 */
function dbt_clear_filter() {
    dtf_submit_table_filter('custom_query');
}

/**
 * Gestione dei checkbox della tabella
 * @param {} json 
 */
function dbt_table_checkboxes(el) {
   // console.log (jQuery(el).is(':checked'));
    jQuery(el).parents('table').find('.js-dbt-table-checkbox').prop('checked', jQuery(el).is(':checked'));
}

/**
 * Bulk actions Le azioni sui record selezionati
 */
function dbt_bulk_actions() {
    let action = jQuery('#dbt_bulk_action_selector_bottom').val();
    let mwith = jQuery('#dbt_bulk_on_selector_bottom').val();
    if (action == "delete" && mwith == 'sql') {
        let sql = jQuery('#sql_query_executed').val();
        dbt_open_sidebar_popup('bulk_delete');
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : ajaxurl,
            data : {'sql':sql,  'action': 'dbt_check_delete_from_sql' },
            success: function(response) { 
                dbt_close_sidebar_loading();
                if (response.error != "") {
                    jQuery('#dbt_dbp_content').append('<div class="dtf-alert-sql-error" style="margin-top:0; margin-right:.4rem">'+response.error+'</div>');
                } else {
                    jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
                    jQuery('#dbt_dbp_title').append('<div class="dbt-edit-btns"><div id="dbt-bnt-edit-query" class="dbt-submit-warning" onclick="dtf_submit_table_filter(\'delete_from_sql\')">DELETE</div></div>');

                    let $form = jQuery('<div class="dbt-form-deletes-rows" id="dbt_form_deletes_rows"></div>');
                    let checkfirst = ' checked="checked"';
                    for (x in response.items) {
                        $form.append('<div class="dbt-dropdown-line-flex"><span style="margin-right:.5rem"><input name="remove_tables_query" type="radio"'+checkfirst+' value="'+x+'"></span><div class="dbt-xmp">'+response.items[x]+'</div></div>');
                        checkfirst = '';
                    }
                    jQuery('#dbt_dbp_content').append($form);
                }
               

                
            }
        });

    }
    if (action == "delete" && mwith == 'checkboxes') {
        var table_ids = [];
        jQuery('#table_filter .js-dbt-table-checkbox:checked').each(function() {
            table_ids.push(dbt_tb_id[jQuery(this).val()]);
        });
        console.log (" !!DELETE CHECKBOXES: " + table_ids);
        dbt_delete_confirm(table_ids);
    }
    if (action == "download" && mwith == 'checkboxes') {
        var table_ids = [];
        jQuery('#table_filter .js-dbt-table-checkbox:checked').each(function() {
            table_ids.push(dbt_tb_id[jQuery(this).val()]);
        });
        let sql = jQuery('#sql_query_executed').val();
        dbt_open_sidebar_popup('download');
        dbt_close_sidebar_loading();
        jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
        jQuery('#dbt_dbp_title').append('<div class="dbt-edit-btns"><h3>Export Data</h3></div>');
        jQuery('#dbt_dbp_content').append('<div class="dbt-download-data"></div>');
        dbt_download_csv(sql, table_ids, 0 ,'');
    }
    if (action == "download" && mwith == 'sql') {
        let sql = jQuery('#sql_query_executed').val();
        dbt_open_sidebar_popup('download');
        dbt_close_sidebar_loading();
        jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
        jQuery('#dbt_dbp_title').append('<div class="dbt-edit-btns"><h3>Export Data</h3></div>');
        jQuery('#dbt_dbp_content').append('<div class="dbt-download-data"></div>');
        dbt_download_csv(sql, false, 0 ,'');
    }
}

/**
 * prepara il csv da scaricare 
 * @param {*} json 
 */
function dbt_download_csv(sql, table_ids, limit_start, csv_filename) {
    data = {'sql':sql, 'ids':table_ids, 'limit_start':limit_start, 'csv_filename':csv_filename, 'action':'dbt_download_csv', 'section':'table-browse'};
    if (typeof dbt_global_list_id != 'undefined' && dbt_global_list_id > 0) {
        data.dbt_id = dbt_global_list_id;
    }
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        cache: false,
        data : data,
        success: function(response) {
            $content =   jQuery('#dbt_dbp_content .dbt-download-data').first();
            if (response.error == "") {
                if (response.next_limit_start < response.count && response.next_limit_start > 0) {
                    let perc = Math.round((response.next_limit_start/response.count) * 100);
                    $content.empty().append("<p>Export: "+perc+"% "+response.msg+"</p>");
                    dbt_download_csv(sql, table_ids, response.next_limit_start, response.filename); 
                } else {
                    $content.empty().append("<p>Export: " + response.count + " items</p>");
                    let $a = jQuery('<a href="'+response.link+'" class="dbt-submit" onclick="setTimeout(function(){ dbt_close_sidebar_popup();},200); return true;">Click here to download</a>');
                    $content.append($a);
                }
            } else {
                jQuery('#dbt_dbp_content').append('<div class="dtf-alert-sql-error" style="margin-top:0; margin-right:.4rem">'+response.error+'</div>');
            }
        }
    });
}

/**
 * Invia la query per visualizzare il dettaglio della singola riga
 */
function dbt_view_details(json) {
    let sql = jQuery('#sql_query_executed').val();
    dbt_open_sidebar_popup('view');
    data_to_send =  {'sql':sql, 'ids':json,'action':'dbt_view_details', 'section':'table-browse'};
    if (typeof dbt_global_list_id != 'undefined' && dbt_global_list_id > 0) {
        data_to_send.dbt_id = dbt_global_list_id;
    }
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        cache: false,
        data : data_to_send,
        success: function(response) {
            dbt_close_sidebar_loading();
            if (response.error != "") {
                jQuery('#dbt_dbp_content').append('<div class="dtf-alert-sql-error" style="margin-top:0; margin-right:.4rem">'+response.error+'</div>');
            }
            let class_box = (Object.keys(response.items).length > 1) ? "dbt-view-multi-box" : "dbt-view-single-box";
            for (x in response.items) {
                $box = jQuery('<div class="'+class_box+' js-dbt-form-box"></div>');
                for (y in response.items[x]) {
                    $box.append('<div class="dbt-row-details"><span class="dbt-label-detail">'+y+':</span><div class="dbt-xmp">'+response.items[x][y]+'</div></div>');
                }
                jQuery('#dbt_dbp_content').append($box);
            }
           
        }
    });
}


/**
 * Invia la query per visualizzare il dettaglio della singola riga
 * @param {Array} ids {table.pri:val, ...}
 */
 function dbt_delete_confirm(ids, el) {
    if (jQuery('#dbt_dbp_title > .js-sidebar-btn').first().hasClass('js-btn-disabled')) return;
    let sql = jQuery('#sql_query_executed').val();
    $checkbox_el = jQuery(el).parents('tr').find('.js-dbt-table-checkbox');
    if ($checkbox_el) {
        $checkbox_el.prop('checked', true);
    }
    opensidebar = dbt_open_sidebar_popup('delete');
    data_to_send = {'sql':sql, 'ids':ids,'action':'dbt_delete_confirm', 'section':'table-browse'};
    if (typeof dbt_global_list_id != 'undefined' &&  dbt_global_list_id > 0) {
        data_to_send.dbt_id = dbt_global_list_id;
    }
    jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
    jQuery('#dbt_dbp_title').append('<div class="dbt-edit-btns"><h3>DELETE ROWS CONFIRM</h3></div>');
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        cache: false,
        data : data_to_send,
        success: function(response) {
            if (opensidebar != "already_open") {
                dbt_close_sidebar_loading();
            }
            hidden_checkbox = '';
            if (response.error != "") {
                jQuery('#dbt_dbp_content .dtf-alert-sql-error').remove();
                jQuery('#dbt_dbp_content').append('<div class="dtf-alert-sql-error" style="margin-top:0; margin-right:.4rem">'+response.error+'</div>');
            }
            if (response.show_msg != '') {
                hidden_checkbox = 'style="display:none"';
            }
            jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
            jQuery('#dbt_dbp_title').append('<div class="dbt-edit-btns"><h3>DELETE ROWS CONFIRM</h3><div id="dbt-bnt-edit-query" class="dbt-submit-warning js-sidebar-btn" onclick="dtf_submit_table_filter(\'delete_rows\')">DELETE</div> <div id="dbt-bnt-edit-query" class="dbt-btn-cancel" onclick="dbt_close_sidebar_popup()">CANCEL</div></div>');
            if (opensidebar == "already_open") {
                for (x in response.items) {
                    let input_lists = document.querySelectorAll('#dbt_form_deletes_rows input');
                    add_check = true;
                    for (il of input_lists) {
                        let ilval = jQuery(il).val();
                        ilval = String(ilval).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); 
                       if (String(ilval) == String(response.checkboxes[x])) {
                           add_check = false;
                           break;
                       }
                    };
                   
                    if (add_check) {
                        jQuery('#dbt_form_deletes_rows').append('<div class="dbt-row-details" '+hidden_checkbox+'><span class="dbt-label-detail"><input name="remove_ids[]" type="checkbox" checked="checked" class="js_checkbox_to_remove_ids" value="'+response.checkboxes[x]+'"></span><div class="dbt-xmp">'+response.items[x]+'</div></div>');
                    }
                }
            } else {
                let $form = jQuery('<div class="dbt-form-deletes-rows" id="dbt_form_deletes_rows"></div>');
                for (x in response.items) {
                    $form.append('<div class="dbt-row-details" '+hidden_checkbox+'><span class="dbt-label-detail"><input name="remove_ids[]" type="checkbox" checked="checked" class="js_checkbox_to_remove_ids" value="'+response.checkboxes[x]+'"></span><div class="dbt-xmp">'+response.items[x]+'</div></div>');
                }
                jQuery('#dbt_dbp_content').append($form);
              
            }
            if (response.show_msg != '') {
                jQuery('.js-custom-msg-delete').remove();
                response.show_msg = response.show_msg.replace("\%s", jQuery('.js_checkbox_to_remove_ids').length);
                jQuery('#dbt_form_deletes_rows').append('<div class="dtf-alert-warning js-custom-msg-delete">'+response.show_msg+'</div>');
                hidden_checkbox = 'style="display:none"';
            }
        }
    });
}

/**
 * Apre la form per modificare o inserire i dati nelle tabelle
 * @param string json Optional Gli id da caricare
 * @param DOM el  Optional La riga che si sta caricando 
 */
 function dbt_edit_details_v2(json, el) {
    let color_list = ['white', 'green','yellow','blue','red','purple', 'brown'];
    data_to_send = {'action':'dbt_edit_details_v2', 'section':'table-browse'};
    if (el instanceof HTMLElement) {
        data_to_send.div_id = jQuery(el).parents('tr').first().prop('id');
    }
    if (typeof dbt_global_list_id != 'undefined' &&  dbt_global_list_id > 0) {
        data_to_send.dbt_id = dbt_global_list_id;
    } else {
        data_to_send.sql = jQuery('#sql_query_executed').val();
    }
    dbt_open_sidebar_popup('edit');
    if (json != "") {
        data_to_send.ids = json;
    } 

    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        cache: false,
        data : data_to_send,
        success: function(response) {
            dbt_close_sidebar_loading();
            if (response.error != "") {
                jQuery('#dbt_dbp_content').append('<div class="dtf-alert-sql-error" style="margin-top:0; margin-right:.4rem">'+response.error+'</div>');
            } else {
                $form = dbt_build_form(jQuery('#dbt_dbp_content'), response);
                if (response.edit_ids) {
                    $form.data('edit_ids', response.edit_ids);
                }
                if (response.div_id) {

                    $form.append('<input type="hidden" name="div_id" value="'+response.div_id+'">');
                    jQuery('#dbt_dbp_title').append(gp_form_btns_edit(response.div_id));
                } else {
                    jQuery('#dbt_dbp_title').append(gp_form_btns_new());
                }
            }
        }
    })
 }


function gp_form_btns_new() {
    jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
    return jQuery('<div class="dbt-edit-btns"><div class="dbt-submit js-sidebar-btn" onclick="gp_submit_form()">SAVE</div></div>');
}

/**
 * Aggiungo nella sidebar in alto i bottoni per salvare o eliminare un record
 * @param {String} div_id  l'id della riga che si sta modificando
 * @returns {DOM}
 */
function gp_form_btns_edit() {
    jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
    return jQuery('<div class="dbt-edit-btns"><h3>EDIT ROW</h3><div class="dbt-submit js-sidebar-btn" onclick="gp_submit_form()">SAVE</div> <div class="dbt-submit-warning js-sidebar-btn" onclick="gp_delete_form_edit()">DELETE</div></div>');
}


/**
 * Rimuovi il record selezionato nell'edit
 */
function gp_delete_form_edit() {
    if (jQuery('#dbt_dbp_title .dbt-edit-btns .js-sidebar-btn').first().hasClass('js-btn-disabled')) return;
    let edit_ids = jQuery('#dbt_edit_details').data('edit_ids');
    dbt_delete_confirm([edit_ids]);
}

// Invio la form
function gp_submit_form() {
    if (jQuery('#dbt_dbp_title .dbt-edit-btns .js-sidebar-btn').first().hasClass('js-btn-disabled')) return;
    dbt_open_sidebar_loading(true);
    let $form = jQuery('#dbt_edit_details');
    
    dbt_validate_form = true;
    $form.parent().find('.dtf-alert-sql-error').remove();

    $form.find('.js-dbt-fn-set').each(function() {
        let __custom_fn = jQuery(this).data('dbt_fn');
        dbt_exec_fn(__custom_fn, this, 'submit');
    })

    $form.find('.js-add-tinymce-editor').each(function() {
        if (jQuery(this).data('tny_editor') == 't') {
            var tiny_content =  tinyMCE.get(jQuery(this).prop('id')).getContent();
            jQuery(this).val(tiny_content);
        }
    });
    $form.find('.js-add-codemirror-editor').each(function() {
        if (jQuery(this).data('cm_editor')) {
            cm_editor = jQuery(this).data('cm_editor');
            var cm_content =  cm_editor.codemirror.getValue();
            jQuery(this).val(cm_content);
        }
    });

    $form.find('.js-dbt-validity').each(function() {
        if (!this.checkValidity()) {
            $form.prepend('<div class="dtf-alert-sql-error" style="margin:0">"<b>' + jQuery(this).data('dbt_label') + '</b>" '+this.validationMessage+'</div>');
            dbt_validate_form = false;
            if (jQuery(this).hasClass('js-dbt-autocoplete-id-title')) {
                jQuery(this).css('display','block');
                jQuery(this).parent().find('.js-fake-autocomplete').css('display','none');
            }
        }
    });

    
    if (!dbt_validate_form) {
        return false;
    }

    // var data = $form.serializeArray() ;
    var data = new FormData(document.getElementById('dbt_edit_details')); 
    data.append( 'action', 'dbt_save_details');
    data.append('section', 'table-browse');
   // .append('SomeField', 'SomeValue');
    let edit_ids =  $form.data('edit_ids');
    if (edit_ids) {
        for (ei in edit_ids) {
            data.append('ids['+ei+']', edit_ids[ei]);
        }
    }
    if (typeof dbt_global_list_id != 'undefined' &&  dbt_global_list_id > 0) {
        data.append('dbt_global_list_id', dbt_global_list_id);
    } else {
        data.append('sql', jQuery('#sql_query_executed').val());
    }
    $form.css('display','none');
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        cache: false,
        data : data,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.error != "") {
                $form.prepend('<div class="dtf-alert-sql-error" style="margin-bottom:1rem">'+response.error+'</div>');
                $form.css('display','block');
                //jQuery('#dbt_cookie_error').empty().html(response.error).css('display','block');
                // delete_cookie('dbt_error');
            } else if (response.reload == 1) {
                dtf_submit_table_filter('limit_start');
            } else {
                dbt_close_sidebar_popup();
                let row_edited = response.table_item_row;
                console.log ("ROW EDITED "+row_edited);
                if (row_edited) {
                    console.log ("ROW EDITED OK DIV_ID" + response.div_id);
                    let div_id = response.div_id;
                    jQuery('#'+div_id).css('background-color','#F1E410');
                    // in realtà non funziona ma mi serve per il tempo!
                    jQuery('#'+div_id).animate({'backgroundColor':'#FFFFFF'}, 5000, function() {jQuery(this).css('background-color','');}
                    );
                    for (re in row_edited) {
                        if ( jQuery('#'+div_id+' div[data-dbt_rif_value=\''+re+'\'] .js-text-content').length > 0) {
                            jQuery('#'+div_id+' div[data-dbt_rif_value=\''+re+'\'] .js-text-content').html(row_edited[re]);
                        } else {
                            jQuery('#'+div_id+' div[data-dbt_rif_value=\''+re+'\']').html(row_edited[re]);
                           
                        }
                    }
                }
                if ( response.msg != "") {
                    jQuery('#dbt_cookie_msg').empty().html(response.msg).css('display','block');
                    delete_cookie('dbt_msg');
                }
                
            }
            
        }
    });
}


/**
 * Il bottone per salvare una query
 */
function dbt_show_save_sql_query() {
    dbt_open_sidebar_popup('save_sql');
    dbt_close_sidebar_loading();
    let $form = jQuery('<form class="dbt-form-save-query dbt-form-edit-row" id="dbt_form_save_new_query" action="'+dbt_admin_post+'"></form>');

    $form.append('<input type="hidden" name="page" value="database_tables"><input type="hidden" name="section" value="list-add"><input type="hidden" name="action" value="dbt_create_list">');

    $form.append('<p class="dtf-alert-info">Save the query. Then you will have the shortcode to view the table on the website.</p>');

    $field_row = jQuery('<div class="dbt-form-row"></div>');
    $field_row.append('<label><span class="dbt-form-label">Name</span></label><input type="text" class="form-input-edit" name="new_title" id="dbt_name_create_list">');
    $form.append($field_row);
    code = document.getElementById('sql_query_edit').dtf_editor_sql;
    
    let get_first_row = jQuery('#sql_query_edit').val().toLowerCase();
    if (typeof(code) != "undefined") {
         get_first_row = code.codemirror.getValue().toLowerCase();
    }
    let temp_name = get_first_row.split("from");
    new_title = "query_"+ dbt_uniqid();
    if (temp_name.length > 1) {
        let temp_name2 = temp_name[1].trim().split(" ");
        if (temp_name2.length > 0 && temp_name2[0].length > 2) {
            new_title = temp_name2[0].trim().replace(/ .*/,'').replaceAll('`','').substring(0,20);
        } else {
            new_title = temp_name[1].trim().replace(/ .*/,'').replaceAll('`','').substring(0,20);
        }
    }
    $field_row.find('#dbt_name_create_list').val(new_title);

    $field_row  = jQuery('<div class="dbt-form-row"><label><span class="dbt-form-label">Description</span><textarea  class="form-textarea-edit" name="new_description"></textarea></label></div>');
    $form.append($field_row);

    $form.append('<div class="dbt-form-row"><label><span class="dbt-form-label">Choose with query use</span>');
    $form.append('<div class="dbt-dropdown-line-flex"><span style="margin-right:.5rem"><input name="choose_tables_query" type="radio" checked="checked" value="sql_query_executed"></span><div class="dbt-xmp">If you used filters to limit your data, save the query with the filtered results</div></div>');
    $form.append('<div class="dbt-dropdown-line-flex"><span style="margin-right:.5rem"><input name="choose_tables_query" type="radio" value="sql_query_edit"></span><div class="dbt-xmp">Show all data without filters</div></div>');
    $form.append('<textarea style="display:none" id="dbt_sql_new_list" name="new_sql"></textarea>');
    jQuery('#dbt_dbp_content').append($form);

    $field_row.append('<input type="hidden" class="form-input-edit" name="new_sort_field" value="'+jQuery('#dtf_table_sort_field').val()+'">');
    $field_row.append('<input type="hidden" class="form-input-edit" name="new_sort_order" value="'+jQuery('#dtf_table_sort_order').val()+'">');

    jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
    jQuery('#dbt_dbp_title').append('<div class="dbt-edit-btns"><h3>New List</h3><div id="dbt-bnt-edit-query" class="dbt-submit" onclick="dbt_save_sql_query()">Save</div></div>');

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