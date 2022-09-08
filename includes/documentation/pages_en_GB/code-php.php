<?php 
namespace DatabasePress;
if (!defined('WPINC')) die;
?>

<div class="dbp-content-table dbp-docs-content  js-id-dbp-content" >
    <h2 class="dbp-h2"> <a href="<?php echo admin_url("admin.php?page=dbp_docs") ?>">Doc</a><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('PHP','database_press'); ?></h2>

    <h1 style="border-bottom:1px solid #CCC">Dbp class</h1>
    <div class="dbp-help-p">
       
        <hr>
        <h2 class="dbp-h2">Dbp::get_list($dbp_id, $only_table = false, $params=[], $prefix = "")</h2>
        <p>Load a list from an id and return the html. Pretty much the same thing that the shortcode does!</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) The id of the list</li>
                <li><b>$ajax</b><br>
                (bool) Whether to print the filters and the form that groups the table or not.</li>
                <li><b>$params</b><br>  
                (Array) Any additional parameters to further filter the table [%params]
                <li><b>$prefix</b><br>  
                (String) A prefix for the names of the fields to be sent in the form to avoid collisions on multiple tables within the same page
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <p>Html</p>

        <h2 class="dbp-h2">Dbp::get_total($dbp_id, $filter = false)</h2>
        <p>Load a list from an id and return the html defined in the frontend view</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) The id of the list</li>
                <li><b>$filter</b><br>
                (bool) Whether to apply filters or not.</li>
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <p>Int |  -1 if he fails to count</p>


        <h2 class="dbp-h2">Dbp::get_lists_names()</h2>
        <p>Load all dbp lists</p>
        <h4 class="dbp-h4">Return</h4>
        <p>Array</p>

        <h2 class="dbp-h2">get_list_columns($dbp_id, $searchable = true, $extend = false)</h2>
        <p>Estrae l'elenco delle colonne di una lista.</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$searchable</b><br>
                (boolean) Extracts columns from tables only, excluding calculated columns.</li>
                <li><b>$extend</b><br>
                (boolean) If true it returns only the names of the columns, otherwise all the information available on the column.</li>
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <p>dbpDs_list_setting[]|array</p>


        <h2 class="dbp-h2">Dbp::get_primaries_id($dbp_id)</h2>
        <p>Returns the list of primary keys of a list. The extracted fields are the aliases!</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) L'id della lista</li>
            
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <p>@return array [table=>primary_name, ...]</p>


        <h2 class="dbp-h2">Dbp::get_data($dbp_id, $return = "items", $add_where = null, $limit = null, $order_field = null, $order="ASC")</h2>
        <p>Return the data or the model of a list</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) The id of the list</li>
                <li><b>$return</b><br>
                (string) items|schema|model|schema+items</li>
                <li><b>$add_where</b><br>
                (array)  [[op:'', column:'', value:'' ], ... ] es: [['op'=>'=', 'column'=>'dbp_id', value=>1]]</li>
                <li><b>$limit</b><br>
                (integer) The maximum number of records extracted</li>
                <li><b>$order_field</b><br>
                (string) The column on which to sort the data</li>
                <li><b>$orderorder_field</b><br>
                (string)  ASC|DESC</li>
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <p>Mixed</p>
        

        <h2 class="dbp-h2">Dbp::get_detail($dbp_id, $dbp_ids)</h2>
        <p>Return the data of a list starting from the ids. The data of the lists are modified by the configuration of list_view_formatting, while the data of the detail are those extracted from the query.</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) The id of the list</li>
                <li><b>$dbp_ids</b><br>
                (array|int|string) $dbp_ids [pri_key=>val, ...] For a single ID because a query can have multiple primary Ids due to left joins so I accept them all. If an integer instead I associate it with the first pri_id it returns to me. It also accepts the string 'uniq_chars_id'.</li>
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <p>\stdClass|false</p>
        
    
        <h2 class="dbp-h2">Dbp::save_data($dbp_id, $data)</h2>
        <p>Return the data or the model of a list</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) The id of the list</li>
                <li><b>$data</b><br>
                (array) The same imported structure extracted from get_data</li>
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <p>array</p>

    
        <h2 class="dbp-h2">Dbp::save_data($dbp_id, $data)</h2>
        <p>Save the data of one or more lines in a list.</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) The id of the list</li>
                <li><b>$data</b><br>
                (array) The same imported structure extracted from get_data</li>
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <p>array</p>


        <h2 class="dbp-h2">Dbp::render($dbp_id, $mode)</h2>
        <p>Restituisce la classe dbp_render_list</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>$dbp_id</b><br>
                (integer) L'id della lista</li>
                <li><b>$mode</b><br>
                (string) Sceglie come gestire i dati se in get|post|ajax o link (gestione parziale)</li>
        </ul>

        <h4 class="dbp-h4">Return</h4>
        <p>dbp_render_list</p>

        <h4 class="dbp-h4">Example</h4>
        <pre class="dbp-code">$list =  DatabasePress\Dbp::render(6, 'get'); // ($list_id:int, $mode:string); $mode is optional
$list->set_color('pink'); // Change the color of the list from the one you choose
$list->table("", false); // (custom_class:string, table_sort:bool)
$list->search(false); // optional (show_button:bool)
$list->single_field_search('column_name'); // Adds a text field to search in a single column 
$list->submit(); // Adds buttons for search
$list->pagination('select');
$list->end(); // Required!</pre>


    </div>

    <h1 style="border-bottom:1px solid #CCC">Template engine</h1>
    <div class="dbp-help-p">

        <h3>set get variable</h3>
        <pre class="code">&lt;?php 
    PinaCode::set_var(&quot;myvar&quot;,&quot;foobar&quot;); 
    echo PinaCode::get_var(&quot;myvar&quot;,&quot;default&quot;); 
?&gt;</pre>

        <h3>Execute shortcode</h3>

        <pre class="code">&lt;?php 
    PinaCode::execute_shortcode('...'); 
?&gt;</pre>
        <h3>Returns the result of a mathematical or logical expression</h3>
        
        <pre class="code">&lt;?php 
    PinaCode::math_and_logic('3 > 6'); 
?&gt;</pre>


        <h3>Set a new function</h3>
        <pre class="code">function pinacode_fn_hello_world($short_code_name, $attributes) {
    return 'HELLO WORLD';	
}
pinacode_set_functions('hello_world', 'pinacode_fn_hello_world');</pre>

        <h3>Set a new attributes</h3>
        <pre class="code">function pinacode_attr_fn_new_upper($gvalue, $param, $shortcode_obj) {
    if (is_string($gvalue)) {
        $gvalue = strtoupper($gvalue);
    }
    return $gvalue;
}
pinacode_set_attribute(['new_upper'], 'pinacode_attr_fn_new_upper');</pre>

    </div>
</div>
   