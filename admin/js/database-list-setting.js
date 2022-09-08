jQuery(document).ready(function () {
    dbp_update_css_table();
    dbp_update_position_pagination();
    dbp_update_column_sort();
    dbp_update_search();
    set_codeEditor('editor_else');
    set_codeEditor('editor_no_result');
    set_codeEditor('editor_detail_template');
    set_codeEditor('editor_content');
    set_codeEditor('editor_content_header');
    set_codeEditor('editor_content_footer');
    editor_else = jQuery("#editor_else").data('cm_editor');
    editor_else.codemirror.setSize('100%', '300px');

    editor_no_result = jQuery("#editor_no_result").data('cm_editor');
    editor_no_result.codemirror.setSize('100%', '100px');

    editor_detail_template = jQuery("#editor_detail_template").data('cm_editor');
    editor_detail_template.codemirror.setSize('100%', '350px');

    editor_content = jQuery("#editor_content").data('cm_editor');
    editor_content.codemirror.setSize('100%', '350px');

    editor_content_header = jQuery("#editor_content_header").data('cm_editor');
    editor_content_header.codemirror.setSize('100%', '150px');

    editor_content_footer = jQuery("#editor_content_footer").data('cm_editor');
    editor_content_footer.codemirror.setSize('100%', '150px');

    dbp_list_setting(jQuery('#dbp_choose_type_frontend_view'));
    dbp_checkif();
    detail_toggle();
    select_editor_table_update();
});
/**
 * Aggiorno gli stili per la tabella
 */
function dbp_update_css_table() {
    let colors = ['blue', 'green', 'red','pink','yellow','gray']
    let color = jQuery('#dbp_css_color').val();
    for(x in colors) {
        jQuery('.dbp-pagination').removeClass('dbp-pagination-'+colors[x]);
        jQuery('#dbp_test_table').removeClass('dbp-table-'+colors[x]);
        jQuery('.dbp-search-button').removeClass('dbp-search-button-'+colors[x]);
    }
    jQuery('#dbp_test_table').addClass('dbp-table-'+color);
    jQuery('.dbp-pagination').addClass('dbp-pagination-'+color);
    jQuery('.dbp-search-button').addClass('dbp-search-button-'+color);

    let size = ['xsmall', 'small','normal','big'];
    let curr_size = jQuery('#dbp_table_size').val();
    for(x in size) {
        jQuery('#dbp_test_table').removeClass('dbp-block-table-'+size[x]);
    }
    if (curr_size == "") {
        curr_size = "normal";
    }
    jQuery('#dbp_test_table').addClass('dbp-block-table-'+curr_size);
}

/**
 * Aggiorno la preview della tabella con la paginazione
 */
 function dbp_update_position_pagination() {
    let val = jQuery('#dbp_position_pagination').val();
    let val_style = jQuery('#dbp_pagination_style').val();
    jQuery('#dbp_pag_up').css('display','none');
    jQuery('#dbp_pag2_up').css('display','none');
    jQuery('#dbp_pag_down').css('display','none');
    jQuery('#dbp_pag2_down').css('display','none');
    console.log ("dbp_update_position_pagination val: "+val);
    if (val == "") {
        jQuery('#dbp_pagination_style_row').css('display','none');
    } else {
        jQuery('#dbp_pagination_style_row').css('display','block');
      
        if (val == "down") {
            if (val_style == "select") {
                jQuery('#dbp_pag_down').css('display','block');
            } else {

                jQuery('#dbp_pag2_down').css('display','block');
            }
        }
        if (val == "up") {
            if (val_style == "select") {
                jQuery('#dbp_pag_up').css('display','block');
            } else {

                jQuery('#dbp_pag2_up').css('display','block');
            }
        }
        if (val == "both") {
            if (val_style == "select") {
                jQuery('#dbp_pag_down').css('display','block');
                jQuery('#dbp_pag_up').css('display','block');
            } else {
                jQuery('#dbp_pag2_down').css('display','block');
                jQuery('#dbp_pag2_up').css('display','block');
            }
        }
    }
 }

 /**
  * Aggiorno la preview se le colonne si possono ordinare o no
  */
function dbp_update_column_sort() {
    let val = jQuery('#dbp_table_sort').val();
    if (val == "") {
        jQuery('.js-no-order').css('display','inline-block');
        jQuery('.js-order-link').css('display','none');
    } else {
        jQuery('.js-no-order').css('display','none');
        jQuery('.js-order-link').css('display','inline-block');
    }
}


 /**
  * Aggiorno la preview se c'è la ricerca
  */
  function dbp_update_search() {
    let val = jQuery('#dbp_table_search').val();
    if (val == "") {
        jQuery('#dbp_preview_table_search').css('display','none');
       
    } else {
        jQuery('#dbp_preview_table_search').css('display','block');
    }
}

/**
 * Cambio a seconda se è una tabella o un editor le opzioni successive
 */
function dbp_list_setting(el) {
    let val = jQuery(el).val();
    if (val == "TABLE_BASE") {
        jQuery('#frontend_view_table').css('display','block');
        jQuery('#frontend_view_editor').css('display','none');
        jQuery('#block_if').css('display','block');
        jQuery('#no_result').css('display','block');
    } else {
        jQuery('#frontend_view_table').css('display','none');
        jQuery('#frontend_view_editor').css('display','block');
        jQuery('#block_if').css('display','none');
        jQuery('#no_result').css('display','none');
    }
}

/**
 * Mostra nasconde i checkbox
 */
function dbp_checkif() {
    if (jQuery('#checkbox_show_if').is(':checked')) {
        jQuery('#block_else').css('display','block');
        jQuery('#dbp_textarea_if').css('display','inline-block');
    } else {
        jQuery('#block_else').css('display','none');
        jQuery('#dbp_textarea_if').css('display','none');
    }
}
/**
 * Invia il form
 */
function dbp_submit_list_setting() {
    code =  jQuery('#editor_else').data('cm_editor');
    jQuery('#editor_else').value = code.codemirror.getValue();

    code =  jQuery('#editor_no_result').data('cm_editor');
    jQuery('#editor_no_result').value = code.codemirror.getValue();

    code =  jQuery('#editor_detail_template').data('cm_editor');
    jQuery('#editor_detail_template').value = code.codemirror.getValue();
    
    code = jQuery('#editor_content').data('cm_editor');
    jQuery('#editor_content').value = code.codemirror.getValue();

    code = jQuery('#editor_content_header').data('cm_editor');
    jQuery('#editor_content_header').value = code.codemirror.getValue();

    code = jQuery('#editor_content_footer').data('cm_editor');
    jQuery('#editor_content_footer').value = code.codemirror.getValue();

    jQuery('#list_setting_form').submit();
}


/**
 * EDITOR PINACODE
 *  set_codeEditor('textarea_id');
 */

 function completeAfter(cm, pred) {
    var cur = cm.getCursor();
    if (!pred || pred()) setTimeout(function () {
        if (!cm.state.completionActive)
            cm.showHint({ hint: wp.CodeMirror.hint.pinacode });
    }, 100);
    return wp.CodeMirror.Pass;
}


function set_codeEditor(id) {
    //console.log (CodeMirror);
    var codeMirror_ext = wp.codeEditor.initialize(document.getElementById(id), {
        'codemirror':{
            mode: "htmlmixed",
            lineNumbers: true,
            extraKeys: {
                "'['": completeAfter,
                "'='": completeAfter,
                "' '": completeAfter,
                "Ctrl-Space": "autocomplete"
            }
        }
        
    });
    jQuery("#"+id).data('cm_editor', codeMirror_ext);
}


var all_list = ['[^POST', '[^IMAGE', '[^NOW', '[^USER', '[^IS_PAGE_AUTHOR]', '[^IS_PAGE_ARCHIVE]', '[^IS_PAGE_TAG]', '[^IS_PAGE_DATE]', '[^IS_PAGE_TAX]', '[^IS_USER_LOGGED_IN]', '[^LINK', '[^IF', '[^IF 1 == 1] ok [^ENDIF]', '[^IF 1 == 2] no [^ELSE] ok [^ENDIF]', '[^FOR EACH=] [^ENDFOR]', '[^WHILE [%x set+=1] < 10] [%x] [^ENDWHILE]', '[^BREAK 1==1]', '[^BLOCK MyVar] Foo [^ENDBLOCK][% MyVar]', '[^SET ', '[^MATH', '[^RETURN', '[// //]', '[: :]'];
var all_attributes = ['DATE-FORMAT=', 'DATE-MODIFY=', 'TIMESTAMP', 'LAST-DAY', 'UPPER', 'LOWER', 'UCFIRST', 'STRIP-COMMENT', 'STRIP-TAGS', 'TRIM', 'GET', 'PRINT=', 'NO_PRINT', 'NL2BR', 'HTMLENTITIES', 'LEFT=', 'RIGHT=', 'TRIM_WORDS', 'SANITIZE', 'SEARCH=', 'REPLACE=', 'LENGTH', 'GET=', 'SEP', 'QSEP', 'IF=', 'MEAN', 'COUNT', 'ORDER_REVERSE', 'IS_STRING', 'IS_OBJECT', 'IS_DATE', 'ZERO','EMPTY','ONE','PLURAL','NEGATIVE'];
var adding_attributes =[];
adding_attributes['[^POST'] = ['TYPE=', 'META_QUERY=', 'ID=', 'CAT=', '!CAT=', 'AUTHOR=', 'SLUG=', 'TAG=', 'PARENT_ID=', 'LIMIT=', 'OFFSET=', 'ORDER=', 'ASC', 'DESC', 'YEAR=', 'MONTH=', 'WEEK=', 'DAY=', 'FIRST=', 'LAST=', 'READ_MORE=', 'IMAGE=', 'LIGHT_LOAD', '[%ITEM.ID]', '[%ITEM.AUTHOR]', '[%ITEM.AUTHOR_ID]', '[%ITEM.AUTHOR_NAME]', '[%ITEM.AUTHOR_ROLES]', '[%ITEM.AUTHOR_EMAIL]', '[%ITEM.AUTHOR_LINK]', '[%ITEM.DATE]', '[%ITEM.CONTENT]', '[%ITEM.TITLE]', '[%ITEM.TITLE_LINK]', '[%ITEM.PERMALINK]', '[%ITEM.GUID]', '[%ITEM.EXCERPT]', '[%ITEM.STATUS]', '[%ITEM.COMMENT_STATUS]', '[%ITEM.NAME]', '[%ITEM.MODIFIED]', '[%ITEM.PARENT]', '[%ITEM.MENU_ORDER]', '[%ITEM.TYPE]', '[%ITEM.MIME_TYPE]', '[%ITEM.COMMENT_COUNT]', '[%ITEM.FILTER]', '[%ITEM.READ_MORE_LINK]', '[%ITEM.IMAGE]', '[%ITEM.IMAGE_LINK]', '[%ITEM.IMAGE_ID]'];
adding_attributes['[^IMAGE'] = ['META_QUERY=', 'ID=', 'CAT=', '!CAT=', 'AUTHOR=', 'SLUG=', 'TAG=', 'PARENT_ID=', 'LIMIT=', 'OFFSET=', 'ORDER=', 'ASC', 'DESC', 'YEAR=', 'MONTH=', 'WEEK=', 'DAY=', 'FIRST', 'LAST', 'READ_MORE=', 'IMAGE=', 'LIGHT_LOAD', '[%ITEM.ID]', '[%ITEM.AUTHOR]', '[%ITEM.AUTHOR_ID]', '[%ITEM.AUTHOR_NAME]', '[%ITEM.AUTHOR_ROLES]', '[%ITEM.AUTHOR_EMAIL]', '[%ITEM.AUTHOR_LINK]', '[%ITEM.DATE]', '[%ITEM.CONTENT]', '[%ITEM.TITLE]', '[%ITEM.TITLE_LINK]', '[%ITEM.PERMALINK]', '[%ITEM.GUID]', '[%ITEM.EXCERPT]', '[%ITEM.STATUS]', '[%ITEM.COMMENT_STATUS]', '[%ITEM.NAME]', '[%ITEM.MODIFIED]', '[%ITEM.PARENT]', '[%ITEM.MENU_ORDER]', '[%ITEM.TYPE]', '[%ITEM.MIME_TYPE]', '[%ITEM.COMMENT_COUNT]', '[%ITEM.FILTER]', '[%ITEM.READ_MORE_LINK]', '[%ITEM.IMAGE]', '[%ITEM.IMAGE_LINK]', '[%ITEM.IMAGE_ID]'];


wp.CodeMirror.registerHelper("hint", "pinacode", function (editor, options) {
    var cur = editor.getCursor(), curLine = editor.getLine(cur.line);
    var start = cur.ch , end = start , range_start = cur.ch;
    while (end <= curLine.length && curLine.charAt(end) != " " && curLine.charAt(end) != "." && curLine.charAt(end) != "[" && curLine.charAt(end) != "]" && curLine.charAt(end) != "=") ++end;
    while (start > 0 && curLine.charAt(start - 1) != " " && curLine.charAt(start - 1) != "." && curLine.charAt(start - 1) != "=") --start;
    //console.log(cur.ch+ " START "+start+" END "+end);
    var list = [];
    var close = 0, end_close = 0;
    var open_tag = "";
    range_start = start;
    while (range_start) {
        range_start--;
        if (curLine.charAt(range_start) == "]") {
            close++;
        } else if (curLine.charAt(range_start) == "[") {
            close--;
        }
        if (close < 0) {
            end_close = range_start;
            while (end_close < curLine.length && curLine.charAt(end_close) != " ") ++end_close;
            open_tag = curLine.slice(range_start, end_close);
            break;
        }
    }

    var curWord = start != end && curLine.slice(start, end);
    console.log("OPEN TAG "+open_tag);
    if (curWord) {
        if (open_tag != "") {
            var open_tag_upper = open_tag.toUpperCase();
            if (adding_attributes[open_tag_upper]) {
              
                for (var k in adding_attributes[open_tag_upper]) {
                //    console.log(adding_attributes[open_tag_upper][k].substring(0, curWord.length).toLowerCase()+" == "+curWord.toLowerCase());
                    if (adding_attributes[open_tag_upper][k].substring(0, curWord.length).toLowerCase() == curWord.toLowerCase()) {
                        list.push(adding_attributes[open_tag_upper][k]);
                    }
                }
            }
            for (var k in all_attributes) {
                if (all_attributes[k].substring(0, curWord.length).toLowerCase() == curWord.toLowerCase()) {
                    list.push(all_attributes[k]);
                }
            }
        }
        for (var k in all_list) {
            if (all_list[k].substring(0, curWord.length).toLowerCase() == curWord.toLowerCase()) {
                list.push(all_list[k]);
            }
        }
    }

    return { list: list, from: wp.CodeMirror.Pos(cur.line, start), to: wp.CodeMirror.Pos(cur.line, end) };
});


function detail_toggle() {
    jQuery('#detail_text').css('display','none');
    jQuery('#detail_info_php').css('display','none');
    jQuery('#detail_info_table').css('display','none');
    if (jQuery('#select_detail_toggle').val() == "PHP") {
        jQuery('#detail_info_php').css('display','block');
    } else if (jQuery('#select_detail_toggle').val() == "CUSTOM") {
        jQuery('#detail_text').css('display','block');
    } else {
        jQuery('#detail_info_table').css('display','block');
    }
}


function select_editor_table_update() {
    if (jQuery('#select_editor_table_upldate').val() == "none") {
        jQuery('#dbp_pagination_style_row_2').css('display','none');
    } else {
        jQuery('#dbp_pagination_style_row_2').css('display','block');
    }
}