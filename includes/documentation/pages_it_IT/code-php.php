<?php 
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>

<div class="dbt-content-table dbt-docs-content  js-id-dbt-content" >
    <h2 class="dbt-h2"> <a href="<?php echo admin_url("admin.php?page=dbt_docs") ?>">Doc</a><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('PHP','database_tables'); ?></h2>

    <h1 style="border-bottom:1px solid #CCC">Dbt class</h1>
    <div class="dbt-help-p">
       
        <hr>
        <h2 class="dbt-h2">Dbt::get_list($dbt_id, $ajax = false)</h2>
        <p>Carica una lista da un id e ne ritorna l'html. </p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
                <li><b>$dbt_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$ajax</b><br>
                (bool) Se stampare i filtri e la form che la raggruppa la tabella oppure no.</li>
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>Html</p>

        <h2 class="dbt-h2">Dbt::get_total($dbt_id, $filter = false)</h2>
        <p>Carica una lista da un id e calcola il totale degli elementi.</p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
                <li><b>$dbt_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$filter</b><br>
                (bool) Se applicare i filtri oppure no.</li>
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>Int,  -1 se non riesce a fare il conto</p>


        <h2 class="dbt-h2">Dbt::get_lists_names()</h2>
        <p>Carica tutte le liste dbt</p>
        <h4 class="dbt-h4">Return</h4>
        <p>Array</p>

        <h2 class="dbt-h2">get_list_columns($dbt_id, $searchable = true, $extend = false)</h2>
        <p>Estrae l'elenco delle colonne di una lista.</p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
                <li><b>$dbt_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$searchable</b><br>
                (boolean) Estrae solo le colonne delle tabelle  escludendo le colonne calcolate.</li>
                <li><b>$extend</b><br>
                (boolean) Se true torna solo i nomi delle colonne, altrimenti tutte le informazioni disponibili sulla colonna.</li>
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>DbtDs_list_setting[]|array</p>


        <h2 class="dbt-h2">Dbt::get_primaries_id($dbt_id)</h2>
        <p>Ritorna l'elenco delle chiavi primarie di una lista. I campi estratti sono gli alias!</p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
                <li><b>$dbt_id</b><br>
                (integer) L'id della lista</li>
            
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>@return array [table=>primary_name, ...]</p>


        <h2 class="dbt-h2">Dbt::get_data($dbt_id, $return = "items", $add_where = null, $limit = null, $order_field = null, $order="ASC")</h2>
        <p>Ritornano i dati o il model di una lista</p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
                <li><b>$dbt_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$return</b><br>
                (string) items|schema|model|schema+items</li>
                <li><b>$add_where</b><br>
                (array)  [[op:'', column:'', value:'' ], ... ] es: [['op'=>'=', 'column'=>'dbt_id', value=>1]]</li>
                <li><b>$limit</b><br>
                (integer) il numero massimo di record estratti</li>
                <li><b>$order_field</b><br>
                (string) La colonna su cui ordinare i dati</li>
                <li><b>$orderorder_field</b><br>
                (string)  ASC|DESC</li>
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>Mixed</p>
        

        <h2 class="dbt-h2">Dbt::get_data_by_id($dbt_id, $dbt_ids)</h2>
        <p>Ritornano i dati di una lista a partire dagli id</p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
                <li><b>$dbt_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$dbt_ids</b><br>
                (array|int) $dbt_ids [pri_key=>val, ...] per un singolo ID perché una query può avere più primary Id a causa di left join per cui li accetto tutti. Se un integer invece lo associo al primo pri_id che mi ritorna.</li>
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>\stdClass|false</p>
    
        <h2 class="dbt-h2">Dbt::save_data($dbt_id, $data)</h2>
        <p>Salva i dati di una o più righe in una lista.</p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
                <li><b>$dbt_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$data</b><br>
                (array) La stessa struttura importata estratta da get_data</li>
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>array</p>

        <h2 class="dbt-h2">Dbt::get_form_data_by_id($dbt_id, $dbt_ids)</h2>
        <p>Ritornano tutti i dati modificabili di una lista a partire dagli id</p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
            <li><b>$dbt_id</b><br>
            (integer) L'id della lista</li>
            <li><b>$dbt_ids</b><br>
            (array|int) $dbt_ids [pri_key=>val, ...] per un singolo ID perché una query può avere più primary Id a causa di left join per cui li accetto tutti. Se un integer invece lo associo al primo pri_id che mi ritorna.</li>
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>\stdClass|false</p>
    
        <h2 class="dbt-h2">Dbt::save_data($dbt_id, $data)</h2>
        <p>Ritornano i dati o il model di una lista</p>
        <h4 class="dbt-h4">Parameters</h4>
        <ul>
                <li><b>$dbt_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$data</b><br>
                (array) La stessa struttura importata estratta da get_data</li>
        </ul>
        <h4 class="dbt-h4">Return</h4>
        <p>array</p>



    </div>

    <h1 style="border-bottom:1px solid #CCC">Template engine</h1>
    <div class="dbt-help-p">

        <h3>set get variable</h3>
        <pre class="code">
        &lt;?php 
            PinaCode::set_var(&quot;myvar&quot;,&quot;foobar&quot;); 
            echo PinaCode::get_var(&quot;myvar&quot;,&quot;default&quot;); 
        ?&gt;
        </pre>


        <h3>Execute shortcode</h3>
        
        <pre class="code">
        &lt;?php 
            PinaCode::execute_shortcode('...'); 
        ?&gt;
        </pre>

        <h3>Ritorna il risultato di un'espressione matematica o logica</h3>
        
        <pre class="code">
        &lt;?php 
            PinaCode::math_and_logic('3 > 6'); 
        ?&gt;
        </pre>


        <h3>Set a new function</h3>
        <pre class="code">
    function pinacode_fn_hello_world($short_code_name, $attributes) {
        PinaCode::set_var('global.search_container.status', 'open');
        return $string;	

    }
    pinacode_set_functions('hello_world', 'pinacode_fn_search_open_container');
        </pre>

        <h3>Set a new attributes</h3>
        <pre class="code">
    function pinacode_attr_fn_new_upper($gvalue, $param, $shortcode_obj) {
		if (is_string($gvalue)) {
			$gvalue = strtoupper($gvalue);
		}
		return $gvalue;
	}
    pinacode_set_attribute(['new_upper'], 'pinacode_attr_fn_new_upper');
    </pre>

    </div>
</div>
   