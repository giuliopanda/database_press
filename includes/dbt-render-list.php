<?php
/**
 * Dbt_html_obj genera tutti i pezzetti che possono servire per generare la tabella o gli shortcode nel frontend
 * 
 * @package    database-table
 * @subpackage database-table/includes
 * @todo il metodo GET diventa LINk e si aggiunge il metodo GET con la form
 */

namespace DatabaseTables;

class Dbt_render_list {
	/**
	 * @var $table_model 
	 */
	var $table_model = false;
	/**
	 * @var String $table_name Il nome della tabella che si sta visualizzando
	 */
	var $table_name = "";
	/**
	 * @var String $table_class Una o più classi css da aggiungere al tag table 
	 */
	var $table_class = "";
	/**
	 * @var String $no_result l'html da stampare se non ci sono risultati
	 */
	var $no_result = "";
	/**
	 * @var String $prefix_request il prefisso da appore a tutte le richieste per paginazione, ordinamento e ricerca
	 */
	var $prefix_request = "";
	/**
	 * @var String $list_id il prefisso da appore a tutte le richieste per paginazione, ordinamento e ricerca
	 */
	var $list_id = "";
	/**
	 *  * @var Array $frontend_view_setting Le configurazioni lato amministrazione
	 */
	var $frontend_view_setting = [];
	/**
	 *  * @var Array $add_attributes [key=>value, ...] sono gli attributi aggiuntivi
	 */
	var $add_attributes = [];

	/**
	 * @var String $uniqid_div
	 */
	var $uniqid_div = "";
	
	/**
	 * @var String $mode ajax|get|post
	 */
	var $mode = "";

	/**
	 * @var Bool $block_opened se è stato aperto il blocco oppure no
	 */
	var $block_opened = false;

	/**
	 * @var Bool $search_opened se è stato aperto il blocco del search ?!
	 */
	var $search_opened = false;

	/**
	 * @var \DatabaseTables\Dbt_search_form $search
	 */
	var $search = false;

	/**
	 * @var Bool $div_container se è stato disegnato il div che contiene tutto 
	 * (in ajax non deve essere ridisegnato)
	 */
	var $div_container = true;
	/**
	 * @var string $color  il colore del blocco
	 */
	var $color = 'blue';

	/**
	 * @var DbtDs_list_setting[] $list_setting
	 */
	var $list_setting; 

	/**
	 * @param \Dbt_model $table_model
	 */
	function __construct($post_id, $mode = null, $prefix = "") {
		$this->list_id = $post_id;
		if ($prefix == "") {
			$prefix = "dbt".$post_id;
		}
		$this->prefix_request = $prefix;
		$post        = Dbt_functions_list::get_post_dbt($post_id);
	
        $this->table_model = Dbt_functions_list::get_model_from_list_params($post->post_content);
		// i params settati nella lista
        $extra_params =  Dbt_functions_list::get_extra_params_from_list_params(@$post->post_content['sql_filter']);
        if ($this->table_model) {
			// global non è documentata!
			PinaCode::set_var('global.dbt_filter_path', $prefix);
            PinaCode::set_var('global.dbt_id', $post_id);
        
            Dbt_functions_list::add_frontend_request_filter_to_model($this->table_model, $post->post_content , $post_id, $prefix);
            $this->table_model->get_list();
			
			$total_row = $this->table_model->get_count();
			$this->uniqid_div = 'dbt_' . Dbt_fn::get_uniqid();
			$this->table_model->update_items_with_setting($post->post_content, false);
			$this->table_model->check_for_filter();
			// TODO verificare se remove_hide_columns deve essere sostituita da  Dbt_fn::remove_hide_columns_in_row
			Dbt_fn::remove_hide_columns($this->table_model);
			if (isset($post->post_content['frontend_view']['detail_type']) && $post->post_content['frontend_view']['detail_type'] != "no" && $post->post_content['frontend_view']['type'] == "TABLE_BASE") {
				Dbt_fn::items_prepare_frontend_link($this->table_model, $post_id,$post->post_content);
			} else if (isset($post->post_content['frontend_view']['detail_type']) && $post->post_content['frontend_view']['detail_type'] != "no" && $post->post_content['frontend_view']['type'] != "TABLE_BASE") {
				Dbt_fn::add_items_frontend_popup_link($this->table_model, $post_id);
			}
			$this->add_extra_params($extra_params);
			if (isset($post->post_content['frontend_view'])) {
				$this->frontend_view_setting = $post->post_content['frontend_view'];
				$this->no_result = $post->post_content["frontend_view"]['no_result_custom_text'];
			}
			$this->list_setting = $post->post_content['list_setting'];
		}

		if (!is_null($mode)) {
			$this->mode = $mode;
		} else {
			$this->mode = $this->get_frontend_view('table_update','get');
		}
	}
	
	/**
	 * uniqid_div serve come riferimento per eventuali chiamate ajax della tabella.
	 * TODO Potrebbe essere usato anche per differenti chiamate non ajax alla stessa lista
	 */
	public function set_uniqid($uniq_id = "") {
		if ($uniq_id != "") {
			$this->uniqid_div = $uniq_id;
		}
		return $this->uniqid_div;
	}

	/**
	 * Setta il colore generico
	 */
	public function set_color($color) {
		if ($color != "") {
			if ($color == "") {
				$this->color = $this->get_frontend_view('table_style_color', 'blue');
			} else {
				$this->color = $color;
			}
		}
		return $this->color;
	}

	/**
	 * Se chiamata non stampa il div che contiene il codice
	 */
	public function hide_div_container() {
		
		$this->div_container =  false;
	}


	/**
	 * Verifica e ritorna un'impostazione di frontendview
	 */
	public function get_frontend_view($key, $default = false) {
		if (array_key_exists($key, $this->frontend_view_setting)) {
			return $this->frontend_view_setting[$key];
		} else {
			return $default;
		}
	}
	/**
	 * Stampa la tabella di una lista
	 * @param Array $items Accetta un array di oggetti o un array di array.
	 * @return void  
	 */
	public function table( $custom_class = "", $table_sort = null) {
		$this->open_block();
		$this->update_table_class($custom_class);
		$items = $this->table_model->items;
		if (!is_array($items) || count ($items) == 0) return;
		$array_thead = array_shift($items);
		?>
		<div class="dbt-table-overflow">
		<table class="dbt-table <?php echo esc_attr(@$this->table_class.' dbt-table-'.$this->color); ?>">
		<?php ob_start(); ?>
		<thead>
			<tr>
				<?php 
				foreach ($array_thead as $key => $value) {
					?>
					<th class="dbt-table-th dtf-th-dim-<?php echo strtolower($value->type). $value->width; ?>">
						<?php if (!$this->get_sort($table_sort, $value->original_table)) : ?>
							<div class="dbt-title-frontend"><?php echo $value->name; ?></div> 
						<?php else: ?>
						<?php $this->icons($value->name_request,  $value->name); ?>
						<?php endif; ?>
					</th>
					<?php 
				} 
				?>
			</tr>
		</thead>
		<?php echo apply_filters('dbt_frontend_table_thead', ob_get_clean(), $this->list_id, $array_thead); ?>
		<tbody>
		<?php foreach ($items as $item) : ?>
			<tr>
				<?php 
				foreach ($array_thead as $key=>$setting) { 
					$formatting_class = Dbt_fn::column_formatting_convert($setting->format_styles, $item->$key, '');
					$item->$key = Dbt_fn::column_formatting_convert($setting->format_values, $item->$key, $item->$key);

					?><td class="dtf-table-td<?php echo $setting->width.' '.$formatting_class; ?>"><div class="btn-div-td"><?php 
						echo $item->$key;
					?></div></td> <?php
				} 
				?>
			</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
		</div>
		<?php
	}

	

	/**
	 * Chiude il container
	 */
	public function end() {
		if ($this->block_opened) {
			$this->block_opened = false;
			if ($this->mode != 'link' && $this->search_opened) {
				$this->search->close_form(false);
			}
			echo "</div>";
			if ($this->div_container) {
				echo "</div>";
			}
		}
	}

	/**
	 * Disegna la ricerca classica su tutti i campi
	 */
	public function search($btn = true) {
		$this->open_block();
		$field_use = "search";
		
		$field_use = apply_filters( 'dbt_frontend_search', $field_use, $this->prefix_request."_search", $this->list_id);
		if ($field_use == "") return;
		
		if ($field_use != "search") {
			$this->single_field_search($field_use);
		} else if ($this->mode != 'link') {
			$this->search->classic_search_post($this->prefix_request, 'Search', $this->color, $btn);
		} else {
			$this->search->classic_search_link($this->prefix_request, false, $this->color);
		}	
		
	}
	/**
	 * Disegna la ricerca classica su tutti i campi
	 */
	public function submit($label = '') {
		if ($label == "") {
			$label = "Search";
		}
		// vedo se deve apparire il bottone clear oppure no
		$req_search = false;
		if (isset($_REQUEST) && is_array($_REQUEST)) {
			foreach ($_REQUEST as $key => $_) {
				if (substr($key, 0, strlen($this->prefix_request)) == $this->prefix_request) {
					$req_search = true;
					break;
				}
			}
		}
		?>
		<div class="dbt-search-row">
			<div class="dbt-search-button dbt-search-button-<?php echo $this->color; ?>" onclick="dbt_submit_simple_search(this)"><?php _e($label, 'database_tables'); ?></div>
			<div class="dbt-search-button dbt-search-button-<?php echo $this->color; ?>" onclick="dbt_submit_clean_simple_search(this)"><?php _e('Clean', 'database_tables'); ?></div>
		</div>
		<?php
	}

	/**  
	 * Genera una form di ricerca di un solo campo.
     * @param String $field_name Il nome della colonna estratta
     * @param String $btn_text Il testo 
     * @param String $label 
     * @return Void
	 */
	public function single_field_search($field_name,  $label = '') {
		$this->open_block();
		$field_name_request = '';
		
		foreach ($this->list_setting as $list_setting) {
            if (strtolower($list_setting->name) == strtolower(trim($field_name))) {
				$field_name_request = $list_setting->name_request;
				if ($label == '') {
					$label = $list_setting->title;
				}
				
			}
		}
		$field_name = apply_filters( 'dbt_frontend_search', $field_name, $this->prefix_request."_".$field_name_request, $this->list_id);
		if ($field_name_request != "" && $field_name != "") {
			$this->search->field_one_input_form($this->prefix_request."_".$field_name_request,  $label ) ;
		}
	}


	/**
	 * Aggiunge parametri da passare nella paginazione, ordinamento o nei filtri in generale
	 * @param array $attributes [key=>value, ...]
	 * @return void
	 */
	public function add_extra_params($attributes) {
		if (is_countable($attributes)) {
			$this->add_attributes = array_merge($this->add_attributes, $attributes);
		}
	}

	/**
	 * Aggiunge una classe alla tabella se la classe è già impostata la sostituisce
	 */
	private function update_table_class($class) {
		if ($class != "") {
			$this->table_class = $class;
		}
	}
	


	/**
	 * Override delle impostazioni del sort
	 */
	private function get_sort($sort, $value_original_table) {
		if ($value_original_table == "") return false;
		if (is_null($sort) || !is_bool($sort)) {
			return !in_array($this->get_frontend_view('table_sort'),[false,'']);
		} else {
			return $sort;
		}
	}

	/**
	 * Tutta la costruzione del codice (a parte l'ajax) deve iniziare con open_block e finire con close block
	 */
	public function open_block($add_class = true) {
		if (!$this->block_opened) {
			$this->block_opened = true;
			// il div container non si stampa quando la chiamata è ajax
			if ($this->div_container) {
				echo '<a name="'.$this->prefix_request.'"></a>';
				echo '<div id="'. $this->uniqid_div.'" class="not-found dbt-block-table '. $this->prefix_request.'-block">';
			}
			if ($add_class) {
				$add_class_size = (!in_array($this->get_frontend_view('table_size'), [false,'']) ) ?
				" dbt-block-table-".$this->get_frontend_view('table_size') : '';
			} else {
				$add_class_size = '';
			}

			echo '<div class="dbt-max-large-table'. $add_class_size.'">';

			$this->search = new Dbt_search_form();
			if ($this->mode != 'link' ) {
				if (!$this->search_opened) {
					$this->search->open_form(get_permalink(), $this->mode);
					$this->search->add_params_list_per_post($this->list_id, ['page','search']);
					$this->search_opened = true;
				}
			}
			if ($this->mode != 'link' ) {
			    if ($this->mode == 'ajax' ) {
				 	?>
					<input type="hidden" name="dbt_list_id" value="<?php echo $this->list_id; ?>">
					<input type="hidden" name="dbt_div_id" value="<?php echo $this->uniqid_div; ?>" class="dbt-div-id">
					<input type="hidden" name="dbt_prefix" value="<?php echo $this->prefix_request; ?>" class="dbt-div-id">
					<?php 
					if (count($this->add_attributes) > 0) { 
						?>
						<textarea style="display:none" name="dbt_extra_attr"><?php echo esc_textarea(base64_encode(json_encode($this->add_attributes))); ?></textarea>	
						<?php 
					}
			    }
				?>
				<input type="hidden" class="js-dbt-sorting" name="<?php echo esc_attr($this->prefix_request); ?>_sort" value="<?php echo @$_REQUEST[$this->prefix_request."_sort"]; ?>">
				<input type="hidden" name="<?php echo $this->prefix_request; ?>_page"  value="" class="js-dbt-page">
				<?php 
				
			}
		
		}
	}


	/**
	 * Disegna le icone accanto al titolo delle colonne della tabella
	 * 
	 * @param String $alias_column il nome o l'alias della colonna
	 * @return String  l'html dell'ordinamento delle colonne
	 */
	private function icons($alias_column, $title) {
		if ($alias_column == "") return ;
		$asc = '&darr;';
		$desc = '&uarr;';
		$no_sort = '&udarr;';
		if ($this->mode != 'link') {	
			if (isset($_REQUEST[$this->prefix_request.'_sort']) && substr($_REQUEST[$this->prefix_request.'_sort'],0, strlen($alias_column)) == $alias_column) {
				if ($_REQUEST[$this->prefix_request.'_sort'] == $alias_column.".asc")  {
					?><span class="dbt-title-order-link" onclick="dbt_submit_sorting(this, '<?php echo $alias_column; ?>.desc')"><?php echo $title." ".$desc; ?></span><?php
				} else {
					?><span class="dbt-title-order-link" onclick="dbt_submit_sorting(this, '<?php echo $alias_column; ?>.asc')"><?php echo $title." ".$asc; ?></span><?php
				}
			} else {
				?><span class="dbt-title-order-link" onclick="dbt_submit_sorting(this, '<?php echo $alias_column; ?>.asc')"><?php echo $title." ".$no_sort; ?></span><?php
			}
		} else {
			$link = $this->filter_order_pagination_add_query_args(get_permalink(), $this->prefix_request, 'sort');
			$link_asc = add_query_arg([$this->prefix_request.'_sort'=> $alias_column.".asc"], $link );
			$link_desc = add_query_arg([$this->prefix_request.'_sort' =>$alias_column.".desc"], $link );
			if (isset($_REQUEST[$this->prefix_request.'_sort']) && substr($_REQUEST[$this->prefix_request.'_sort'],0, strlen($alias_column)) == $alias_column) {
				if ($_REQUEST[$this->prefix_request.'_sort'] == $alias_column.".asc")  {
					?><a class="dbt-title-order-link" href="<?php echo $link_desc; ?>"><?php echo $title." ".$desc; ?></a><?php
				} else {
					?><a class="dbt-title-order-link" href="<?php echo $link_asc; ?>"><?php echo $title." ".$asc; ?></a><?php
				}
			} else {
				?><a class="dbt-title-order-link" href="<?php echo $link_asc; ?>"><?php echo $title." ".$no_sort; ?></a><?php
			}
		}
	}

	/**
	 * Disegna la paginazione
	 * 
	 * @param String $style select | numeric
	 * @return Void
	 */
	function pagination($style = null) {
		$this->open_block();
		if (is_null($style) || !in_array($style, ['select','numeric'])) {
			$style = $this->get_frontend_view('table_pagination_style', '');
		} else {
			$style = "";
		}
		if ($this->mode != "link") {
			$this->search->pagination_form($this->prefix_request, $this->table_model->total_items, $this->table_model->limit, $this->list_id, $style, $this->color, $this->mode);
		} else {
			//@TODO manca il prefix
			$this->search->pagination_link(get_permalink(), $this->table_model->total_items, $this->table_model->limit, $this->list_id, $style, $this->color);
		}
	
	}

	/**
	 * Aggiunge i parametri dei filtraggi del frontend ai link
	 * Lo uso su pagination, order, filter
	 * le query possono essere passate in $path_parametro oppure $path[parametro]
	 * @param String $link 
	 * @param String $path Il prefisso dei parametri
	 * @param String $exclude Se c'è un parametro da non inserire
	 * @return String Il nuovo link
	 */
	private function filter_order_pagination_add_query_args($link, $path, $exclude = "") {
		$length = strlen($path);
		if (isset($_REQUEST) && is_array($_REQUEST)) {
			foreach ($_REQUEST as $key=>$value) {
                if (is_string($value)) {
                    if (substr($key,0, $length) == $path && substr($key, $length) != "_".$exclude) {
                        $link = add_query_arg($key, urlencode(stripslashes($value)), $link);
                    }
                } else {
                    foreach ($value as $val) {
                        if (is_string($val)) {
                            if (substr($key,0, $length) == $path && substr($key, $length) != "_".$exclude) {
                                $link = add_query_arg($key."[]", urlencode(stripslashes($val)), $link);
                            }
                        }
                    }
                   
                }
			}
		}
		return $link;
	}
}