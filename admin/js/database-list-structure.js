jQuery(document).ready(function ($) {
    //aggiungo la possibilità di fare il sort sulla creazione del nuovo db
    jQuery('.js-dragable-table').sortable({
        items: '.js-dragable-tr',
        opacity: 0.5,
        cursor: 'move',
        axis: 'y',
        handle: ".js-dragable-handle"
    });

    jQuery('.js-toggle-row').change();
    jQuery('.js-type-fields').change();
    jQuery('.js-structure-toggle').click();
});

/**
 * Il bottone per creare nuove righe della tabelle per la creazione delle tabelle mysql
 */
 function dbt_list_structure_add_row (el) {
    $original =  jQuery('#clone_master');
    $clone = $original.clone(true);
    jQuery('.js-dragable-table').append($clone);
    $clone.css('display','block');
    $clone.removeAttr('id').addClass('js-dragable-tr dbt-structure-card js-dbt-structure-card');
    jQuery('.js-dragable-table').sortable('refresh');
    $clone.find('.js-structure-toggle').click();
 }
/**
 * Invia la form, ma prima imposta i campi da riordinare
 */
 function dbt_submit_list_structure() {
   var count_list = 0;
   jQuery('.js-dragable-order').each(function() {
      jQuery(this).val(count_list);
      count_list++;
   })
   jQuery('#list_structure').submit();
 }

 /**
  * Elimina una riga
  * @param DOM el 
  */
function dbt_list_structure_delete_row(el) {
   jQuery(el).parents('.js-dbt-structure-card').remove();
}

// Questa funzione deve rimanere anche se vuota
function dbt_change_toggle(el) {

}

/**
 * Vede se è un CUSTOM TYPE OPPURE NO
 * @param DOM el 
 */
function dbt_change_custom_type(el) {
   
   let $tr = jQuery(el).parents('.js-dbt-structure-card');
   $tr.find('.js-dbt-params-column').css('display','none');
   $tr.find('.js-dbt-params-column .dbt-form-label').css('display','none');
   if (el._first_change === 'no') {
      $tr.find('.js-input-parasm-custom').val('');
   }
   el._first_change = 'no';
   $tr.find('.js-lookup-params').css('display','none');
   if (jQuery(el).val() == "CUSTOM") {
      jQuery(el).css('display', 'none');
      $tr.find('.js-type-custom-code').css('display', 'inline-block');
      $tr.find('.js-textarea-btn-cancel').css('display', 'inline-block');
   } else {
      jQuery(el).css('display', 'block');
      $tr.find('.js-type-custom-code').css('display', 'none');
      $tr.find('.js-textarea-btn-cancel').css('display', 'none');
      if (jQuery(el).val() == 'DATE' || jQuery(el).val() == 'DATETIME') {
         $tr.find('.js-dbt-params-column').css('display','block');
          $tr.find('.js-dbt-params-date').css('display','inline-block');
      }
      if ( jQuery(el).val() == 'LINK') {
         $tr.find('.js-dbt-params-column').css('display','block');
          $tr.find('.js-dbt-params-link').css('display','inline-block');
      }
      if ( jQuery(el).val() == 'USER') {
         $tr.find('.js-dbt-params-column').css('display','block');
          $tr.find('.js-dbt-params-user').css('display','inline-block');
      }
      if ( jQuery(el).val() == 'POST') {
         $tr.find('.js-dbt-params-column').css('display','block');
          $tr.find('.js-dbt-params-post').css('display','inline-block');
      }
      if (jQuery(el).val() == 'TEXT') {
          $tr.find('.js-dbt-params-column').css('display','block');
          $tr.find('.js-dbt-params-text').css('display','inline-block');
      }
      if (jQuery(el).val() == 'LOOKUP') {
         $tr.find('.js-lookup-params').css('display','block');
         $tr.find('.js-select-fields-lookup').change();
      }
   }
}

/**
 * Mostra/Nasconde gli attributi di una colonna
 */
function dbt_structure_toggle(el) {
   let $tr = jQuery(el).parents('.js-dbt-structure-card');
   let $box = $tr.find('.js-structure-content');
   if ($box.css('display') == "none") {
      $box.css('display','block');
   } else {
      $box.css('display','none');
   }
}

/**
 * DA custom type a un tipo predefinito
 * @param DOM el 
 */
function dbt_custom_cancel(el) {
   let $td = jQuery(el).parents('.js-form-row-custom-field');
   let $tr = jQuery(el).parents('.js-dbt-structure-card');
   let def = $tr.find('.js-type-td').data('type');
   if (def == 'CUSTOM') {
      def = "";
   }
   $td.find('.js-type-fields').css('display', 'block').val(def);
   $td.find('.js-type-custom-code').css('display', 'none');
   $td.find('.js-textarea-btn-cancel').css('display', 'none');
}


/**
 * Lookup
 */
 function dbt_list_change_lookup_id(el) {
   $box = jQuery(el).parents('.js-lookup-params');
   val_dbt_id = $box.find('.js-select-fields-lookup').val();
   jQuery.ajax({
      type : "post",
      dataType : "json",
      url : ajaxurl,
      data : {'page':'dbt_list','action':'dbt_get_list_columns','dbt_id':val_dbt_id, 'rif':$box.prop('id'),'searchable':1},
      success: function(resp) {
         $box = jQuery('#'+resp.rif);
         $selv = $box.find('.js-lookup-select-value').first();
         $selt = $box.find('.js-lookup-select-text').first();
         jQuery($selv).val(resp.pri);
         jQuery($selt).empty();
          
         for (x in resp.list) {
           
            jQuery($selt).append(jQuery('<option></option>').prop('value', x).text(resp.list[x]));
         }
         jQuery($selv).val(Object.keys(resp.list)[0]);
         jQuery($selt).val(Object.keys(resp.list)[0]);
      }
   });
 }



/**
 * Testa una formula rispetto alla tabella
 * copiate da admin\js\database-list-form.js
 * @param {DOM} el 
 */
 function click_dbt_test_formula(el) {
   if (jQuery(el).hasClass('disabled')) return;
   jQuery(el).addClass('disabled');
   var formula = jQuery(el).parents('.js-calculated-field-block').find('textarea').val();
   var test_row = jQuery(el).parents('.js-calculated-field-block').find('.js-choose-test-row').val();
   dbt_open_sidebar_popup('test_formula');
   jQuery('#dbt_dbp_title > .dbt-edit-btns').remove();
   
   jQuery('#dbt_dbp_title').append('<div class="dbt-edit-btns"><h3>Test Formula</h3></div>');
   jQuery.ajax({
      type : "post",
      dataType : "json",
      url : ajaxurl,
      data : {'page':'dbt_list','action':'dbt_test_formula','dbt_id':jQuery('#dbt_id_list').val(),'formula':formula, 'row':test_row},
      success: function(response) {
        // console.log (response);
         dbt_close_sidebar_loading();
         // jQuery('#dbt_dbp_title > .dbt-edit-btns').remove(); // Titolo e bottoni
         $block1 = jQuery('<div class="dbt-view-single-box dbt-form-box-white dbt-content-margin "></div>');
         $formula = jQuery('<div class="dbt-xmp"></div>').text(response.formula);
         $typeof = jQuery('<div class="dbt-xmp"></div>').text(response.typeof);
         $row_formula = jQuery('<div class="dbt-row-details"></div>');
         $row_response = jQuery('<div class="dbt-row-details"></div>');
         $row_typeof = jQuery('<div class="dbt-row-details"></div>');
         console.log ('response.response');
         console.log (response.response);
         if (typeof(response.response) == "object") {
            $resBlock = jQuery('<div class="dbt-sidebar-test-formula-params"></div>');
            test_formula_show_data('', response.response, $resBlock);
            $row_response.append('<div class="dbt-label-detail">Response:</div>').append($resBlock);
            response.warning.push('The result must be a text string and not an object or array');
         } else {
            $response = jQuery('<div class="dbt-xmp"></div>').text(response.response);
            $row_response.append('<div class="dbt-label-detail">Response:</div>').append($response);
         }

         $row_formula.append('<div class="dbt-label-detail">Test Formula:</div>').append($formula);
         $row_typeof.append('<div class="dbt-label-detail">Type of:</div>').append($typeof);
         $block1.append($row_response).append($row_typeof).append($row_formula);

         jQuery('#dbt_dbp_content').append($block1 );
       
         if (response.error.length > 0) {
            $resBlockError = jQuery('<div class="dbt-content-margin dtf-alert-sql-error"></div>');
            for (x in response.error) {
               $resBlockError.append ('<p>'+response.error[x]+'</p>');
            }
            jQuery('#dbt_dbp_content').append($resBlockError);
         }
         if (response.warning.length > 0) {
            $resBlockWarning = jQuery('<div class="dbt-content-margin dtf-alert-warning"></div>');
            for (x in response.warning) {
               $resBlockWarning.append ('<p>'+response.warning[x]+'</p>');
            }
            jQuery('#dbt_dbp_content').append($resBlockWarning);
         }

         if (response.notice.length > 0) {
            $resBlockNotice = jQuery('<div class="dbt-content-margin dtf-alert-info"></div>');
            for (x in response.notice) {
               $resBlockNotice.append ('<p>'+response.notice[x]+'</p>');
            }
            jQuery('#dbt_dbp_content').append($resBlockNotice);
         }
  
         $block2 = jQuery('<div class="dbt-sidebar-test-formula-params dbt-content-margin"></div>');
         test_formula_show_data('', response.pinacode_data, $block2);
         jQuery('#dbt_dbp_content').append('<h3 class="dbt-content-margin">Template Engine Variables</h3>').append($block2);
        
         jQuery('.js-test-formula').removeClass('disabled');
      }, error : function(xhr, status, error) {
         dbt_close_sidebar_loading();
         $resBlockNotice = jQuery('<div class="dbt-content-margin dtf-alert-sql-error"></div>');
         $resBlockNotice.append ('<p>there was an unexpected error</p>');
         jQuery('#dbt_dbp_content').append($resBlockNotice);
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
         $var = jQuery('<div class="dbt-xmp"></div>').text(data[x]+" "+typeof(data[x]));
         $row = jQuery('<div class="dbt-row-details"></div>');
         if (left_name == "") {
            var_name = x;
         } else {
            var_name = left_name+"."+x;
         }
         $row.append('<div class="dbt-label-detail">[%'+var_name+']</div>').append($var);
         $block.append($row);
      }
   }
  
}