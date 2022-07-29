<?php 
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>

<div class="dbt-content-table dbt-docs-content  js-id-dbt-content" >

    <hr>
    <h2 class="dbt-h2"> apply_filters('<b>dbt_frontend_build_custom_link</b>', $custom_link, $list_id, $primary_values, $col_value, $col_key);</h2>
    Nel frontend nella creazione dei link personalizzati per aprire il dettaglio dei risultati.
    <hr>
    <h4 class="dbt-h4">Parameters</h4>
    <div class="dbt-help-p">
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
    <h4  class="dbt-h4">Source</h4>
    <div class="dbt-help-p">./includes/dbt-functions-items-setting.php</div>

    <h4 class="dbt-h4">Example</h4>
    <div class="dbt-help-p">
        L'esempio mostra il caricamento dei dati in un div personalizzato. Questo è possibile grazie alla funzione javascript setup_dbt_load_ajax_custom_div(el_container)
        <pre class="dbt-code">
        /**
        * Nella tabella del frontend:  Nell'HTML dove è stato messo lo shortcode [dbt_list id=xx] inserisco un html personalizzato '<div id="dbt_div_target_link"></div>'
        */
        function load_in_custom_box($custom_link, $list_id, $primary_values, $col_value, $col_key) {
            return '<a class="js-dbt-load-ajax-custom-div" href="'.esc_url(add_query_arg($primary_values, admin_url('admin-ajax.php'))).'" target="_blank">'
            .strip_tags($col_value).'</a>';

        }
        add_filter('dbt_frontend_build_custom_link', 'load_in_custom_box', 10, 5);
        </pre>
    </div>


    <hr>
    <h2 class="dbt-h2">apply_filters('<b>dbt_frontend_total_items</b>', $total_items_text, $list_id, $total_items,  $limit, $curr_page, $pages );</h2>
    Nelle tabelle modifica la scritta del totale degli elementi
    <hr>
    <h4 class="dbt-h4">Parameters</h4>
    <div class="dbt-help-p">
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
    <h4  class="dbt-h4">Source</h4>
    <div class="dbt-help-p">./includes/dbt-html-search-frontend.php</div>

    <h4 class="dbt-h4">Example</h4>
    <div class="dbt-help-p">
        <pre class="dbt-code">
        /**
        * Nella tabella del frontend: Mostra un testo alternativo nel totale degli elementi mostrati di una tabella
        */
        function dbt_total_items( $total_items_text, $list_id, $total_items,  $limit, $curr_page, $pages) {
            $min = (($curr_page -1) * $limit) + 1;
            $max = (($curr_page) * $limit);
            return sprintf( "Showing %s to %s of %s entries", $min, $max, $total_items);
        }

        add_filter('dbt_frontend_total_items', 'dbt_total_items', 10, 6);
        </pre>
    </div>





    <hr>
    <h2 class="dbt-h2">apply_filters('<b>dbt_frontend_table_thead</b>', header_text, $list_id, $array_thead);</h2>
    Nel frontend stampa i titoli della tabella. 
    <hr>
    <h4 class="dbt-h4">Parameters</h4>
    <div class="dbt-help-p">
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
    <h4  class="dbt-h4">Source</h4>
    <div class="dbt-help-p">./includes/dbt-html-table-frontend.php</div>

    <h4 class="dbt-h4">Example</h4>
    <div class="dbt-help-p">
        <pre class="dbt-code">
        /**
        * Nascondo l'header
        */
        function dbt_total_items($header_text, $list_id, $array_thead) {
            return '';
        }

        add_filter('dbt_frontend_table_thead', 'dbt_hide_thead', 10, 3);
        </pre>
    </div>

    

    <hr>
    <h2 class="dbt-h2">apply_filters('<b>dbt_table_status</b>', $status, $table);</h2>
    Cambia lo stato di una tabella (DRAFT|PUBLISH|CLOSE)
    <hr>
    <h4 class="dbt-h4">Parameters</h4>
    <div class="dbt-help-p">
        <ul>
            <li><b>$status</b>
                <br>(string) Lo stato attivo della tabella
            </li>
            <li><b>$table</b>
                <br>(string) Il nome della tabella
            </li>
        </ul>
        </div>
    <h4  class="dbt-h4">Source</h4>
    <div class="dbt-help-p">./includes/dbt-functions.php</div>

    <h4 class="dbt-h4">Example</h4>
    <div class="dbt-help-p">
        <pre class="dbt-code">
        /**
        * impedisco la modifica della tabella post
        */
        function dbt_lock_post_table($status, $table) {
            global $wpdb; 
            // $status = DRAFT|PUBLISH|CLOSE
            if ($table == $wpdb->prefix."posts") {
                $status = 'CLOSE';
            }
            return $status;
        }

        add_filter('dbt_table_status', 'dbt_lock_post_table', 10, 2);
        </pre>
    </div>


    <hr>
    <h2 class="dbt-h2">apply_filters('<b>dbt_save_data</b>', $query_to_execute, $list_id, $origin)</h2>
    Modifica i dati che si stanno per salvare nel database
    <hr>
    <h4 class="dbt-h4">Parameters</h4>
    <div class="dbt-help-p">
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
    <h4  class="dbt-h4">Source</h4>
    <div class="dbt-help-p">./includes/dbt-list-functions.php</div>

    <h4 class="dbt-h4">Example</h4>
    <div class="dbt-help-p">
        <pre class="dbt-code">
    /**
    * Salvo un post e ne modifico il titolo dal filtro
    */
    function test_dbt($status, $table) {
        $list_id = '[number_of_list_id]'; // è una lista che carica i dati dei post;
        $row = new StdClass();
        $row->post_title = "new record";
        $ris = DatabaseTables\Dbt::save_data($dbtid, $row);
    }
    add_action( 'init', 'test_dbt' );
    
    function dbt_action_in_save_data($query_to_execute, $list_id, $where) {
        // cambio il titolo
        $query_to_execute[0]['sql_to_save']['post_title'] = "Change the title";
        return $query_to_execute;
    }
    add_filter('dbt_save_data', 'dbt_action_in_save_data', 10, 3);
        </pre>
    </div>


    <h2 class="dbt-h2">apply_filters( '<b>dbt_frontend_search</b>', $field_name, $request_field_name, $list_id);</h2>
    Permette di modificare la form di ricerca nel frontend
    <hr>
    <h4 class="dbt-h4">Parameters</h4>
    <div class="dbt-help-p">
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
    <h4  class="dbt-h4">Source</h4>
    <div class="dbt-help-p">./includes/dbt-render-list.php</div>
    
    <h4 class="dbt-h4">Example</h4>
    <div class="dbt-help-p">
        <pre class="dbt-code">/**
 * change search form example 
 */
function dbt_frontend_field_search_fn($field_name, $input_field_name, $list_id) {
	if ($field_name == &quot;search&quot;) {
		?&gt;
		&lt;div class=&quot;dbt-search-row&quot;&gt;
			&lt;label&gt;&lt;span class=&quot;dbt-search-label&quot;&gt;Search Field&lt;/span&gt;	
			&lt;?php DatabaseTables\Dbt_fn::html_select(['0'=&gt;'No','1'=&gt;'Yes'], true, 'class=&quot;dbt-search-input js-dbt-search-input&quot; name=&quot;'. esc_attr($input_field_name).'&quot;', @$_REQUEST[$input_field_name]); ?&gt;
			&lt;/label&gt;
			&lt;div class=&quot;dbt-search-button dbt-search-button-blue&quot; onclick=&quot;dbt_submit_simple_search(this)&quot;&gt;&lt;?php _e('Search', 'database_tables'); ?&gt;&lt;/div&gt;
		&lt;/div&gt;
		&lt;?php 
		// to prevent input printing
		return '';
	} else {
		// print the search field normally
		return $field_name;
	}
}
add_filter('dbt_frontend_search', 'dbt_frontend_field_search_fn', 10, 3);</pre>
</div>



    <h2 class="dbt-h2">apply_filters('<b>dbt_frontend_get_list</b>', $html, $list_id);</h2>
    Permette di ridisegnare la visualizzazione di una lista in php.
        <hr>
        <h4 class="dbt-h4">Parameters</h4>
        <div class="dbt-help-p">
            <ul>
                <li><b>$html</b>
                    <br>(string)
                </li>
                <li><b>$list_id</b>
                    <br>(int) L'id della lista
                </li>
            </ul>
            </div>
        <h4  class="dbt-h4">Source</h4>
        <div class="dbt-help-p">./includes/dbt-render-list.php</div>
        
        <h4 class="dbt-h4">Example</h4>
        <div class="dbt-help-p">
            <pre class="dbt-code">function my_custom_list($render_data, $list_id) {
	if ($list_id != 6) return ''; // 6 is an example
    ob_start();
    $list =  DatabaseTables\Dbt::render($list_id, 'ajax'); 
    $list->table(); 
    $list->pagination();
    $list->end(); // Required!
    return ob_get_clean();
} 
add_filter('dbt_frontend_get_list', 'my_custom_list', 10, 2);</pre>
    </div>



    <h2 class="dbt-h2">apply_filters( 'pinacode_attribute_tmpl_'.$param, $gvalue, $param, $shortcode_obj);</h2>
    Se viene chiamato l'attributo template è possibile filtrare il parametro passato
    <hr>
    <p> I parametri accettati sono: $param, $gvalue, $count_for</p>


    <h4 class="dbt-h4">Parameters</h4>
    <div class="dbt-help-p">
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
    <h4  class="dbt-h4">Source</h4>
    <div class="dbt-help-p">./includes/pinacode/pina-attributes.php</div>
    
    <h4 class="dbt-h4">Example</h4>
    <div class="dbt-help-p">
        <pre class="dbt-code">
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




</div>

