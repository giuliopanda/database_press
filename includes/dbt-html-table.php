<?php
/**
 * Gestisco la tabella amministrativa
 * 
 * @since      1.0.0
 *
 * @package    database-table
 * @subpackage database-table/includes
 */
namespace DatabaseTables;

if (!defined('WPINC')) die;
class Dbt_html_table {
	/**
	 * @var $table_name Il nome della tabella che si sta visualizzando
	 */
	var $table_name = "";
	/**
	 * @var Array $filter_columns L'array delle funzioni per personalizzare la visualizzazione delle colonne
	 */
	var $filter_columns = [];
	/**
	 * @var Array $filter_title L'array delle funzioni per personalizzare la visualizzazione dei titoli delle colonne
	 */
	var $filter_title   = [];
	/**
	 * @var String $table_class Una o più classi css da aggiungere al tag table 
	 */
	var $table_class = "";
	/**
	 *  * @var Array $add_attributes [key=>value, ...] sono gli attributi aggiuntivi
	 */
	var $add_attributes = [];
	
	/**
	 * Funziona solo se la classe viene estesa
	 * Quando viene renderizzata la tabella prima vengono cercate eventuali funzioni all'interno della classe 
	 * che personalizzino la visualizzazione della colonna stessa.
	 */
	private function set_items($items) {
		if (!is_array($items)) return;
		$first_row = (array)reset($items);
		foreach ($first_row as $key=>$item) {
			if (!array_key_exists($key, $this->filter_columns) && method_exists($this, "tr_".$key)) {
				$this->filter_columns[$key] = [$this, "tr_".$key];
			}
			if (!array_key_exists($key, $this->filter_title) && method_exists($this, "th_".$key)) {
				$this->filter_title[$key] = [$this, "th_".$key];
			}
		}
	}

	/**
	 * Ritorna l'html Un div con un messaggio se c'è stato un errore o se il totale dei risultati è 0 oppure la tabella
	 * @param \Dbt_model $table_model
	 * @return String
	 */
	public function template_render($table_model) {
		ob_start();
		if ($table_model->last_error !== false) {
			?><div class="dtf-alert-sql-error"><h2>Query error:</h2><?php echo $table_model->last_error; ?></div><?php 
		} else if($table_model->items != false && is_countable($table_model->items) && count($table_model->items) > 0) {
			$this->render($table_model->items, $table_model->sort, $table_model->filter);
		} else  {
			?><div class="dtf-alert-gray"><?php echo sprintf(__('MySQL returned %s rows. (Query took %s seconds.)', 'database_tables'), $table_model->effected_row,  $table_model->time_of_query ); ?></div><?php 
		}
		if (count($this->add_attributes) > 0) { 
			?>
			<textarea style="display:block" name="dbt_extra_attr"><?php echo esc_textarea(base64_encode(json_encode($this->add_attributes))); ?></textarea>	
			<?php 
		}
		return ob_get_clean();
	}

	/**
	 * Stampa una tabella a partire da un array inserendo come nomi delle colonne le chiavi dell'array stesso
	 * Per personalizzare le colonne puoi creae i filtri
	 * call_gp_view_table_th_[nome_colonna]() : per cambiare il titolo
	 * call_gp_view_table_tr_[nome_colonna]($item) : per cambiare il valore della colonna $item è la riga
	 * @param Array $items [{info_schema}{item},{item},...] Accetta un array di oggetti o un array di array. La prima riga ha le informazioni della tabell
	 * @param Array $sorting ['key'=>'String','order'=>'ASC|DESC'] | false
	 * @param Array $searching [[op:'',column:'',value:''],...] | false
	 * @return void  
	 */
	public function render($items, $sorting = false, $searching = false) {
		if (!is_array($items) || count ($items) == 0) return;
		$this->set_items($items);
		$array_thead = array_shift($items);
		$max_input_vars = Dbt_fn::get_max_input_vars();
		?>
		<script>var dbt_tb_id = [];var dbt_tb_id_del = [];</script>
		<table class="wp-list-table widefat striped dbt-table-view-list <?php echo esc_attr($this->table_class); ?>">
		<thead>
			<tr>
				<?php 
				foreach ($array_thead as $key => $value) {
					$row_sorting = $this->check_sorting($sorting, $value->sorting, $value->original_field_name);
					if ($value->type == "CHECKBOX"  ) {
						if ($max_input_vars - 50 > count($items)) {
							?>
							<th class="dtf-table-th dtf-th-dim-<?php echo strtolower($value->type); ?>" ><input type="checkbox" onclick="dbt_table_checkboxes(this)"></th>
							<?php
						}
					}  else { 
						?>
						<th class="dtf-table-th dtf-th-dim-<?php echo strtolower($value->type). $value->width; ?>">
							<?php 
							$dropdwon_html = "";
							if ($value->dropdown) {
								ob_start();
								$this->dropdown($value->field_key, $value->original_field_name, $value->name_column, $value->type, $value->original_table, $row_sorting, $searching);
								$dropdwon_html = ob_get_clean();
							}
							?>
							<div class="dtf-table-th-content">
								<?php if ($dropdwon_html != "") : ?>
									<div class="dtf-table-title<?php echo ($value->field_key != "" && ($row_sorting !== false || $searching !== false)) ? " js-dtf-table-show-dropdown": ""; ?>" data-fieldkey="<?php echo $value->name_column; ?>"><?php echo $value->name; ?></div> 
								<?php $this->icons($value->field_key, $value->original_field_name, $row_sorting, $searching, $value->name_column); ?>
								<?php else : ?>
									<div class="dtf-table-title"><?php echo $value->name; ?></div> 
								<?php endif; ?>
							</div>
							<?php
							if ($dropdwon_html != "") {
								?><div class="js-dbt-dropdown-header dbt-dropdown-header" id="dbt_dropdown_<?php echo $value->name_column; ?>"><?php
								echo $dropdwon_html;
								?></div><?php
							}
							?>
						</th>
						<?php 
					}
				} 
				?>
			</tr>
		</thead>
		<tbody>
		<?php 
		$id_base = "dbt_".uniqid();
		$count_row = 1;
		foreach ($items as $item) : ?>
			
			<tr id="<?php echo $id_base.'_'.$count_row; ?>">
				<?php 
				
				$count_row++;
				foreach ($array_thead as $key=>$setting) { 
					
					$formatting_class = Dbt_fn::column_formatting_convert($setting->format_styles, $item->$key, '');
					$item->$key = Dbt_fn::column_formatting_convert($setting->format_values, $item->$key, $item->$key);
					if ($setting->type == "CHECKBOX" && $max_input_vars - 50 <= count($items) ) continue;
					?><td class="dtf-table-td<?php echo $setting->width.' '.$formatting_class; ?>"><div class="btn-div-td btn-div-td-<?php echo strtolower($setting->type); ?>" data-dbt_rif_value="<?php echo esc_attr($key); ?>"><?php echo $item->$key; ?></div></td> <?php
				} 
				?>
			</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
		<?php
	}

	/**
	 * 
	 */
	private function check_sorting($global_sort, $column_sort, $field_key) {
		if (is_array($global_sort) && $global_sort['field'] == $field_key ) {
			return $global_sort;
		} else {
			return ($global_sort == true) ? $column_sort : $global_sort;
		}
		
	}

	/**
	 * Imposta una funzione per una colonna. Questa funzione verrà chiamata per renderizzare la colonna
	 */
	public function add_table_class($class) {
		$this->table_class = $class;
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
	 * Imposta una funzione per una colonna. Questa funzione verrà chiamata per renderizzare la colonna
	 */
	public function add_filter_column($column_name, $function) {
		$this->filter_columns[$column_name] = $function;
	}

	/**
	 * Imposta una funzione per renderizzare il titolo di una colonna.
	 */
	public function add_filter_title($column_name, $function) {
		$this->filter_title[$column_name] = $function;
	}


	/**
	 * Disegna le icone accanto al titolo delle colonne della tabella
	 * è statica perché può essere richiamata anche dall'esterno per disegnare una singola colonna se ad esempio viene chiamato un add_filter
	 * 
	 * @param String $alias_column il nome o l'alias della colonna
	 * @param String $original_field_name Il nome originale della colonna
	 * @return String  l'html dell'ordinamento delle colonne
	 */
	static function icons($alias_column, $original_field_name, $sort, $filter, $name_column) {
		if ($alias_column == "") return ;
		if ($sort != false && is_array($sort)) {
			if (strtolower(@$sort['field']) == strtolower($alias_column) || strtolower(@$sort['field']) == strtolower($original_field_name)) {
				if (strtolower(@$sort['order'])  == "asc") {
					?><span class="dashicons dashicons-arrow-down dtf-table-sort js-dtf-table-sort" data-dtf_sort_key="<?php echo esc_attr($alias_column); ?>" data-dtf_sort_order="DESC"></span><?php
				} else {
					?><span class="dashicons dashicons-arrow-up dtf-table-sort js-dtf-table-sort" data-dtf_sort_key="<?php echo esc_attr($alias_column); ?>" data-dtf_sort_order="ASC"></span><?php
				}
				
			}
		}
		if (is_array($filter)) {
			foreach ($filter as $f) {
				$between =  Dbt_fn::is_correct_between_value($f['value'], $f['op']);
				if (strtolower($f['column']) == strtolower($original_field_name) && (($f['value'] != "" && strpos($f['op'], 'BETWEEN') === false) || $between !== false)) {
					?><span class="dashicons dashicons-filter js-click-dashicons-filter" data-rif="<?php echo esc_attr($name_column); ?>"></span><?php
				}
			}
		}
	}

	/**
	 * Disegna il popup al click del titolo delle colonne delle tabelle
	 * è statica perché può essere richiamata anche dall'esterno per disegnare una singola colonna se ad esempio viene chiamato un add_filter
	 * @param String $alias_column il nome o l'alias della colonna
	 * @param String $original_field_name table.orgname
	 * 
	 * @return String  l'html dell'ordinamento delle colonne
	 */
	static function dropdown($alias_column, $original_field_name, $name_column, $type, $original_table, $sort, $filter) {
		if ($alias_column == "") return "";
		if ($sort !== false)  {
			$sort_asc_class = 'js-dtf-table-sort dbt-dropdown-line-click';
			$sort_desc_class = 'js-dtf-table-sort dbt-dropdown-line-click';
			$sort_remove_class = 'dbt-dropdown-line-disable';
			if (is_array($sort) ) {
				if (strtolower(@$sort['field']) == strtolower($original_field_name)) {
					$sort_desc_class = (strtolower(@$sort['order'])  == "desc") ? 'dbt-dropdown-line-disable' : 'js-dtf-table-sort dbt-dropdown-line-click';	
					$sort_asc_class = (strtolower(@$sort['order'])  == "asc") ? 'dbt-dropdown-line-disable' : 'js-dtf-table-sort dbt-dropdown-line-click';
					$sort_remove_class = 'js-dtf-table-sort dbt-dropdown-line-click';
				}
			} 
		}
			// ricerca
		if ($filter !== false)  {	
			$symple_type = $type;
			$name_column = Dbt_fn::clean_string($name_column);
			$def_op = "=";
			$def_input_value = "";
			list($html_select_array, $def_op) =  Dbt_fn::get_array_for_select_in_drowdown_filter($type);
			$default_value = $def_input_value_2 = "";
			
			if (@is_array($filter)) {
				foreach ($filter as $f) {
					if ( strtolower($f['column']) == strtolower($original_field_name) && $f['value'] != "" && $f['value'] != "#AND#") {
						$default_value = $f['value'];
						$between = Dbt_fn::is_correct_between_value($f['value'], $f['op']);
						if ($between !== false) {
							$def_input_value = $between[0];
							$def_input_value_2 = $between[1];
						} else if ($f['op'] != "IN" && $f['op'] != "NOT IN") {
							$def_input_value = $f['value'];
						} 
						$def_op = $f['op'];
					}
				}
			} 
		}
		if ($sort !== false || $filter !== false)  {	
			require (DBT_DIR."/admin/partials/dbt-partial-dropdown.php");
		}
	}
}