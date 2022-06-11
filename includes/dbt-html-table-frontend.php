<?php
/**
 * Gestisco la visualizzazione della tabella nel frontend.
 *
 * @package    database-table
 * @subpackage database-table/includes
 */

namespace DatabaseTables;

class Dbt_html_table_frontend {
	/**
	 * @var $table_name Il nome della tabella che si sta visualizzando
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
	 * Imposta il prefisso dei request
	 */
	public function add_list_id($id) {
		$this->list_id = $id;
		$this->prefix_request = 'dbt'.$id;
	}
	/**
	 * @param Array $params Le configurazioni lato amministrativo
	 */
	public function add_frontend_view_setting($params) {
		$this->frontend_view_setting = $params;
	}

	

	/**
	 * Ritorna l'html Un div con un messaggio se c'è stato un errore o se il totale dei risultati è 0 oppure la tabella
	 * TODO Da spostare in list-functions!!
	 * @param Class $table_model
	 * @param Boolean ajax 
	 * @return String
	 */
	public function template_render($table_model, $ajax = false) {
		ob_start();
		$search = new Dbt_search_form();
		if ($table_model->last_error !== false) {
			?><div class="dbt-alert-error"><?php _e('The table is currently unavailable','database_tables'); ?></div><?php
		} else  {
			
			if ($ajax) {
				$uniqid_div = $_REQUEST['dbt_div_id'];
			} else {
				$uniqid_div = 'dbt_' . Dbt_fn::get_uniqid();
			}
			$add_class_size = (!in_array($this->get_frontend_view('table_size'), [false,'']) ) ?
				" dbt-block-table-".$this->get_frontend_view('table_size') : '';
			if (!$ajax) {
				?><a name="<?php echo  $this->prefix_request; ?>"></a>
				<div id="<?php echo $uniqid_div; ?>" class="not-found dbt-block-table <?php echo @$this->prefix_request; ?>-block"><?php
			}
			?><div class="dbt-max-large-table<?php echo $add_class_size; ?>"><?php
			if ($this->get_frontend_view('table_update', 'get') != 'get' ) {
				$search->open_form(get_permalink(), $this->get_frontend_view('table_update'));
				$search->add_params_list_per_post($this->list_id, ['page','search']);
				?>
				<input type="hidden" name="dbt_list_id" value="<?php echo $this->list_id; ?>">
				<input type="hidden" name="dbt_div_id" value="<?php echo $uniqid_div; ?>">
				<input type="hidden" class="js-dbt-sorting" name="<?php echo esc_attr($this->prefix_request); ?>_sort" value="<?php echo @$_REQUEST[$this->prefix_request."_sort"]; ?>">
				<?php 
				if (count($this->add_attributes) > 0) { 
					?>
					<textarea style="display:none" name="dbt_extra_attr"><?php echo esc_textarea(base64_encode(json_encode($this->add_attributes))); ?></textarea>	
					<?php 
				}
			}
			if ($this->get_frontend_view('table_search') == 'simple' ) {
				if ($this->get_frontend_view('table_update', 'get') != 'get') {
					$search->classic_search_post($this->prefix_request, 'Search', $this->get_frontend_view('table_style_color', 'blue'));
				} else {
					$search->classic_search_link($this->prefix_request, false, $this->get_frontend_view('table_style_color', 'blue'));
				}	
			}

			if (in_array($this->get_frontend_view('table_pagination_position'), ["up",'both']) ) {
				$this->pagination($table_model);
			}
			if (($this->no_result == '' || empty($this->no_result)) || count($table_model->items) > 1) {
				?><?php
				$this->render($table_model->items);
			} else {
				echo $this->no_result; 
			}
			
			if (in_array($this->get_frontend_view('table_pagination_position'), ["down",'both']) ) {
				$this->pagination($table_model);
			}

			if ($this->get_frontend_view('table_update','get') != 'get') {
				$search->close_form(false);
			}
			?></div><?php // chiudo dbt-max-large-table
			if (!$ajax) {
				?></div><?php
			}
		}
		return ob_get_clean();
	}

	/**
	 * Ritorna solo l'html della tabella
	 */
	public function table_render($table_model) {
		ob_start();
		$this->render($table_model->items);
		return ob_get_clean();
	}

	/**
	 * Stampa una tabella a partire da un array inserendo come nomi delle colonne la prima riga dell'array
	 * @param Array $items Accetta un array di oggetti o un array di array.
	 * @return void  
	 */
	private function render($items) {
		if (!is_array($items) || count ($items) == 0) return;
		$array_thead = array_shift($items);
		//var_dump ($array_thead);
		//die();
		?>
		<table class="dbt-table <?php echo esc_attr(@$this->table_class.' dbt-table-'.$this->get_frontend_view('table_style_color', 'blue')); ?>">
		<?php ob_start(); ?>
		<thead>
			<tr>
				<?php 
				foreach ($array_thead as $key => $value) {
					?>
					<th class="dbt-table-th dtf-th-dim-<?php echo strtolower($value->type). $value->width; ?>">
						<?php if (in_array($this->get_frontend_view('table_sort'),[false,'']) || $value->original_table == "") : ?>
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
		</table><?php
	}

	/**
	 * Imposta una funzione per una colonna. Questa funzione verrà chiamata per renderizzare la colonna
	 */
	public function add_table_class($class) {
		$this->table_class = $class;
	}
	/**
	 * Imposta un html speciale se non ci sono risultati
	 * @param string $html
	 */
	public function add_no_result($html) {
		$this->no_result = $html;
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
	 * Verifica e ritorna un'impostazione di frontendview
	 */
	private function get_frontend_view($key, $default = false) {
		if (array_key_exists($key, $this->frontend_view_setting)) {
			return $this->frontend_view_setting[$key];
		} else {
			return $default;
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
		if ($this->get_frontend_view('table_update', 'get') != 'get') {
			
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
	 * @param Object $table_model
	 * @return Void
	 */
	function pagination($table_model) {
		// pina-actions2.php

		echo PinaActions::execute(['shortcode'=>'pagination','attributes'=>[
			'id'=>$this->list_id, 
			'limit'=>$table_model->limit,
			'method'=> $this->get_frontend_view('table_update','get'),
			'total_items'=>$table_model->total_items, 
			'color'=>$this->get_frontend_view('table_style_color', 'blue'), 
			'style'=>$this->get_frontend_view('table_pagination_style', '')
			]]);
	return;
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