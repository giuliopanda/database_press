<?php
/**
 * Gestisce la creazione dei parametri per una form
 * La classe si s
 * @example new Dbt_class_form()->set_sql('select ...')->get_form();
 */
namespace DatabaseTables;

class Dbt_class_form {
	/** @var \database_tables_model_base $table_model */
    private $table_model;
	/** @var array $add_fields_info Sono tutti i campi aggiunti nella query */
    private $add_fields_info;
	/** @var array $all_pri_ids Sono tutte le chiavi primarie delle tabelle interessate nella query */
    private $all_pri_ids;
	/** @var int $dbt_id */
    private $dbt_id;
	/** @var array $items L'elenco dei dati caricati dalla query */
	private $items;
	/** @var array $where_precompiled_temp */
	private $where_precompiled_temp;
	/**
	 *
	 * @param int|string $param dbt_id | sql
	 */
	public function __construct($param) 
	{
		if (is_numeric($param)) {
			$this->dbt_id = $param;
			$post  = Dbt_functions_list::get_post_dbt($this->dbt_id);
			$sql =  @$post->post_content['sql'];
			$this->prepare_table_model($sql);
		} else if (is_string($param) && stripos($param, 'select') !== false) {
			$this->prepare_table_model($param);
		}
	}


	/**
	 * Carica i dati di una form a partire dagli id.
	 *
	 * @param array $ids
	 * @return void
	 */
    public function get_data($ids) {
		if (!is_array($ids) && !is_object($ids)) {
			return [];
		}
		foreach ($ids as $column => $id) {
			$column = str_replace("`", "", $column );
			$column = "`".str_replace(".", "`.`", $column )."`";
			$filter[] = ['op' => "=", 'column' => $column, 'value' => $id];
		}
		$this->table_model->list_add_where($filter);
		// Metto il limite a 50 perché c'è il limite di 1000 per i post
		$this->table_model->list_add_limit(0,50);
		$this->items = $this->table_model->get_list();
		if (count($this->items) > 0) {
			array_shift($this->items);
		}
        return $this->items;
    }

    /**
     * crea tutti i parametri per gestire una form
     *
     * @param bool $logic serve per la generazione dei form, se false invece lo si usa per il form list-form
     * @return Array [$settings, $table_options]
     */
    public function get_form($logic = true) {
		$schema = $this->table_model->get_schema();
		$new_schema = $this->convert_table_to_group($schema);
		//	print "<pre>";
		//	var_dump ($new_schema);
		//	die;
		$this->where_precompiled_temp =  $this->table_model->get_default_values_from_query(); 
		//var_dump ($this->where_precompiled_temp);
		$settings = $this->convert_schema_to_setting($new_schema);
		$settings = $this->add_where_precompiled_to_settings($settings);
		// ATTENZIONE table_options ora è un array di table_options!
		$table_options = $this->get_table_options($new_schema, $logic);
		
		if (!empty($this->dbt_id)) {
			$post = Dbt_functions_list::get_post_dbt($this->dbt_id);
			$settings = $this->convert_post_content_form_to_setting($post->post_content, $settings);    
			$table_options = $this->convert_post_content_form_to_table_options($post->post_content, $table_options);
			foreach ($settings as &$setting) {
				uasort($setting, function ($a, $b) { return  $a->order - $b->order; });
			}
		}
		return [$settings, $table_options];
		//TODO questa sparisce perché il risultato di questa classe sarà array di classi
	}

	/**
	 * Preparo la query per la gestione del form inserendo tutti i campi
	 * Genero il table_model e le variabili: all_pri_ids e add_fields_info
	 *
	 * @return void
	 */
	private function prepare_table_model($sql) {
		$this->table_model = new Dbt_model();
		$this->table_model->prepare($sql);
		list($all_pri_ids, $add_fields_info) = $this->add_all_fields($this->table_model);
		$this->all_pri_ids = $all_pri_ids;
		$this->add_fields_info = $add_fields_info;
	}

	/**
	 * Aggiunge tutti i campi alla query
	 *
	 * @param \database_tables_model_base $table_model
	 * @return Array [all_pri_ids , all_fields]
	 */
	private function add_all_fields(&$table_model) {
        $current_query_select = $table_model->get_partial_query_select();
        $schema = $table_model->get_schema();
        // Preparo i dati:
        // e raggruppo i campi per tabella
        $all_pri_ids = [];
		$all_fields = [];
        $field_group = [];
        foreach ($schema as $sc) {
            if ($sc->orgtable != "") {
                if (!array_key_exists($sc->orgtable, $all_pri_ids)) {
					$all_fields[$sc->orgtable] = Dbt_fn::get_table_structure($sc->orgtable);
					 // mi segno la chiave primaria delle tabelle
                    $all_pri_ids[$sc->orgtable] = $this->get_primary_form_structure($all_fields[$sc->orgtable]);
                }
                // Raggruppo i campi per tabella (alias)
                if ($sc->table != "") {
                    if (!isset($field_group[$sc->table])) {
                        $field_group[$sc->table] = ['table'=>$sc->orgtable, 'alias_table'=>$sc->table, 'fields'=>[]];
                    }
                    $field_group[$sc->table]['fields'][] = $sc;
                }
            }
        }
        $all_pri_ids = array_filter($all_pri_ids);
  
        // verifico se c'è la chiave primaria, oppure segno che deve essere aggiunta
        $add_select_pri = [];
		$add_fields_info = [];
        foreach ( $field_group as $group) {
            if (isset($all_fields[$group['table']])) {
                //aggiungo tutti i campi che non esistono
				foreach ($all_fields[$group['table']] as $af) {
					$exist = false;
					foreach ($group['fields'] as $fields) {
						if ($fields->orgname == $af->Field) {
							$exist = true;
							break;
						}
					} 
					if (!$exist) {
						$alias = Dbt_fn::get_column_alias($group['alias_table']."_".$af->Field, $current_query_select);
						$add_select_pri[] =  '`'. $group['alias_table'].'`.`'.$af->Field.'` AS `'.$alias.'`';
						$current_query_select .= " ".$alias;
						$add_fields_info[] = ['table' => $group['alias_table'], 'orgname' => $af->Field, 'name'=> $alias];

					}
                }
            }
        }
        // aggiungo i nuovi select, ripeto la query e aggiorno table_items
        if (count($add_select_pri) > 0) {
            $table_model->list_add_select(implode(", ", $add_select_pri));
        }
		return [$all_pri_ids , $add_fields_info];
    }

	/**
	 * Estrae le chiave primarie
	 *
	 * @param array $columns
	 * @return void
	 */
	function get_primary_form_structure($columns) {
		$primary = '';
        $autoincrement = false;
        foreach ($columns as $col) {
            if ($col->Key == "PRI") {
                if ($primary == "") {
                    $primary = $col->Field;
                    if ($col->Extra == "auto_increment") {
                        $autoincrement = true;
                    }
                } else {
                    return '';
                }
            }
        }
        if ($autoincrement) {
            return $primary;
        } else {
            return '';
        }
	}

	/**
	 * I valori di values sono gestiti nello stesso formato di get_data
	 *
	 * @param array $values
	 * @return array
	 * ```json
	 * {"execute":"boolean", "details":"array}
	 * ```
	 */
	public function save_data($values) {
		global $wpdb;
		list($settings, $table_options) = $this->get_form();
        $items_groups = $this->convert_items_to_groups($values, $settings, $table_options);
		$query_to_execute = [];
		foreach ($items_groups as $items) {
			//print "<h3>ITEMS</h3><pre>";
			//var_dump ($items);
			//print "</pre>";
			foreach ($settings as $key=>$setting) {
				// trovo la tabella e la chiave primaria
				$table = "";
				$table_alias = "";
				$primary_name = "";
				$primary_value = "";
				$sql_to_save = [];
				foreach ($setting as $ss) {
					if ($ss->is_pri) {
						$table = $ss->orgtable;
						$table_alias = $ss->table;
						$primary_name = $ss->name;
					}
				}
				$pri_name = Dbt_fn::clean_string($table_alias).'.'.Dbt_fn::clean_string($primary_name);
				
				// Preparo gli array di modifica dei dati
				if (array_key_exists($key, $items) && $table != "" && $primary_name != "") {
					// salvo la tabella
					foreach ($items[$key] as $val_key => $val_value) {
						if (!array_key_exists($val_key, $setting) || $setting[$val_key]->name == "_dbt_alias_table_") {
							continue;	
						}
						if ($setting[$val_key]->name == $primary_name) {
							$primary_value = $val_value;
						} else {
							if (is_countable($val_value)) {
								$fn[$key] = maybe_serialize($val_value);
							}
							$sql_to_save[$setting[$val_key]->name] = $val_value;
							PinaCode::set_var(Dbt_fn::clean_string($table_alias).".".Dbt_fn::clean_string($setting[$val_key]->name), $val_value);
							
						}
					}
				
					$exists  = 0;
					if ($primary_value != "") {
						$exists = $wpdb->get_var('SELECT count(*) as tot FROM `'.$table.'` WHERE `'.esc_sql($primary_name).'` = \''.esc_sql($primary_value).'\'');
						if ($exists == 0) {
							$sql_to_save[$primary_name] = $primary_value;
						}
					} 
					if (count($sql_to_save) > 0) {
						if ($exists == 1) {
							$query_to_execute[] = ['action'=>'update', 'table'=>$table, 'sql_to_save'=>$sql_to_save, 'id'=> [$primary_name=>$primary_value], 'table_alias'=>$table_alias, 'pri_val'=>$primary_value, 'pri_name'=>$primary_name, 'setting' => $setting];
							
						} else {
							$query_to_execute[] = ['action'=>'insert', 'table'=>$table, 'sql_to_save'=>$sql_to_save, 'table_alias'=>$table_alias, 'pri_val'=>$primary_value, 'pri_name'=>$primary_name, 'setting' => $setting];
						}
					}
				}
			}
		}
		$ris =  Dbt_functions_list::execute_query_savedata($query_to_execute, $this->dbt_id, 'php-form');
		$execute = true;
		foreach ($ris as $r) {
			if (!($r['result'] == true || ($r['result'] == false && $r['error'] == "" && $r['action']=="update"))) {
				$execute = false;
				break;
			}
		}
		return ['execute' => $execute, 'details' => $ris];
	}


	/**
	 * Trova una riga dei setting a partire dall'alias della tabella e dal nome(vero) del campo
	 * @param \DbtDs_field_param[][] $settings
	 * @param string $table_alias 
	 * @param string $field 
	 * @return \DbtDs_field_param
	 */
	static public function find_setting_row_from_table_field($settings, $table_alias, $field) {
		foreach ($settings as $setting) {
			foreach ($setting as $row) {
				if ($row->name == $field && $row->table == $table_alias) {
					return $row;
				}
			}
		}
		return false;
	}
	/**
	 * Trova il setting di un salvataggio a partire dal table_alias
	 * @param \DbtDs_field_param[][] $settings
	 * @param string $table_alias 
	 * @return \DbtDs_field_param
	 */
	public function find_setting_from_table_field($table_alias) {
		list($settings, $_) = $this->get_form();
		foreach ($settings as $setting) {
			foreach ($setting as $row) {
				if ($row->table == $table_alias) {
					return $setting;
				}
			}
		}
		return false;
	}

	/**
	 * Nella visualizzazione dei dati e modifica, se c'è una query che ritorna più righe di risultato 
	 * cerco di raggrupparli per tabelle
     * @param Array $table_items il risultato di model->get_list
	 * @return Array in cui ogni item è il risultato di una tabella
	 */
	private function convert_table_to_group($schemas) {
		// Divido in gruppi a seconda della tabella
		$temp_groups = [];
		foreach ($schemas as $schema) {
		//	print "\n".$schema->table."\n";
			if ($schema->table != "") {
				if (!isset($temp_groups[$schema->table])) {
					$temp_groups[$schema->table] = [];
				}
				$temp_groups[$schema->table][$schema->name] = $schema;
				
			} else {
				//if (!isset($temp_groups['__orphan__'])) {
				//	$temp_groups['__orphan__'] = [];
				//}
				//$temp_groups['__orphan__'][$schema->name] =  '';
			}
		}
		$count_group = 0;
		$items = [];
		foreach ($temp_groups as $key=>$group) {
			$count_group++;
			//if ($key != '__orphan__') {
				$items["gr".$count_group] = $group;
		//	} else  {
				//$items[$key] = $group;
		//	}
		}
		return $items;
	}

	/**
	 * Converte i dati dallo schema a DbtDs_field_param
	 *
	 * @param array $new_schema
	 * @return DbtDs_field_param[]
	 * {'grxx':{'table_alias':DbtDs_field_param	}}
	 */
	private function convert_schema_to_setting($new_schema) {
		$table_params = [];
		$count_form_block = 0;

		foreach ($new_schema as $key => $group) {
			$count_form_block++;
			foreach ($group as $field => $schema) {
				$edit_view = 'SHOW';
				$form_type = 'VARCHAR';
				$is_pri = 0;
				foreach ($this->add_fields_info as $afi) {
					//var_dump ($afi);
					if ($afi['table'] == $schema->table && $afi['orgname'] == $schema->orgname && $afi['name'] == $schema->name) {
						//$form_type = 'HIDDEN';
						$edit_view = 'HIDE';
					} 
				}
				
				if ($this->all_pri_ids[$schema->orgtable] == $schema->orgname) {
					$form_type = 'PRI';
					$is_pri = 1;
				} else if ($form_type == "VARCHAR") {
					$form_type = Dbt_fn::h_type2txt($schema->type);
					if ($form_type == "DATE") {
						$temp_type_2 = Dbt_fn::h_type2txt($schema->type, false);
						if ($temp_type_2 == "DATETIME" || $temp_type_2 == "DATE") {
							$form_type = $temp_type_2;
						}
					} 
				}

				$table_params[$key][$field] = new DbtDs_field_param([
					'name' => $schema->orgname,
					'orgtable'=> $schema->orgtable,
					'table'=> $schema->table,
					'label'=> $schema->name,
					'type' => $schema->type,
					'is_pri' => $is_pri,
					'edit_view' => $edit_view,
					'field_name' => "edit_table[".$count_form_block."][".$schema->orgtable."][".$schema->orgname."][]",
					'form_type'=> $form_type
				]);
			}
			$table_params[$key]["_dbt_alias_table_"] = new DbtDs_field_param([
                'name' =>  "_dbt_alias_table_",
                'table'=> $schema->orgtable,
                'field_name' => "edit_table[".$count_form_block."][".$schema->orgtable."][_dbt_alias_table_][]",
				'form_type'=>  "HIDDEN",
				'default_value' =>  $schema->table
			]);
		}
		
		return $table_params;
	}

	/**
	 * Table options 
	 * Ho aggiunto un livello di array perché in teoria potrebbero esserci più righe di items (mai testato!)
	 *
	 * @param array $new_schema
	 * @param bool $logic se true gestisce la creazione del form se false i parametri per list-form
	 * @return array [\DbtDs_table_param[]]
	 */
	private function get_table_options($new_schema, $logic) {
		
		if (empty($this->items)) {
			$items = [false];
		} else {
			$items = $this->items;
		}
		//TODO 	qui dovrei definire se è un edit o un add!
		//print "COUNT ITEMS: ".count ($items);
		foreach ($items as $item) {
			$table_options_temp = [];
			$count_form_block = 0;
			
			foreach ($new_schema as $key => $schema) {
				$kschema = '';
				foreach ($schema as $kschema=>$v) {
					if ($v->table != '') break;
				}
				$count_form_block++;
				$table_options_temp[$key] = new DbtDs_table_param();
				[$pri_orgname, $pri_name] = $this->get_primary_alias($schema);
				$table_options_temp[$key]->pri_name = $pri_name;
				$table_options_temp[$key]->pri_orgname = $pri_orgname;
				$table_options_temp[$key]->count_form_block = $count_form_block;
		
				$table_options_temp[$key]->table = $schema[$kschema]->table;
				$table_options_temp[$key]->orgtable = $schema[$kschema]->orgtable;
				if ($logic) {
					
					if (!empty($item) && isset($item->$pri_name) && $item->$pri_name > 0) {
						$table_options_temp[$key]->pri_value = $item->$pri_name;
						$table_options_temp[$key]->allow_create = 'HIDE';
					} else {
						$table_options_temp[$key]->allow_create = 'SHOW';
					}
					$table_options_temp[$key]->set_rand_frame_style();
					if ($count_form_block == 1) {
						$table_options_temp[$key]->allow_create = 'HIDE';
						$table_options_temp[$key]->frame_style = 'WHITE';
					} else if (!empty($item)) {
						if ($pri_name != "") {
							// il campo non è stato ancora inserito
							$table_options_temp[$key]->table_compiled = "edit_table[".$count_form_block."][".$schema[$kschema]->orgtable."][_dbt_leave_empty_][]";
						} 
						
					} 
				} else {
					$table_options_temp[$key]->allow_create = 'SHOW';
					if ($count_form_block == 1) {
						$table_options_temp[$key]->frame_style = 'WHITE';
					} else {
						$table_options_temp[$key]->set_rand_frame_style();
					}
				}
				$option = Dbt_fn::get_dbt_option_table($schema[$kschema]->orgtable);
				$table_options_temp[$key]->table_status = $option['status'];
			}
			$table_options[] = $table_options_temp;
		}
		return $table_options;
	}

	/**
	 * Trova la chiave primaria della query (quindi con il nome della colonna della query) 
	 * @param array $new_schema
	 * @return array [pri_orgname, pri_name]
	 */
	private function get_primary_alias($new_schema) {
		reset ($new_schema);
		$key_schema = key($new_schema);
		$table = $new_schema[$key_schema]->table;
	
		if ($table == "") return '';
		if (isset($this->all_pri_ids[$new_schema[$key_schema]->orgtable])) {
			$pri = $this->all_pri_ids[$new_schema[$key_schema]->orgtable];
		}
		if ($pri != "") {
			foreach ($new_schema as $k=>$schema) {
				if ($schema->orgname == $pri) {
					return [$pri, $schema->name];
				}
			}
		}
		return '';
	}

	/**
	 * Se è una lista carico i dati dalla lista!
	 *
	 * @param [array] $post_content
	 * @param DbtDs_table_param[] $settings
	 * @return DbtDs_table_param[]
	 */
	private function convert_post_content_form_to_table_options($post_content, $table_options) {
		if (array_key_exists('form_table', $post_content)) {
			foreach ($table_options as &$item_row) {
				foreach ($item_row as &$group) {
					$this->convert_post_content_form_to_table_options_single($post_content, $group);
				}
			}
			
		}
		return $table_options;
	}

	private function convert_post_content_form_to_table_options_single($post_content, &$group) {
		$form_field = false;
		$form_field2 = [];
		foreach ($post_content['form_table'] as $table => $form_field2) {	
			if ($table == $group->table) {
				$form_field = $form_field2;
				break;
			}
		}
		if (!$form_field) return false;
		$group->allow_create = $form_field["allow_create"];
		$group->show_title = $form_field["show_title"];
		$group->title = $form_field["title"];
		$group->frame_style = $form_field["frame_style"];
		$group->description = $form_field["description"];
		$group->module_type = $form_field["module_type"];

		$primary_key = Dbt_fn::get_primary_key($group->orgtable);
		foreach ($this->where_precompiled_temp as $wpt) {
			if ($group->table == $wpt[0] && $primary_key == $wpt[1]) {
				$group->precompiled_primary_id = $wpt;
			}
		}
	}

	/**
	 * Aggiunge i campi precompilati dalla query 
	 *
	 * @param DbtDs_field_param[] $settings
	 * @return DbtDs_field_param[]
	 */
	private function add_where_precompiled_to_settings($settings) {
		
		foreach ($settings as $group) {
			foreach ($group as $field) {
				if ($field->orgtable != "") {
					$field->js_rif = str_replace(" ","_", $field->table.".".$field->name);
					$primary_key = Dbt_fn::get_primary_key($field->orgtable);
					// campi calcolati dalla query
					foreach ($this->where_precompiled_temp as $wpt) {
						//print "<h2>".$field->table." == ".$wpt[0]."</h2>";
						if ($field->table == $wpt[0] && $field->name == $wpt[1] && $primary_key != $field->name) {
							if (empty($this->dbt_id)) {
								$field->form_type = "VARCHAR";
								$field->default_value = $wpt[2];
							} else {
								$field->form_type = "CALCULATED_FIELD";
								$field->custom_value = $wpt[2];
							}
							$field->where_precompiled = 1;
						}
					}	
				}
			}
		}
		return $settings;
		
	}
	/**
	 * Se è una lista carico i dati dalla lista!
	 *
	 * @param [type] $post_content
	 * @param DbtDs_field_param[] $settings
	 * @return DbtDs_field_param[][]
	 */
	private function convert_post_content_form_to_setting($post_content, $settings) {
		
		if (array_key_exists('form', $post_content)) {
			foreach ($settings as $group) {
				foreach ($group as $field) {
					$this->convert_post_content_form_to_setting_single($post_content, $field);
				}
			}
		}
		return $settings;
	}

	/**
	 * Funzione di servizio per la conversione dei setting dal form imposta i singoli campi con i parametri salvati in una lista
	 *
	 * @param [type] $post_content
	 * @param \DbtDs_field_param $field
	 * @return void
	 */
	private function convert_post_content_form_to_setting_single($post_content, &$field) {

		$form_field = false;
		foreach ($post_content['form'] as $form_field2) {
			//print ("<p>" . $form_field2['table']." == ".$field->table." && ".$form_field2['name']." == ".$field->name  . "</p>");
			if ($form_field2['table'] == $field->table && $form_field2['name'] == $field->name  ) {
				$form_field = $form_field2;
				break;
			}
		}

		if (!$form_field) return false;
	
		
		//print ("<h4>trovato!!</h4>");
		$field->order = $form_field['order'];
		if (isset($form_field['label']) && $form_field['label'] != "") {
			$field->label = $form_field['label'];
		} else {
			$field->label = $form_field['name'];
		}
		if (isset($form_field['note'])) {
			$field->note = $form_field['note'];
		}
		if (isset($form_field['options'])) {
			$field->options = Dbt_fn::parse_csv_options($form_field['options']);
		}

		if (isset($form_field['default_value'])) {
			$field->default_value = $form_field['default_value'];
		}
		if (isset($form_field['required'])) {
			$field->required = $form_field['required'];
		}
		if (isset($form_field['custom_css_class'])) {
			$field->custom_css_class = $form_field['custom_css_class'];
		}
		if (isset($form_field['custom_value'])) {
			$field->custom_value = $form_field['custom_value'];
		}
		if (isset($form_field['form_type'])) {
			$field->form_type = $form_field['form_type'];
			if ( $field->form_type == "CREATION_DATE") {
				if ($field->Type == "date") {
					$field->default_value = (new \DateTime('now',  wp_timezone()))->format('Y-m-d');
					$field->custom_value = (new \DateTime('now',  wp_timezone()))->format(get_option('date_format'));
				} else {
					$field->default_value = (new \DateTime('now',  wp_timezone()))->format('Y-m-d H:i:s');
					$field->custom_value = (new \DateTime('now',  wp_timezone()))->format(get_option('date_format')." ".get_option('time_format'));
				}
			}
			if ( $field->form_type == "LAST_UPDATE_DATE") {
				if ($field->Type == "date") {
					$field->default_value = (new \DateTime('now',  wp_timezone()))->format('Y-m-d');
					$field->custom_value = (new \DateTime('now',  wp_timezone()))->format(get_option('date_format'));
				} else {
					$field->default_value = (new \DateTime('now',  wp_timezone()))->format('Y-m-d H:i:s');
					$field->custom_value = (new \DateTime('now',  wp_timezone()))->format(get_option('date_format')." ".get_option('time_format'));
				}
			}
			if ( $field->form_type == "RECORD_OWNER" || $field->form_type == "MODIFYING_USER" ) {
				$field->default_value = get_current_user_id();
				if ($field->default_value == 0) {
					$field->custom_value = __('Guest');
				} else {
					$user = wp_get_current_user();
					$field->custom_value = $user->user_login;
				}
			}
			if ( $field->form_type == "CALCULATED_FIELD") {
				if (isset($form_field['where_precompiled'])) {
					$field->where_precompiled =  $form_field['where_precompiled'];
				} else {
					$field->where_precompiled = 0;
				}
			} elseif (isset($field->where_precompiled)) {
				$field->where_precompiled = 0;
			}

			if ( $field->form_type == "POST") {
				$field->post_types = $form_field['post_types'];
				$field->post_cats = $form_field['post_cats'];
			}
			if ( $field->form_type == "USER") {
				$field->user_roles = $form_field['user_roles'];
			}
			if ( $field->form_type == "LOOKUP") {
				$field->lookup_id  = $form_field['lookup_id'];
				$field->lookup_sel_val = $form_field['lookup_sel_val'];
				$field->lookup_sel_txt = $form_field['lookup_sel_txt'];
			}

		}
		if (isset($form_field['edit_view'])) {
			$field->edit_view = $form_field['edit_view'];
		}
		if (isset($form_field['js_script'])) {
			$field->js_script = $form_field['js_script'];
		}

	}



	/**
	 * Converte un array di array di strutture di dati (DbtDs_data_structures) in un array di array di array
	 *
	 * @param array $structures
	 * @return array
	 */
	static public function data_structures_to_array($structures) {
		foreach ($structures as &$to) {
			foreach ($to as &$t) {	
				$t = $t->get_array();
			}
		}
		return $structures;	
	}

	/**
	 * Converte la struttura degli items estratti da get_data nella struttura dei setting
	 *
	 * @param array $items
	 * @param [type] $settings
	 * @return array
	 */
	static public function convert_items_to_groups($items, $settings, $table_options) {
		$new_items = [];
		$temp_already_added = [];
		$temp_old_items = [];
		if (!is_countable($items) || count($items) == 0) {
			foreach ($settings as $ks => $st) {
				$temp_old_items[$ks] = new \stdClass;
				foreach ($st as $tk => $_) {
					$temp_old_items[$ks]->$tk = '';
				}
			}
			$items = [$temp_old_items];
			return $items;
 		} 
		
		foreach ($items as $item_key => $item) {
			$temp_item = [];
			foreach ($settings as $key=>$setting) {
				$added = false;
				foreach ($temp_already_added as $oa) { 
					if ($table_options[$item_key][$key]->table == $oa->table && $table_options[$item_key][$key]->pri_value ==  $oa->pri_value) {
						$added = true;
					}
				}
				if (!$added) {
					$temp_already_added[] = (object)['table' => $table_options[$item_key][$key]->table, 'pri_value' => $table_options[$item_key][$key]->pri_value];
					$temp_item[$key] = [];
					foreach ($setting as $field=>$setting_field) {
						if (property_exists($item, $field)) {
							if ($item->$field == null) {
								$temp_item[$key][$field] = '';
							} else {
								if ($setting_field->form_type == "DATETIME" || $setting_field->form_type == "DATE" ) {
									try {
										$temp = new \DateTime($item->$field, wp_timezone());
									} catch (\Exception $ex) {
										if ($setting_field->form_type == "DATETIME") {
											$item->$field = '0000-00-00 00:00:00';
										} else {
											$item->$field = '0000-00-00';
										}
									}
									if (is_a($temp, 'DateTime')) {
										if ($setting_field->form_type == "DATETIME") {
											$item->$field = $temp->format('Y-m-d\TH:i:s');
										} else {
											$item->$field = $temp->format('Y-m-d');
										}
									}
								}
								if ($setting_field->form_type == "USER") {
									if ($item->$field > 0) {
										$user = get_user_by('ID', $item->$field);
										if ($user) {
											$item->$field = ['id'=>$item->$field, 'label'=>$user->user_login];
										}
									}
								}
								if ($setting_field->form_type == "POST") {
									if ($item->$field > 0) {
										$post = get_post($item->$field);
										if ($post) {
											$item->$field = ['id'=>$item->$field, 'label'=>$post->post_title];
										}
									}
								}

								if ($setting_field->form_type == "LOOKUP") {
									if ($item->$field > 0) {
										$ris = Dbt::get_data($setting_field->lookup_id, 'items', [['op'=>"=",'column'=>$setting_field->lookup_sel_val,'value'=>$item->$field]]);
										
										if (is_array($ris) && count($ris) > 0) {
											$ris = array_shift($ris);
											$label = $setting_field->lookup_sel_txt;
											$item->$field = ['id'=>$item->$field, 'label'=>$ris->$label];
										}
									}
								}
								if ($setting_field->form_type == "MEDIA_GALLERY") {
									if ($item->$field > 0) {
										$attachment = get_post($item->$field);
										$url = wp_get_attachment_image_src($item->$field, 'thumbnail', true);
										$item->$field = ['id'=>$item->$field, 'url'=> $url[0], 'link'=> wp_get_attachment_url($item->$field), 'title'=> $attachment->post_title];
									}
								}

								$temp_item[$key][$field] = $item->$field;
							}
						}
					}
				}
			}
			$new_items[] = $temp_item;	
		}
		return $new_items;
	}
}