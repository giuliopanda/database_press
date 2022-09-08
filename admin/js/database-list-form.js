jQuery(document).ready(function ($) {
    //aggiungo la possibilità di fare il sort sulla creazione del nuovo db
    jQuery('.js-dragable-table').sortable({
        items: '.js-dragable-fields',
        opacity: 0.5,
        cursor: 'move',
        axis: 'y',
        handle: ".js-dragable-handle"
    });

    jQuery('.js-toggle-row').change();
    jQuery('.js-type-fields').change();
    jQuery('.js-structure-toggle').click();

    // disegno la demo dei singoli field
    jQuery('.js-dbp-lf-form-card').each(function() {
        if ($(this).find('.js-show-hide-select').val() == "SHOW") {
            dbp_lf_show_field_example($(this));
        } else {
            $(this).find('.js-lf-form-field-example').css('display','none');
        }
    })
    
    jQuery('.js-lf-container-table').each(function() {
        if  (jQuery(this).find('.js-module-type').val() == 'HIDE') {
            jQuery(this).find('.js-lf-dbp-show').click();
            jQuery(this).find('.js-dragable-table').css('display','none');
            jQuery(this).find('.js-row-allow-create-record').css('display','none');
        } else if  (jQuery(this).find('.js-module-type').val() == 'READONLY') {
            jQuery(this).find('.js-lf-dbp-show').click();
            jQuery(this).find('.js-row-allow-create-record').css('display','none');
            jQuery(this).find('.js-lf-form-field-example').css('display','none');
            jQuery(this).find('.js-lf-form-content').css('display','none');
        }
    })

});

/**
 * Invia la form, ma prima imposta i campi da riordinare
 */
 function dbp_submit_list_form() {
    var count_list = 0;
    jQuery('.js-dragable-order').each(function() {
        jQuery(this).val(count_list);
        count_list++;
    });
    // prevent Input variables exceeded 1000.
    jQuery('#list_form .js-prevent-exceeded-1000').each(function() {
        if (jQuery(this).val() == "") {
            jQuery(this).remove();
        }
    });
    jQuery('#list_form .js-dbp-lf-form-card').each(function() {
        if (jQuery(this).find('.js-fields-field-type').val() != 'LOOKUP') {
            jQuery(this).find('.js-prevent-exceeded-1000-lookup').remove();
        }
    });
    jQuery('#list_form .js-dbp-example').empty();


    jQuery('#list_form').submit();
}

/**
 * Mostra/Nasconde gli attributi di una colonna
 */
function dbp_lf_form_toggle(el) {
    let $card = jQuery(el).parents('.js-dbp-lf-form-card');
    let $container_table = jQuery(el).parents('.js-lf-container-table');
    if ($container_table.find('.js-module-type').val() != "EDIT") {
        alert ("Set the module type in show attributes in Editable");
        dbp_lf_toggle_attr($container_table.find('.js-lf-dbp-show').get(0), 1);
        return;
    }
    let $box = $card.find('.js-lf-form-content');
    if ($box.css('display') == "none") {
        $box.css('display','block');
        $card.find('.js-lf-form-field-example').css('display','none');
    } else {
        dbp_lf_show_field_example($card);
    }
}

function dbp_lf_select_type_change(el) {
    let $card = jQuery(el).parents('.js-dbp-lf-form-card');
    if (jQuery(el).val() == "PRI") {
        $card.find('.js-dbp-user-data').css('display','none');
        $card.find('.js-default-custom-class-row').css('display','none');
        $card.find('.js-row-field-note').css('display','none');
        $card.find('.js-label-required').css('display','none');
        $card.find('.js-javascript-script-block').css('display','none');
        return;
    } else {
        $card.find('.js-dbp-user-data').css('display','grid');
        $card.find('.js-default-custom-class-row').css('display','grid');
        $card.find('.js-row-field-note').css('display','none');
        $card.find('.js-label-required').css('display','block');
        $card.find('.js-javascript-script-block').css('display','grid');
    }
    if (jQuery(el).val() == "SELECT" || jQuery(el).val() == "RADIO" || jQuery(el).val() == "CHECKBOXES") {
        $card.find('.js-lf-options-content').css('display','grid');
    } else {
        $card.find('.js-lf-options-content').css('display','none');
    }
    if (jQuery(el).val() == "CHECKBOX") {
        $card.find('.js-lf-checkbox-value').css('display','grid');
    } else {
        $card.find('.js-lf-checkbox-value').css('display','none');
    }
    if (jQuery(el).val() == "POST") {
        $card.find('.js-dbp-post-data').css('display','grid');
    } else {
        $card.find('.js-dbp-post-data').css('display','none');
    }
    if (jQuery(el).val() == "USER") {
        $card.find('.js-dbp-user-data').css('display','grid');
    } else {
        $card.find('.js-dbp-user-data').css('display','none');
    }
    if (jQuery(el).val() == "LOOKUP") {
        $card.find('.js-dbp-lookup-data').css('display','block');
        $card.find('.js-select-fields-lookup').change();
    } else {
        $card.find('.js-dbp-lookup-data').css('display','none');
    }

    if (jQuery(el).val() == "READ_ONLY" || jQuery(el).val() == "CREATION_DATE"  || jQuery(el).val() == "LAST_UPDATE_DATE"  || jQuery(el).val() == "RECORD_OWNER"  || jQuery(el).val() == "MODIFYING_USER"  || jQuery(el).val() == "CALCULATED_FIELD") {
        $card.find('.js-input-required').prop('checked',false);
        $card.find('.js-label-required').css('display','none');
    } else {
        $card.find('.js-label-required').css('display','block');
    }
    if (jQuery(el).val() == "CALCULATED_FIELD") {
        $card.find('.js-calculated-field-block').css('display','grid');
        $card.find('.js-javascript-script-block').css('display','none');
    } else {
        $card.find('.js-calculated-field-block').css('display','none');
        $card.find('.js-javascript-script-block').css('display','grid');
        
    }
}


/**
 * Testa una formula rispetto alla tabella
 * @param {DOM} el 
 */
function click_dbp_test_formula(el) {
    if (jQuery(el).hasClass('disabled')) return;
    jQuery(el).addClass('disabled');
    var formula = jQuery(el).parents('.js-calculated-field-block').find('textarea').val();
    var test_row = jQuery(el).parents('.js-calculated-field-block').find('.js-choose-test-row').val();
    dbp_open_sidebar_popup('test_formula');
    jQuery('#dbp_dbp_title > .dbp-edit-btns').remove();

    jQuery('#dbp_dbp_title').append('<div class="dbp-edit-btns"><h3>Test Formula</h3></div>');
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        data : {'page':'dbp_list','action':'dbp_test_formula','dbp_id':jQuery('#dbp_id_list').val(),'formula':formula, 'row':test_row},
        success: function(response) {
        // console.log (response);
            dbp_close_sidebar_loading();
            // jQuery('#dbp_dbp_title > .dbp-edit-btns').remove(); // Titolo e bottoni
            $block1 = jQuery('<div class="dbp-view-single-box dbp-form-box-white dbp-content-margin "></div>');
            $formula = jQuery('<div class="dbp-xmp"></div>').text(response.formula);
            $typeof = jQuery('<div class="dbp-xmp"></div>').text(response.typeof);
            $row_formula = jQuery('<div class="dbp-row-details"></div>');
            $row_response = jQuery('<div class="dbp-row-details"></div>');
            $row_typeof = jQuery('<div class="dbp-row-details"></div>');
        // console.log ('response.response');
        //  console.log (response.response);
            if (typeof(response.response) == "object") {
            $resBlock = jQuery('<div class="dbp-sidebar-test-formula-params"></div>');
            test_formula_show_data('', response.response, $resBlock);
            $row_response.append('<div class="dbp-label-detail">Response:</div>').append($resBlock);
            response.warning.push('The result must be a text string and not an object or array');
            } else {
            $response = jQuery('<div class="dbp-xmp"></div>').text(response.response);
            $row_response.append('<div class="dbp-label-detail">Response:</div>').append($response);
            }

            $row_formula.append('<div class="dbp-label-detail">Test Formula:</div>').append($formula);
            $row_typeof.append('<div class="dbp-label-detail">Type of:</div>').append($typeof);
            $block1.append($row_response).append($row_typeof).append($row_formula);

            jQuery('#dbp_dbp_content').append($block1 );
        
            if (response.error.length > 0) {
            $resBlockError = jQuery('<div class="dbp-content-margin dbp-alert-sql-error"></div>');
            for (x in response.error) {
                $resBlockError.append ('<p>'+response.error[x]+'</p>');
            }
            jQuery('#dbp_dbp_content').append($resBlockError);
            }
            if (response.warning.length > 0) {
            $resBlockWarning = jQuery('<div class="dbp-content-margin dbp-alert-warning"></div>');
            for (x in response.warning) {
                $resBlockWarning.append ('<p>'+response.warning[x]+'</p>');
            }
            jQuery('#dbp_dbp_content').append($resBlockWarning);
            }

            if (response.notice.length > 0) {
            $resBlockNotice = jQuery('<div class="dbp-content-margin dbp-alert-info"></div>');
            for (x in response.notice) {
                $resBlockNotice.append ('<p>'+response.notice[x]+'</p>');
            }
            jQuery('#dbp_dbp_content').append($resBlockNotice);
            }

            $block2 = jQuery('<div class="dbp-sidebar-test-formula-params dbp-content-margin"></div>');
            test_formula_show_data('', response.pinacode_data, $block2);
            jQuery('#dbp_dbp_content').append('<h3 class="dbp-content-margin">Template Engine Variables</h3>').append($block2);
        
            jQuery('.js-test-formula').removeClass('disabled');
        }, error : function(xhr, status, error) {
            dbp_close_sidebar_loading();
            $resBlockNotice = jQuery('<div class="dbp-content-margin dbp-alert-sql-error"></div>');
            $resBlockNotice.append ('<p>there was an unexpected error</p>');
            jQuery('#dbp_dbp_content').append($resBlockNotice);
            jQuery('.js-test-formula').removeClass('disabled');
        }
    });
}

function test_formula_show_data(left_name, data, $block) {
    for (x in data) {
        if (typeof(data[x]) == 'object' || typeof(data[x]) == 'array') {
            if (left_name == "") {
            new_left_name = x;
            } else {
            new_left_name = left_name+"."+x;
            }
            test_formula_show_data(new_left_name,  data[x], $block);
            
        } else if (typeof(data[x]) == 'string' || typeof(data[x]) == 'number' || typeof(data[x]) == 'boolean') {
            $var = jQuery('<div class="dbp-xmp"></div>').text(data[x]+" "+typeof(data[x]));
            $row = jQuery('<div class="dbp-row-details"></div>');
            if (left_name == "") {
            var_name = x;
            } else {
            var_name = left_name+"."+x;
            }
            $row.append('<div class="dbp-label-detail">[%'+var_name+']</div>').append($var);
            $block.append($row);
        }
    }
  
}



/**
 * ricalcola una formula rispetto alla tabella
 * @param {DOM} el 
 */
function click_dbp_recalculate_formula(el_id, limit_start, total) {
    if (confirm('Are you sure you want to recalculate all records?')) {
        $el = jQuery('#'+el_id);
        if ($el.hasClass('disabled')) return;
        $el.addClass('disabled');
        dbp_open_sidebar_popup('test_formula');
        jQuery('#dbp_dbp_title > .dbp-edit-btns').remove();
        jQuery('#dbp_dbp_title').append('<div class="dbp-edit-btns"><h3>Recalculate and save all records</h3></div>');
        click_dbp_recalculate_formula_recursive(el_id, limit_start, total, 0, 0);
    }
}

function click_dbp_recalculate_formula_recursive(el_id, limit_start, total, success_count, error_count) {
    $el = jQuery('#'+el_id);
    var formula =  $el.parents('.js-calculated-field-block').find('textarea').val();
    var field_name =  $el.parents('.js-dbp-lf-form-card').find('.js-hidden-field-name').val();
    var field_table =  $el.parents('.js-dbp-lf-form-card').find('.js-hidden-field-orgtable').val();
    console.log ("click_dbp_recalculate_formula_recursive: "+limit_start);
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        data : {'page':'dbp_list','action':'dbp_recalculate_formula','dbp_id':jQuery('#dbp_id_list').val(),'formula':formula, 'limit_start':limit_start, 'el_id': el_id,'total':total, 'field_name':field_name, 'insert_table':field_table, 'success_count':success_count, 'error_count':error_count},
        success: function(response) {
            console.log (response);
            dbp_close_sidebar_loading();
            if (response.limit_start < response.total) {
                click_dbp_recalculate_formula_recursive(response.el_id, response.limit_start, response.total,  response.success_count, response.error_count);
                $block = jQuery('<div class="dbp-view-single-box dbp-form-box-white dbp-content-margin"></div>');
                $row= jQuery('<div class="dbp-row-details"> Updated: '+ response.limit_start+"/"+ response.total+'</div>');
                $block.append($row);
            } else {
                $el = jQuery('#'+response.el_id);
                $el.removeClass('disabled');
                $block = jQuery('<div class="dbp-view-single-box dbp-form-box-white dbp-content-margin"></div>');
                $row= jQuery('<div class="dbp-row-details" style="color:#080"> '+ response.success_count+' records updated successfully.</div>');
                $block.append($row);
                if ( response.error_count > 0) {
                    $row= jQuery('<div class="dbp-row-details" style="color:#A00"> '+ response.error_count+' records gave an error</div>');
                    $block.append($row);
                }
            }
            recerror = jQuery('#dbp_dbp_content').find('.js-rec-error');
            jQuery('#dbp_dbp_content').empty().append($block);
            if (recerror.length > 0) {
                jQuery('#dbp_dbp_content').append(recerror);
            }
            if (response.error != "") {
                $resBlockError = jQuery('<div class="dbp-content-margin dbp-alert-sql-error js-rec-error"></div>').append(response.error);
                jQuery('#dbp_dbp_content').append($resBlockError); 
            }
        }
    })
}


function dbp_where_precompiled(el) {
    $parent = jQuery(el).parents('.js-dbp-lf-form-card');
    $inside = $parent.find('.js-structure-content-inside');
    $custom_val = $parent.find('.js-fields-custom-value-calc');
    if (jQuery(el).is(':checked')) {
        
        $inside.css('display','none');
        $custom_val.val( $custom_val.data('cval'));
        $parent.find('.js-fields-field-type').val('CALCULATED_FIELD').change();
    } else {
        
        $inside.css('display','block');
        $custom_val.data('cval',$custom_val.val());
    }
}


function dbp_lf_toggle_attr(el, show) {
    let $box = jQuery(el).parents('.js-dbp-lf-box-table-info');
    if (show) {
        $box.find('.js-dbp-lf-box-attributes').css('display','block');
        $box.find('.js-lf-dbp-show').css('display','none');
        $box.find('.js-lf-dbp-hide').css('display','inline-block');

    } else {
        $box.find('.js-dbp-lf-box-attributes').css('display','none');
        $box.find('.js-lf-dbp-show').css('display','inline-block');
        $box.find('.js-lf-dbp-hide').css('display','none');
    }
}

function dbp_change_lookup_id(el) {
    
    $box = jQuery(el).parents('.js-dbp-lookup-data');
   
    $box.find('.js-lookup-select-value').first().val('');
    $box.find('.js-select-fields-lookup').first().prop('disabled', true);
    $selt = $box.find('.js-lookup-select-text').first();
    $selt.empty().append(jQuery('<option></option>').text('Loading...'));
    $selt.prop('disabled',true);
    table_rif = $box.find('.js-select-fields-lookup').val();
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        data : {'page':'dbp_list','action':'dbp_get_table_columns','table_rif':table_rif, 'rif':$box.prop('id')},
        success: function(resp) {
            $box = jQuery('#'+resp.rif);
            $selv = $box.find('.js-lookup-select-value').first();
            $selt = $box.find('.js-lookup-select-text').first();
            $selt.parent().find('.js-lookup-warning').remove();
            $box.find('.js-select-fields-lookup').first().prop('disabled', false);
            $selt.prop('disabled',false);

            if (resp.pri == "") {
               $selv.val('');
               $selt.empty().css('display','none');
               $selt.parent().append('<div class="dbp-alert-warning js-lookup-warning" style="margin:0">Questa tabella non ha una chiave primaria valida!</div>');
            } else {
            
               jQuery($selv).val(resp.pri);
               jQuery($selt).empty().css('display','block');
                  
               for (x in resp.list) {
                  jQuery($selt).append(jQuery('<option></option>').prop('value', resp.list[x]).text(resp.list[x]));
               }
               jQuery($selt).val(resp.list[Object.keys(resp.list)[0]]);
            }
        },
        error:function() {
            $box.find('.js-select-fields-lookup').first().prop('disabled', false);
            alert("OPS, There was an unexpected error!");

        }
    });
}

function link_table() {
    dbp_open_sidebar_popup('link_table');
    jQuery('#dbp_dbp_title > .dbp-edit-btns').remove(); // Titolo e bottoni
    jQuery('#dbp_dbp_title').append('<div class="dbp-edit-btns"><h3>connect table</h3><div id="dbp-bnt-edit-query" class="dbp-submit" onclick="dbp_save_link()">SAVE</div></div>'); 
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        cache: false,
        data : {'page':'dbp_list', 'section':'list-form', 'action':'dbp_link_table', 'dbp_id':jQuery('#dbp_id_list').val()},
        success: function(response) {
            dbp_close_sidebar_loading();
            jQuery('#dbp_dbp_content').append(response.content); // Il contenuto
            get_link_table_column();
        }
    });
}

/**
 * Trova le colonne di una tabella quando stai facendo il merge viene richiamato ogni volta che cambi il select con l'elenco delle tabelle.
 */
function get_link_table_column() {
    jQuery('#dbp_link_filter').empty();
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        cache: false,
        data : {action:'dbp_merge_sql_query_get_fields',table:jQuery('#dbp_link_table').val()},
        success: function(response) {
            //console.log (response.all_columns);
            for (x in response.all_columns) {
                $option = jQuery('<option></option>');
                $option.text(response.all_columns[x]);
                $option.val(response.all_columns[x]);
                jQuery('#dbp_link_filter').append($option);
            }
            
        }
    });
}

function dbp_save_link() {
  // jQuery('#dbp_dbp_content').css('display','none');
   jQuery('#list_form').append(jQuery('#dbp_dbp_content'));
   dbp_submit_list_form();
}


function dbp_duplicate_field(el) {
    $box_table = jQuery(el).parents('.js-lf-container-table');
    var order = $box_table.find('.js-dbp-lf-form-card').length;
    count = 1000 + order;
    $clone = $box_table.find('.js-dbp-lf-form-card').first().clone(true);
    $clone.find('*').each(function() {
        if (this.hasAttribute('name')) {
            jQuery(this).prop('name', jQuery(this).prop('name').replace(/\[(.)*\]/i, "["+count+"]"));
        }
        if (this.hasAttribute('value')) {
            if (!jQuery(this).hasClass('js-hidden-field-table') && !jQuery(this).hasClass('js-hidden-field-orgtable') && this.nodeName != 'OPTION' && this.type != 'checkbox') {
                jQuery(this).prop('value', '');
            }
        }
        if (this.hasAttribute('checked')) {
            jQuery(this).prop('checked', 'false');
        }
        if (jQuery(this).hasClass('js-field-js-script')) {
            jQuery(this).prop('disabled', true);
            jQuery(this).prop('title', "The javascript field can be used after saving the form.");
        }
    })
    
    $clone.find('.js-title-field').empty().html('NEW FIELD NAME: <input type="text" class="dbp-input js-fields-edit-new" name="fields_edit_new['+count+']" value="fl_'+count+'">');
    jQuery(el).before($clone);

    let choose_field_type = {'Standard fields':{'VARCHAR':'Text (single line)', 'TEXT':'Text (multi line)', 'DATE':'Date', 'DATETIME':'Date time' , 'NUMERIC':'Number','DECIMAL':'Decimal (9,2)', 'SELECT':'Multiple Choice - Drop-down List (Single Answer)', 'RADIO':'Multiple Choice - Radio Buttons (Single Answer)', 'CHECKBOX':'Checkbox (Single Answer)','CHECKBOXES':'Checkboxes (Multiple Answers)'}, 'Special fields':{'READ_ONLY':'Read only','EDITOR_CODE':'Editor Code','EDITOR_TINYMCE':'Classic text editor', 'CREATION_DATE':'Record creation date', 'LAST_UPDATE_DATE':'Last update date', 'RECORD_OWNER':'Author (who created the record)', 'MODIFYING_USER':'Modifying user', 'CALCULATED_FIELD':'calculated field', 'LOOKUP':'lookup', 'UPLOAD_FIELD': 'Upload files in a custom dir'}, 'Wordpress field':{'POST':'post','USER':'user', 'MEDIA_GALLERY' : 'Media Gallery'}};
    $clone.find('.js-fields-field-type').empty();
    for (field_label in choose_field_type) {
        $optgroup = jQuery('<optgroup label="'+field_label+'"></optgroup>');
        for (option in choose_field_type[field_label]) {
            $optgroup.append('<option value="'+option+'">'+choose_field_type[field_label][option]+'</option>');
        }
        $clone.find('.js-fields-field-type').append($optgroup);
    }
    $clone.find('.js-dbp-lookup-data').prop('id', dbp_uniqid());
    $clone.find('.js-dashicon-edit').click();
    $clone.find('.js-dragable-order').val(order);
    $clone.find('.js-fields-edit-new').focus().select();
}

function dbp_form_remove_field(el) {
    $box = jQuery(el).parents('.js-dbp-lf-form-card');
    if ( $box.find('.js-fields-edit-new').length == 1) {
        $box.remove();
    } else if (confirm("You are removing the field from the database. All data saved in the field will be deleted when you save. Do you want to continue?")) {
        $box.find('.js-lf-edit-icon').css('display','none');
        $box.find('.js-dragable-handle').css('display','none');
        $box.find('.js-show-hide-select').parent().css('display','none');
        $box.find('.js-title-field').css({'text-decoration':'line-through', 'color':'#922'});
        $box.find('.js-cancel-delete').css('display','inline-block');
        $box.find('.js-delete-field').val('1');
        
        dbp_lf_show_field_example($box)
    }
}

function dbp_form_cancel_remove_field(el) {
    $box = jQuery(el).parents('.js-dbp-lf-form-card');
    $box.find('.js-lf-edit-icon').css('display','inline-block');
    $box.find('.js-dragable-handle').css('display','inline-block');
    $box.find('.js-show-hide-select').parent().css('display','inline-block');
    $box.find('.js-title-field').css({'text-decoration':'none', 'color':'#50575e'});
    $box.find('.js-lf-form-content').css('display','block');
    $box.find('.js-lf-form-field-example').css('display','none');

    $box.find('.js-cancel-delete').css('display','none');
    $box.find('.js-delete-field').val('');
}


function dbp_lf_select_onchange_toggle_field(el) {
    $box = jQuery(el).parents('.js-dbp-lf-form-card');
    $box.removeClass('dbp-form-hide-field');
    if (jQuery(el).val() == "HIDE") {
        $box.addClass('dbp-form-hide-field');
        $box.find('.js-lf-form-content').css('display','none');
        $box.find('.js-lf-form-field-example').css('display','none');
    } else {
        dbp_lf_show_field_example($box);
    }
}


function dbp_lf_show_field_example($card) {
    let type = $card.find('.js-fields-field-type').val();
    let label = $card.find('.js-fields-label').val();
    let m_options = $card.find('.js-fields-options').val();
    let m_param = {id:'u'+dbp_uniqid()};
    m_param.note = $card.find('.js-lf-fields-note').val();
    m_param.options ={
        "0" : {"value" : "0", "label" : "Not reported"}, 
        "1" : {"value" : "1", "label" : "Male"}, 
        "2" : {"value" : "2", "label": "Female"} };
    if (type == "SELECT" || type == "RADIO"  || type == "CHECKBOXES") {
        let options_temp = m_options.split("\n");
        let m_param_options = {};
        let add = true;
        for (x in options_temp) {
            console.log ("X "+x);
            let ot = options_temp[x].split(",");
            if (ot.length == 2) {
                m_param_options[x] = {"value" : ot[0].trim(), "label" : ot[1].trim()}
            } else {
                m_param_options[x] = {"value" :'', "label" : options_temp[x]}
            }
        }
        m_param.options = m_param_options;
    }
    if (type == "LOOKUP") {
        m_param.lookup_sel_txt = $card.find('.js-lookup-select-text').val();
        m_param.lookup_sel_val = $card.find('.js-lookup-select-value').val();
        m_param.lookup_id = $card.find('.js-select-fields-lookup').val();
    }
    $example_field = gp_form_field(label, '', '', type, m_param);
    $card.find('.js-lf-form-content').css('display','none');
    $card.find('.js-lf-form-field-example').css('display','flex');
    $card.find('.js-lf-form-field-example .js-dbp-example').empty().append($example_field);
    if (type == "EDITOR_CODE" || type == "EDITOR_TINYMCE" ) {
        $card.find('.js-add-tinymce-editor').height('100px');
        gp_form_add_editor( $card.find('.js-lf-form-field-example'));
        $card.find('.js-add-codemirror-editor').each(function() {
            codeMirror_ext = jQuery(this).data('cm_editor');
            codeMirror_ext.codemirror.setSize('100%', '100px');
        });
    }
    if (type == "LOOKUP" || type == "USER" || type == "POST") {
        $card.find('.js-lf-form-field-example .js-dbp-example').css({'overflow-y':'initial','overflow-x':'initial','overflow':'initial'});
    }
}


function dbp_select_change_toggle_form_title(el) {
    $row = jQuery(el).parents('.js-dbp-lf-box-table-info').find('.js-form-row-title');
    if ( jQuery(el).val() == "SHOW") {
        $row.css('display','grid');
    } else {
        $row.css('display','none');
    }
}


function change_select_module_type(el) {
    $el = jQuery(el);
    let $container_table = jQuery(el).parents('.js-lf-container-table');
    $container_table.find('.js-row-allow-create-record').css('display','block');
    if ($el.val() == "HIDE") {
        $container_table.find('.js-dragable-table').css('display','none');
        $container_table.find('.js-row-allow-create-record').css('display','none');
    } else {
        $container_table.find('.js-dragable-table').css('display','block');
    }
    if ($el.val() == "READONLY") {
        $container_table.find('.js-lf-form-field-example').css('display','none');
        $container_table.find('.js-row-allow-create-record').css('display','none');
        $container_table.find('.js-lf-form-content').css('display','none');
    } else {
        $container_table.find('.js-dbp-lf-form-card').each(function() {
            if (jQuery(this).find('.js-show-hide-select').val() == "SHOW") {
                jQuery(this).find('.js-lf-form-field-example').css('display','flex');
            }
        })
       
    }
}


function btn_lookup_test_query(el, id) {
    console.log ('btn_lookup_test_query');
    $box = jQuery(el).parents('.js-dbp-lookup-data');
    jQuery('#'+id).empty().text('Loading...');
    let data = {};
    data.table = $box.find('.js-select-fields-lookup').first().val();
    data.field_id = $box.find('.js-lookup-select-value').first().val();
    data.label = $box.find('.js-lookup-select-text').first().val();
    data.where = $box.find('.js-lookup-where').first().val();
    data.action = 'dbp_form_lookup_test_query';
    data.id = id;
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : ajaxurl,
        data : data,
        success: function(response) {
            jQuery('#'+response.id).empty();
            if (response.error != "") {
                let war = jQuery('<span class="dbp-alert-danger" style="padding:0"></span>');
                war.text(response.error);
                jQuery('#'+response.id).append(war);
            } else {
                let ok = jQuery('<span class="dbp-alert-info" style="padding:0">The query returns '+response.count+' rows</span>');
                jQuery('#'+response.id).append(ok);
            }
        }
    });
}