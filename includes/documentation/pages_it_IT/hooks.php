<?php 
namespace DatabasePress;
if (!defined('WPINC')) die;
?>

<div class="dbp-content-table dbp-docs-content  js-id-dbp-content" >

    <hr>
    <h2 class="dbp-h2"> apply_filters('<b>dbp_frontend_build_custom_link</b>', $custom_link, $list_id, $primary_values, $col_value, $col_key);</h2>
    Nel frontend nella creazione dei link personalizzati per aprire il dettaglio dei risultati.
    <hr>
    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$custom_link</b>
                <br>(string) il link che verrà visualizzato
            </li>
            <li><b>$list_id</b>
                <br>(int) L'id della lista
            </li>
            <li><b>$primary_values</b>
                <br>(Array) 
            </li>
            <li><b>$col_value</b>
                <br>(string) 
            </li>
            <li><b>$col_key</b>
                <br>(string) 
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">./includes/dbp-functions-items-setting.php</div>

    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        L'esempio mostra il caricamento dei dati in un div personalizzato. Questo è possibile grazie alla funzione javascript setup_dbp_load_ajax_custom_div(el_container)
        <pre class="dbp-code">
        /**
        * Nella tabella del frontend:  Nell'HTML dove è stato messo lo shortcode [dbp_list id=xx] inserisco un html personalizzato '<div id="dbp_div_target_link"></div>'
        */
        function load_in_custom_box($custom_link, $list_id, $primary_values, $col_value, $col_key) {
            return '<a class="js-dbp-load-ajax-custom-div" href="'.esc_url(add_query_arg($primary_values, admin_url('admin-ajax.php'))).'" target="_blank">'
            .strip_tags($col_value).'</a>';

        }
        add_filter('dbp_frontend_build_custom_link', 'load_in_custom_box', 10, 5);
        </pre>
    </div>


    <hr>
    <h2 class="dbp-h2">apply_filters('<b>dbp_frontend_total_items</b>', $total_items_text, $list_id, $total_items,  $limit, $curr_page, $pages );</h2>
    Nelle tabelle modifica la scritta del totale degli elementi
    <hr>
    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$total_items_text</b>
                <br>(string) Il testo che mostra
            </li>
            <li><b>$list_id</b>
                <br>(int) L'id della lista
            </li>
            <li><b>$total_items</b>
                <br>(int) Il numero totale degli elementi
            </li>
            <li><b>$limit</b>
                <br>(int) Il numero di elementi per pagina 
            </li>
            <li><b>$curr_page</b>
                <br>(int) La pagina corrente
            </li>
            <li><b>$page</b>
                <br>(int) Il numero di pagine totali
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">./includes/dbp-html-search-frontend.php</div>

    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        <pre class="dbp-code">
        /**
        * Nella tabella del frontend: Mostra un testo alternativo nel totale degli elementi mostrati di una tabella
        */
        function dbp_total_items( $total_items_text, $list_id, $total_items,  $limit, $curr_page, $pages) {
            $min = (($curr_page -1) * $limit) + 1;
            $max = (($curr_page) * $limit);
            return sprintf( "Showing %s to %s of %s entries", $min, $max, $total_items);
        }

        add_filter('dbp_frontend_total_items', 'dbp_total_items', 10, 6);
        </pre>
    </div>





    <hr>
    <h2 class="dbp-h2">apply_filters('<b>dbp_frontend_table_thead</b>', header_text, $list_id, $array_thead);</h2>
    Nel frontend stampa i titoli della tabella. 
    <hr>
    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$header_text</b>
                <br>(string) Il testo che mostra
            </li>
            <li><b>$list_id</b>
                <br>(int) L'id della lista
            </li>
            <li><b>$array_thead</b>
                <br>(Array) I dati per stampare le colonne
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">./includes/dbp-html-table-frontend.php</div>

    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        <pre class="dbp-code">
        /**
        * Nascondo l'header
        */
        function dbp_total_items($header_text, $list_id, $array_thead) {
            return '';
        }

        add_filter('dbp_frontend_table_thead', 'dbp_hide_thead', 10, 3);
        </pre>
    </div>

    

    <hr>
    <h2 class="dbp-h2">apply_filters('<b>dbp_table_status</b>', $status, $table);</h2>
    Cambia lo stato di una tabella (DRAFT|PUBLISH|CLOSE)
    <hr>
    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$status</b>
                <br>(string) Lo stato attivo della tabella
            </li>
            <li><b>$table</b>
                <br>(string) Il nome della tabella
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">./includes/dbp-functions.php</div>

    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        <pre class="dbp-code">
        /**
        * impedisco la modifica della tabella post
        */
        function dbp_lock_post_table($status, $table) {
            global $wpdb; 
            // $status = DRAFT|PUBLISH|CLOSE
            if ($table == $wpdb->prefix."posts") {
                $status = 'CLOSE';
            }
            return $status;
        }

        add_filter('dbp_table_status', 'dbp_lock_post_table', 10, 2);
        </pre>
    </div>


    <hr>
    <h2 class="dbp-h2">apply_filters('<b>dbp_save_data</b>', $query_to_execute, $list_id, $origin)</h2>
    Modifica i dati che si stanno per salvare nel database
    <hr>
    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$query_to_execute</b>
                <br>(array) di record che stanno per essere inseriti o modificati nel database
            </li>
            <li><b>$list_id</b>
                <br>(int) L'id della lista
            </li>
            <li><b>$origin</b>
                <br>(string) Definisce da dove vengono immessi i dati
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">./includes/dbp-list-functions.php</div>

    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        <pre class="dbp-code">
    /**
    * Salvo un post e ne modifico il titolo dal filtro
    */
    function test_dbp($status, $table) {
        $list_id = '[number_of_list_id]'; // è una lista che carica i dati dei post;
        $row = new StdClass();
        $row->post_title = "new record";
        $ris = DatabasePress\Dbp::save_data($dbpid, $row);
    }
    add_action( 'init', 'test_dbp' );
    
    function dbp_action_in_save_data($query_to_execute, $list_id, $where) {
        // cambio il titolo
        $query_to_execute[0]['sql_to_save']['post_title'] = "Change the title";
        return $query_to_execute;
    }
    add_filter('dbp_save_data', 'dbp_action_in_save_data', 10, 3);
        </pre>
    </div>


    <h2 class="dbp-h2">apply_filters( '<b>dbp_frontend_search</b>', $field_name, $request_field_name, $list_id);</h2>
    Permette di modificare la form di ricerca nel frontend
    <hr>
    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$field_name</b>
                <br>(string) il nome della colonna che si sta ricercando oppure search se è il campo di ricerca classico.
            </li>
            <li><b>$request_field_name</b>
                <br>(string) Il nome del campo da inviare nella form.
            </li>
            <li><b>$list_id</b>
                <br>(int) L'id della lista
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">./includes/dbp-render-list.php</div>
    
    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        <pre class="dbp-code">/**
 * change search form example 
 */
function dbp_frontend_field_search_fn($field_name, $input_field_name, $list_id) {
	if ($field_name == &quot;search&quot;) {
		?&gt;
		&lt;div class=&quot;dbp-search-row&quot;&gt;
			&lt;label&gt;&lt;span class=&quot;dbp-search-label&quot;&gt;Search Field&lt;/span&gt;	
			&lt;?php DatabasePress\dbp_fn::html_select(['0'=&gt;'No','1'=&gt;'Yes'], true, 'class=&quot;dbp-search-input js-dbp-search-input&quot; name=&quot;'. esc_attr($input_field_name).'&quot;', @$_REQUEST[$input_field_name]); ?&gt;
			&lt;/label&gt;
			&lt;div class=&quot;dbp-search-button dbp-search-button-blue&quot; onclick=&quot;dbp_submit_simple_search(this)&quot;&gt;&lt;?php _e('Search', 'database_press'); ?&gt;&lt;/div&gt;
		&lt;/div&gt;
		&lt;?php 
		// to prevent input printing
		return '';
	} else {
		// print the search field normally
		return $field_name;
	}
}
add_filter('dbp_frontend_search', 'dbp_frontend_field_search_fn', 10, 3);</pre>
</div>



    <h2 class="dbp-h2">apply_filters('<b>dbp_frontend_get_list</b>', $html, $list_id);</h2>
    Permette di ridisegnare la visualizzazione di una lista in php.
        <hr>
        <h4 class="dbp-h4">Parameters</h4>
        <div class="dbp-help-p">
            <ul>
                <li><b>$html</b>
                    <br>(string)
                </li>
                <li><b>$list_id</b>
                    <br>(int) L'id della lista
                </li>
            </ul>
            </div>
        <h4  class="dbp-h4">Source</h4>
        <div class="dbp-help-p">./includes/dbp-render-list.php</div>
        
        <h4 class="dbp-h4">Example</h4>
        <div class="dbp-help-p">
            <pre class="dbp-code">function my_custom_list($render_data, $list_id) {
	if ($list_id != 6) return ''; // 6 is an example
    ob_start();
    $list =  DatabasePress\Dbp::render($list_id, 'ajax'); 
    $list->table(); 
    $list->pagination();
    $list->end(); // Required!
    return ob_get_clean();
} 
add_filter('dbp_frontend_get_list', 'my_custom_list', 10, 2);</pre>
    </div>



    <h2 class="dbp-h2">apply_filters( 'pinacode_attribute_tmpl_'.$param, $gvalue, $param, $shortcode_obj);</h2>
    Se viene chiamato l'attributo template è possibile filtrare il parametro passato
    <hr>
    <p> I parametri accettati sono: $param, $gvalue, $count_for</p>


    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$gvalue</b>
                <br>(object) Lo stato attivo della tabella
            </li>
            <li><b>$param</b>
                <br>(string)
            </li>
            <li><b>$shortcode_obj</b>
                <br>(object)
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">./includes/pinacode/pina-attributes.php</div>
    
    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        <pre class="dbp-code">
    add_filter('pinacode_attribute_tmpl', 'bold_pinacode_attribute_tmpl', 10, 3 );
    function bold_pinacode_attribute_tmpl( $param, $gvalue, $count_for ) {
    if ($param == "bold") {
        $item = PinaCode::get_var('item');
        $html = [];
        foreach ($item as $k=>$v) {
        $html[] = "<b>".$k."</b>";
        }
        $param = implode("", $html);
    }
    return $param;
    }
        </pre>
    </div>

    <h2 class="dbp-h2">apply_filters( '<b>dbp_items_add_action</b>, $btns, $dbp_id, $count_unique_id);</h2>
    Le azioni che si possono eseguire su un record (edit | view | delete).
    <hr>
    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$btns</b>
                <br>(array) The focus of the table
            </li>
            <li><b>$dbp_id</b>
                <br>(integer)
            </li>
            <li><b>$count_unique_id</b>
                <br>(integer)
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">./includes/pinacode/dbp-functions.php</div>
    
    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        <pre class="dbp-code">add_action( 'dbp_items_add_action', 'dbp_items_add_action', 10, 3 );
function dbp_items_add_action($btns, $dbp_id, $count_unique_id) {
	if ( $dbp_id == 'xxx') {
        $alert = "alert('ids: '+JSON.stringify(dbp_tb_id[".$count_unique_id."]))";
		$btns[] = '<span class="dbp-submit-style-link" onclick="'.$alert.'">Alert</span></div>';
	}
	return $btns;
}</pre>
    </div>

    <h2 class="dbp-h2">do_action( '<b>dbp_admin_page_list_after_title</b>, $dbp_id, $total_items);</h2>
    Chiama la funzione dopo il titolo nelle liste pubblicate su una nuova voce di menu.
    <hr>
    <h4 class="dbp-h4">Parameters</h4>
    <div class="dbp-help-p">
        <ul>
            <li><b>$dbp_id</b>
                <br>(integer) 
            </li>
            <li><b>$total_items</b>
                <br>(integer)
            </li>
        </ul>
        </div>
    <h4  class="dbp-h4">Source</h4>
    <div class="dbp-help-p">.admin/partials/dbp-page-admin-menu.php</div>
    
    <h4 class="dbp-h4">Example</h4>
    <div class="dbp-help-p">
        <pre class="dbp-code">add_action( 'dbp_admin_page_list_after_title', function($dbp_id, $count_items) {echo '<div style="width:100%">dbp_admin_page_list_after_title ID:'.$dbp_id.' total_items: '.$count_items.'</div>';}, 10, 2 );</pre>
    </div>



</div>

