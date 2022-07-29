<?php
/**
 * Gestisco il filtri e hook per il tab import
 * 
 *
 * @package    database-table
 * @subpackage database-table/includes
 */
namespace DatabaseTables;

class Dbt_loader_import {

	public function __construct() {
		// L'ajax per l'import carica la struttura di una tabella
		add_action( 'wp_ajax_dbt_import_csv_table_structure', [$this, 'dbt_import_csv_table_structure']);
		// L'ajax per testare l'import di un csv
		add_action( 'wp_ajax_dbt_test_import_csv_data', [$this, 'dbt_test_import_csv_data']);
		// L'ajax per testare l'import di un csv
		add_action( 'wp_ajax_dbt_import_csv_data', [$this, 'dbt_import_csv_data']);
		// Questa una chiamata che deve rispondere un csv
		add_action( 'admin_post_dbt_download_csv_report', [$this, 'dbt_download_csv_report']);		
	}

    /**
	 * Ritorna la struttura di una tabella per la pagina di importazione da csv 
	 * 
	 */
	public function dbt_import_csv_table_structure() {
		Dbt_fn::require_init();
		$ris = ['html'=>'','result'=>'no'];
		if (array_key_exists('table', $_REQUEST) && array_key_exists('elid', $_REQUEST)) {
			$columns = Dbt_fn::get_table_structure(@$_REQUEST['table']);
			$ris['elid'] = $_REQUEST['elid'];
			$csv_columns = $this->prepare_columns();
			ob_start();
			$unique =  Dbt_fn::clean_string($_REQUEST['table'])."_".$_REQUEST['elid'];
			?>
			<input type="hidden" name="import_table[<?php echo esc_attr($_REQUEST['elid']); ?>]" value="<?php echo esc_attr($_REQUEST['table']); ?>">
			<input type="hidden" class="js-import-unique-input" name="import_unique[<?php echo esc_attr($_REQUEST['elid']); ?>]" value="<?php echo esc_attr($unique); ?>">
			<table class="wp-list-table widefat striped dbt-table-view-list dbt-table-preview-csv">	
				<thead>
					<tr>
						<th class="dtf-table-th dtf-th-dim-gen"><?php echo _e('Table Column','database_tables'); ?></td>
						<th class="dtf-table-th dtf-th-dim-gen"><?php echo _e('Type','database_tables'); ?></td>
						<th class="dtf-table-th dtf-th-dim-gen"><?php echo _e('CSV column','database_tables'); ?></td>
					</tr>
				</thead>
				<tbody>
				<?php 
				$csv_columns_convert = $this->prepare_columns_convert();
				$csv_columns_convert2 = [];
				if (isset($_REQUEST['csv_structure_table_created'])) {
					foreach ($_REQUEST['csv_structure_table_created'] as $csv_stru) {
						$csv_stru = (array)$csv_stru;
						$csv_columns_convert2[$csv_stru['field_name']] = $csv_stru['name'];
					} 
				}
				foreach ($columns as $c) {
					$column_search = $c->Field;
					if (isset($csv_columns_convert2[$c->Field])) {
						$column_search = $csv_columns_convert2[$c->Field];
					}  else if (isset($csv_columns_convert[$c->Field])) {
						$column_search = $csv_columns_convert[$c->Field];
					} else if (isset($csv_columns[str_replace("column_","",$c->Field)])) {
						$column_search = str_replace("column_","",$c->Field);
					}
					?>
						<tr>
							<td><input type="hidden" name="import_field[<?php echo esc_attr($_REQUEST['elid']); ?>][]" value="<?php echo $c->Field; ?>"><?php echo $c->Field; ?>
							<?php 
							if ($c->Key == "PRI" && $c->Extra == "auto_increment") {
								$key_class=" js-fields-choosen-key";
								?> <span class="dashicons dashicons-admin-network" title="PRIMARY KEY"></span>*<?php
							}  else {
								$key_class="";
							}
							?>
							</td>
							<td><?php echo $c->Type; ?></td>
							<td><?php Dbt_fn::html_select($csv_columns, true, 'class="js-fields-choosen'.$key_class.'" name="import_csv_column['.esc_attr($_REQUEST['elid']).'][]" style="min-width:400px"',  $column_search); ?></td>
						</tr>
				<?php 
				}
				?>
				</tbody>
			</table>
			<?php 
			$ris['html'] = ob_get_clean();
			$ris['unique'] = $unique;
			$ris['result'] = 'ok';
		} else {
			$ris['result'] = 'no';
		}
		wp_send_json($ris);
		die();
	}

	/**
	 * Testo l'import del csv
	 * $_REQUEST['import_table'];
	 * $_REQUEST['import_unique'];
	 * $_REQUEST['csv_temporaly_filename'];
	 * $_REQUEST['csv_delimiter'];
	 * $_REQUEST['import_csv_column']
	 * $_REQUEST['import_field']
	 */
	public function dbt_test_import_csv_data() {
		global $wpdb;
		/**
		 * @var Array $table_insert  [['table'=>'','fields'=>[]],[]] dice quali campi del csv deve importare in quali campi delle tabelle
		 */
		$table_insert = [];
		/**
		 * @var Array $html_result;
		 */
		$html_result = [];

		
		Dbt_fn::require_init();
		$import_tables = @$_REQUEST['import_table'];
		$import_unique = @$_REQUEST['import_unique'];
		
		if (!is_array($import_tables) || count($import_tables) == 0 ) {
			wp_send_json(['result'=>'no','msg'=>__('You must select at least one table before you can import data','database_tables')]);
			die;
		}
		$temporaly_files = new Dbt_temporaly_files();
		$csv_filename = $_REQUEST['csv_temporaly_filename'];
		$csv_delimiter = $_REQUEST['csv_delimiter'];
		$csv_first_row_as_headers = Dbt_fn::get_request('csv_first_row_as_headers', false, 'boolean');
		$table_insert  = Dbt_fn_import::convert_csv_data_request_to_vars($import_tables, $_REQUEST['import_field'], $_REQUEST['import_csv_column']);
		$csv_items = $temporaly_files->read_csv($csv_filename, $csv_delimiter, $csv_first_row_as_headers, 200);
		array_shift($csv_items);
		$wpdb->query("SET sql_mode = '';");
		// importo i csv
		foreach ($table_insert as $uniqid_key => $ti) {
			$primary_key = Dbt_fn::get_primary_key($ti->table);
			$table_temp = Dbt_fn_import::create_temporaly_table_from($ti, $csv_items, $primary_key);
			if ($table_temp) {
				$results = [];
				$columns = Dbt_fn::get_table_structure($ti->table);
				//PinaCode::set_var('csv_items', $csv_items) ;
				foreach ($csv_items as $key_csv_items=>$item) {
					PinaCode::set_var('key', $key_csv_items) ;
					$array_insert = [];
					// ciclo i campi da importare
					PinaCode::set_var('item', $item) ;
					foreach ($ti->fields as $table_column => $csv_column) {
						if ($csv_column != "") {
							$array_insert[$table_column] = PinaCode::execute_shortcode(stripslashes($csv_column));
						}
					}
				
					$array_insert = array_filter($array_insert);
				
					if (count($array_insert) > 0 && $primary_key != "") {
						$temp = Dbt_fn_import::wpdb_replace($table_temp, $array_insert, $primary_key);
						$results[] = $temp;
						if (isset($import_unique[$uniqid_key])) {
							$csv_items[$key_csv_items][$import_unique[$uniqid_key]] = $temp['id'];
						}
					}
				}
				ob_start();
				?>
				<div class="dtf-alert-info"><?php _e(sprintf('I start testing the import for the "<b>%s</b>" table', $ti->table), 'database_tables'); ?></div>
				<?php 
				if ($primary_key == "") : ?>
					<div class="dtf-alert-sql-error"><?php _e(sprintf('The table "<b>%s</b>" must have a field set auto increment primary key', $ti->table ),'database_tables'); ?></div>
				<?php elseif (count($results) == 0) : ?>
					<div class="dtf-alert-sql-error"><?php _e('I have not found any data to insert','database_tables'); ?></div>
				<?php else : ?>
					<div class="dbt-import-table-csv-big-preview">
						<table class="wp-list-table widefat striped dbt-table-view-list dbt-table-preview-csv">
						<thead>	
							<tr>
						<?php foreach ($columns as $r) :?>
								<th><?php echo $r->Field; ?></th>
						<?php endforeach; ?>
								<th>Action</th>
								<th>Error</th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach ($results as $r) {
							?><tr><?php 
							foreach ($columns as $c) {
								$field = $c->Field;
								
								$icon = "";
								if (isset($r['data'][$field])) {
									$current_data = $r['data'][$field];
									if (isset($r['row']->$field) && $current_data != $r['row']->$field) {
										$icon = '<span class="dashicons dashicons-info dbt-icon-alert" title="'.esc_attr(__('The value is different from the original csv','database_tables')).'"></span>';
									}
								}
								if (isset($r['old_row']->$field)) {
									$taction = "UPDATE";
									if (isset($r['row']->$field) && $r['row']->$field != $r['old_row']->$field) {
										echo "<td class=\"dbt_css_insert\"><span style=\" text-decoration: line-through;\">".$r['old_row']->$field."</span><br>".$r['row']->$field.$icon."</td>";
									} else {
										if (isset($r['row']->$field)) {
											echo "<td class=\"dbt_css_insert\">".$r['row']->$field.$icon."</td>";
										} else {
											echo "<td>".$icon."</td>";
										}
									}
								} else {
									if (isset($r['row']->$field)) {
										echo "<td  class=\"dbt_css_insert\">".$r['row']->$field.$icon."</td>";
									} else {
										echo "<td>".$icon."</td>";
									}
									$taction = "INSERT";
								}
							}
							?>
							<td><?php echo $taction; ?></td>
							<td>
							<?php 
							if ($r['error'] != "") {
								if (strtoupper(substr($r['sql'],0, 6)) == "INSERT" || strtoupper(substr($r['sql'],0, 6)) == "UPDATE" ) {
									echo "<b>".$r['sql']."</b><br>".$r['error'];
								} else {
									_e('The query failed','database_tables');
								}
							} else if ($r['result']  === false) {
								if (strtoupper(substr($r['sql'],0, 6)) == "INSERT" || strtoupper(substr($r['sql'],0, 6)) == "UPDATE" ) {
									echo "<b>".$r['sql']."</b><br>".__('The query failed','database_tables');
								} else {
									_e('The query failed','database_tables');
								}
							}
							?></td>
						</tr><?php 
						}
					?>
					</tbody>
					</table>
				
				<?php endif; ?>
				</div>
				<?php 
				$html_result[] = ['html'=>ob_get_clean(), 'table'=>$ti->table, 'uniqid'=>$uniqid_key];
			} else {
				$html_result[] = ['html'=>'<div class="dtf-alert-info">' . $ti->table . '</div><div class="dtf-alert-sql-error">'.__('The insertions cannot be performed for the primary key in the table','database_tables').'</div>', 'table'=>$ti->table, 'uniqid'=>$uniqid_key];
			}
		}
		foreach ($table_insert as $ti) {
			$table_temp = substr($ti->table,0,57)."__temp";
			$wpdb->query('DROP TEMPORARY TABLE IF EXISTS '.$table_temp);
		}
		wp_send_json(['html'=>$html_result,'result'=>'ok']);
		die;
		//var_dump ($table_insert);
	}


	/**
	 * Import reale del csv
	 */
	public function dbt_import_csv_data() {
		global $wpdb;
		/**
		 * @var Array $table_insert  [['table'=>'','fields'=>[]],[]] dice quali campi del csv deve importare in quali campi delle tabelle
		 */
		$table_insert = [];
		/**
		 * @var Array $html_result;
		 */
		$response = ['result'=>'ok','row'=>'','insert'=>(int)$_REQUEST['insert'],'update'=>(int)$_REQUEST['update'], 'errors'=>(int)$_REQUEST['errors'], 'break'=>0];

		
		Dbt_fn::require_init();
		$import_tables = @$_REQUEST['import_table'];
		$import_unique = @$_REQUEST['import_unique'];
		
		if (!is_array($import_tables) || count($import_tables) == 0 ) {
			wp_send_json(['result'=>'no','msg'=>__('You must select at least one table before you can import data','database_tables')]);
			die;
		}
		$temporaly_files = new Dbt_temporaly_files();
		$csv_filename = $_REQUEST['csv_temporaly_filename'];
		$csv_delimiter = $_REQUEST['csv_delimiter'];
		$csv_first_row_as_headers = Dbt_fn::get_request('csv_first_row_as_headers', false, 'boolean');
		$table_insert  = Dbt_fn_import::convert_csv_data_request_to_vars($import_tables, $_REQUEST['import_field'], $_REQUEST['import_csv_column']);
		
		$csv_items = $temporaly_files->read_csv($csv_filename, $csv_delimiter, $csv_first_row_as_headers);
		// Se ha la prima colonna come intestazioni, altrimenti ho aggiunto riga di intestazioni fittizie
		array_shift($csv_items); 
		$response['total_row'] = count($csv_items);
		$wpdb->query("SET sql_mode = '';");
		// importo i csv

		//var_dump ($table_insert);
		//print "\n\n\n";
		//var_dump ($_REQUEST);
		$count_row = 0;
		$total_row_executed = isset($_REQUEST['total_row_executed']) ? (int)$_REQUEST['total_row_executed'] : 0;
		foreach ($table_insert as $uniqid_key => $ti) {
			$primary_key = Dbt_fn::get_primary_key($ti->table);
			if ($primary_key && $primary_key != "" ) {
				$results = [];
				//$columns = Dbt_fn::get_table_structure($ti->table);
				foreach ($csv_items as $key_csv_items=>$item) {
					$count_row ++;
					if ($count_row < $total_row_executed) continue;
					
					if (isset($import_unique[$uniqid_key])) {
						$name_new_column_csv = $import_unique[$uniqid_key]; 
					} else {
						continue;
					}
					if (array_key_exists('__dbt_datatable_import_result', $item) && $item['__dbt_datatable_import_result'] == 'error') {
						// se la riga ha dato errore, non eseguo eventuali altre query su quella stessa riga.
						continue;
					}
					
					if (array_key_exists($name_new_column_csv, $item) ) {
						// se per una determinata tabella è stata già eseguita la query e quindi c'è già un id scritto non devo rieseguirla!
						continue;
					}
					PinaCode::set_var('key', $key_csv_items) ;
					$array_insert = [];
					// ciclo i campi da importare
					PinaCode::set_var('item', $item) ;
					foreach ($ti->fields as $table_column => $csv_column) {
						if ($csv_column != "") {
							$array_insert[$table_column] = PinaCode::execute_shortcode(stripslashes($csv_column));
						}
					}
					$array_insert = array_filter($array_insert);
					if (count($array_insert) > 0) {
						$temp = Dbt_fn_import::wpdb_replace( $ti->table, $array_insert, $primary_key);
						$results[] = $temp;
						$csv_items[$key_csv_items][$name_new_column_csv] = $temp['id'];
						// insert/update 
						$response[$temp['action']]++;
						$csv_items[$key_csv_items]['__dbt_datatable_import_result'] = $temp['action'];
						if ($temp['error'] != "" || $temp['result'] === false) {
							$csv_items[$key_csv_items]['__dbt_datatable_import_result'] = 'error';
							/*
							// Wordpress bug: sbaglia a tornare le query
							if ($temp['error'] != "") {
								$csv_items[$key_csv_items][$name_new_column_csv]  =  $temp['error'];
							} else {
								$csv_items[$key_csv_items][$name_new_column_csv]  = __('The query failed: '.$temp['sql'], 'database_tables');
							}
							*/
							$response['errors']++;
							//break;
						}
						if (!Dbt_fn::get_max_execution_time()) {
							$response['break'] = 1;
							break;
						}
					}
				}
			} else {
				$response['last_error'] = __('The insertions cannot be performed for the primary key in the table','database_tables');
			}
		}
	
		$response['total_row_executed'] = $count_row;
		$temporaly_files->store_csv($csv_items, $csv_filename, $csv_delimiter);
		wp_send_json($response);
		die;
	}


	/**
	 * Scarica il report delle multiqueri
	 */
	public function dbt_download_csv_report() {
		Dbt_fn::require_init();
		$temporaly_files = new Dbt_temporaly_files();
		header('Content-Description: File Transfer');
		header("Content-Type: application/csv") ;
		header("Content-Disposition: attachment; filename=\"".$_REQUEST['filename']."_".date('Y_m_d_Hi').".csv\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $temporaly_files->read($_REQUEST['filename']);
		die;
	}

	/**
	 * Prepara la lista di colonne da inserire nel select in cui configurare quali colonne del csv vengono associati a quali campi del db
	 */
	private function prepare_columns() {
		$temporaly_files = new Dbt_temporaly_files();
		$csv_filename = $_REQUEST['csv_temporaly_filename'];
		$csv_delimiter = $_REQUEST['csv_delimiter'];
		$csv_first_row_as_headers = Dbt_fn::get_request('csv_first_row_as_headers', false, 'boolean');
		$csv_items = $temporaly_files->read_csv($csv_filename, $csv_delimiter, $csv_first_row_as_headers, 3);
		$csv_item = reset($csv_items);
		$k = 0;
		foreach ($csv_items as $cssv) {
			$k++;
			if ($k == 2) {
				foreach ($cssv as $keeey=>$csssv) {
					$append_p = (strlen($csssv) > 40) ? ' ...' : '';
					$csv_item[$keeey] .= " (".substr($csssv, 0, 40).$append_p.")";
				}
			}
			if ($k >= 2) break;
		}
		
		$csv_columns = [''=>__('Don\'t insert','database_tables')];
		foreach ($csv_item as $key=>$cc) {
			if ($key != "") {
				$csv_columns[$key] = $cc;
			}
		} 
		$csv_columns['__[custom]__'] = __('[Custom Text]','database_tables');
		return $csv_columns;
	}
	/**
	 * Prepara la lista di colonne da inserire nel select in cui configurare quali colonne del csv vengono associati a quali campi del db
	 */
	private function prepare_columns_convert() {
		$temporaly_files = new Dbt_temporaly_files();
		$csv_filename = $_REQUEST['csv_temporaly_filename'];
		$csv_delimiter = $_REQUEST['csv_delimiter'];
		$csv_first_row_as_headers = Dbt_fn::get_request('csv_first_row_as_headers', false, 'boolean');
		$csv_items = $temporaly_files->read_csv($csv_filename, $csv_delimiter, $csv_first_row_as_headers, 3);
		$csv_item = reset($csv_items);
		foreach ($csv_item as $key=>$val) {
			$csv_item['col_'.$key] = $val;
		}
		foreach ($csv_item as $key=>$val) {
			$csv_item[Dbt_fn::convert_to_mysql_column_name($key)] = $val;
			$csv_item[strtolower(substr(Dbt_fn_structure::clean_column_name($key),0 , 64))] = $val;
			$csv_item[substr(Dbt_fn_structure::clean_column_name($key),0 , 64)] = $val;
		}
		return $csv_item;
	}
}