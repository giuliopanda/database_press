<?php
/**
 * Queste sono funzioni specifiche per database table
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
/**
 * [^order field="" path="" asc="" desc="" no_sort=""] 
 */
if (!function_exists('pinacode_fn_order')) {
	function pinacode_fn_order($short_code_name, $attributes) { 
		/**
		 * @var $asc il prefisso che assumono le variabili get/post
		 */
		if (isset($attributes['asc'])) {
			$asc = $attributes['asc'];
		} else {
			$asc = '&darr;';
		}

		if (isset($attributes['asc'])) {
			$desc = $attributes['desc'];
		} else {
			$desc = '&uarr;';
		}
		/**
		 * @var $no_sort Se quando non c'è un ordinamento faccio apparire un'icona speciale oppure lascio le due icone di sorting
		 */
		if (isset($attributes['no_sort'])) {
			$no_sort = $attributes['no_sort'];
		} else {
			$no_sort = '&udarr;';
		}
		/**
		 * @var $path è il prefisso per le variabili delle liste
		 */
		if (isset($attributes['path'])) {
			$path = $attributes['path'];
		} else {
			$path = PinaCode::get_var('global.dbt_filter_path');
		}
		/**
		 * @var $field il nome del campo
		 */
		if (isset($attributes['field'])) {
			$field = $attributes['field'];
		} else {
			// ERROR!
			return "";
		}
		/**
		 * @var $link 
		 */
		if (isset($attributes['link'])) {
			$link = $attributes['link'];
		} else {
			$link = get_permalink();
		}
		$selected_asc = $selected_desc = "";
		if (isset($_REQUEST[$path.'_sort'])) {
			if ($_REQUEST[$path.'_sort'] == $field.".asc")  {
				$selected_asc = " dbt-selected-order";
			}
			if ($_REQUEST[$path.'_sort'] == $field.".desc")  {
				$selected_desc = " dbt-selected-order";
			}
		}
		$link = pina_frontend_add_query_args($link, $path, 'sort');
		$link_asc = add_query_arg([$path.'_sort'=> $field.".asc"], $link );
		$link_desc = add_query_arg([$path.'_sort' =>$field.".desc"], $link );
		ob_start();

		if (in_array(strtolower($no_sort),[0,'f','false'. false])) :
			?>
			<a class="dbt-order-link<?php echo $selected_asc; ?>" href="<?php echo $link_asc; ?>"><?php echo $asc; ?></a>
			<a class="dbt-order-link<?php echo $selected_desc; ?>" href="<?php echo $link_desc; ?>"><?php echo $desc; ?></a>
		<?php
		else: 
			if (isset($_REQUEST[$path.'_sort']) && substr($_REQUEST[$path.'_sort'],0 , strlen($field)) == $field ) {
				if ($_REQUEST[$path.'_sort'] == $field.".asc")  {
					?><a class="dbt-order-link" href="<?php echo $link_desc; ?>"><?php echo $desc; ?></a><?php
				} else {
					?><a class="dbt-order-link" href="<?php echo $link_asc; ?>"><?php echo $asc; ?></a><?php
				}
			} else {
				?><a class="dbt-order-link" href="<?php echo $link_asc; ?>"><?php echo $no_sort; ?></a><?php
			}
		endif; 
		
		return ob_get_clean();

	}
}
pinacode_set_functions('html_order', 'pinacode_fn_order');


/**
 * Apre un form per il search setta le variabili per sapere che da quel punto in poi la form è aperta
 *  [^open-search method="get" link="" ] 
 */
if (!function_exists('pinacode_fn_search_open_container')) {
	function pinacode_fn_search_open_container($short_code_name, $attributes) {
		PinaCode::set_var('global.search_container.status', 'open');

		pina_check_attributes($attributes,  ['link'=>'[string]','method'=>['get','post'], 'id'=>'[int]'], $short_code_name);
		
		/**
		 * @var $link 
		 */
		if (isset($attributes['link'])) {
			$link = $attributes['link'];
		} else {
			$link = get_permalink();
		}
		if (isset($attributes['method'])) {
			$method = $attributes['method'];
		} else {
			$method = 'get';
		}
		if (isset($attributes['id'])) {
			PinaCode::set_var('global.dbt_filter_id', $attributes['id']);
		}
	

		ob_start();
		$search_class = new Dbt_search_form();
		$search_class->open_form($link, $method);
		
		return ob_get_clean();	

	}
}
pinacode_set_functions('open-search', 'pinacode_fn_search_open_container');


/**
 * Apre un form per il search setta le variabili per sapere che da quel punto in poi la form è aperta
 *  [^close-search btn=String color=String] 
 */
if (!function_exists('pinacode_fn_search_close_container')) {
	function pinacode_fn_search_close_container($short_code_name, $attributes) {
	
		pina_check_attributes($attributes,  ['btn'=>'[string]','color'=>['red','pink','blue','green','gray','yellow']], $short_code_name);
		if (isset($attributes['btn'])) {
			$search_text = $attributes['btn'];
		} else {
			$search_text ='Search';
		}
		if (isset($attributes['color'])) {
			$color = $attributes['color'];
		} else {
			$color ='blue';
		}

		$attributes = pina_add_class_to_attributes($attributes, 'dbt-search-row');
		$attributes_html = pina_add_html_attributes($attributes);
		ob_start();
		$search_class = new Dbt_search_form();
		$search_class->close_form($search_text, $attributes_html, $color);
		
		return ob_get_clean();	
	}
}
pinacode_set_functions('close-search', 'pinacode_fn_search_close_container');



/**
 * [^search id=Int type=Str name=Str link=Str btn=Str label=Str, values=  color= ] 
 * es: [^search type="select" name="publish" values=[%{"1":"Yes", "0": "No"}] ]
 */
if (!function_exists('pinacode_fn_search')) {
	function pinacode_fn_search($short_code_name, $attributes) {
		$global_status = PinaCode::get_var('global.search_container.status');
		$search_class = new Dbt_search_form();
		if (isset($attributes['values'])) {
			$attributes['values'] = PinaCode::get_registry()->short_code($attributes['values']);
		} 
		if (isset($attributes['value'])) {
			$attributes['value'] = PinaCode::get_registry()->short_code($attributes['value']);
		} 
		pina_check_attributes($attributes,  ['type'=>['input','select','checkbox','date'],'name'=>'[string]','btn'=>'[string]', 'label'=>'[string]', 'values'=>'[array]', 'value'=>'[mixed]', 'id'=>'[numeric]'], $short_code_name);
		/**
		 * @var $name il nome della variabile senza dbtXX_
		 */
		if (isset($attributes['name'])) {
			$name = $attributes['name'];
		} else {
			$name = 'search';
		}
		/**
		 * @var $type Il tipo di form
		 */
		if (isset($attributes['type'])) {
			$type = $attributes['type'];
		} else {
			$type = 'text';
		}
		
		/**
		 * @var $link 
		 */
		if (isset($attributes['link'])) {
			$link = $attributes['link'];
		} else {
			$link = get_permalink();
		}

		
		/**
		 * @var $btn Il testo del bottone search
		 */
		if (isset($attributes['btn'])) {
			$search_text = $attributes['btn'];
		} else {
			$search_text = 'Search';
		}
		
		if (isset($attributes['label'])) {
			$label = $attributes['label'];
		} else {
			$label = '';
		}
		/**
		 * @var $path è il prefisso per le variabili delle liste
		 */
		if (isset($attributes['id'])) {
			$id = $attributes['id'];
		} else {
			$id = PinaCode::get_var('global.dbt_filter_id');
		}
		$search_class->set_post_id($id);
	
		if (isset($attributes['color'])) {
			$attributes = pina_add_class_to_attributes($attributes, 'dbt-children-color-'.$attributes['color']);
		} else {
			$attributes = pina_add_class_to_attributes($attributes, 'dbt-children-color-blue');
		}

		$attributes = pina_add_class_to_attributes($attributes, 'dbt-search-row');
		$attributes_html = pina_add_html_attributes($attributes);

		ob_start();
		if ($type == "select" )  {
			if (isset($attributes['values'])) {
				$options =$attributes['values'];
			} else {
				$options = "distinct";
			}
			$search_class->one_field_select_form($link, 'get', 'dbt'.$id.'_'.$name, $options, __($search_text, "database_tables") , $attributes_html, $label);
		} elseif ($type == "checkbox" )  {
			if (isset($attributes['value'])) {
				$value = $attributes['value'];
			} else {
				$value = 1;
				$msg = sprintf('%s checkbox: The "value" attribute is required ', $short_code_name);
				PcErrors::set($msg , $short_code_name,-1, 'warning');
			}
			if (!isset($attributes['label'])) {
				if (is_string($value)) {
					$label = $value;
				}
			}
			$search_class->one_field_checkbox_form($link, 'get', 'dbt'.$id.'_'.$name, $value, __($search_text, "database_tables") , $attributes_html, $label);
		} else {
			$search_class->one_field_input_form($link, 'get', 'dbt'.$id.'_'.$name, __($search_text, "database_tables") , $attributes_html, $label);
		}
		return ob_get_clean();
	}
}
pinacode_set_functions('search', 'pinacode_fn_search');



/**
 * [^pagination id=Int limit=Int total_items=Int link=Str btn=Str label=Str, values=] 
 * [^pagination id=1 limit=10 total_items=120]
 */
if (!function_exists('pinacode_fn_dbt_pagination')) {
	function pinacode_fn_dbt_pagination($short_code_name, $attributes) {
		
		$search_class = new Dbt_search_form();
		pina_check_attributes($attributes,  ['id'=>'[numeric]', 'link'=>'[string]', 'total_items'=>'[numeric]', 'limit'=>'[numeric]','method'=>'[string]','style'=>'[string]','color'=>'[string]' ], $short_code_name, ['limit', 'total_items']);
		/**
		 * @var $id l'id della lista
		 */
		if (isset($attributes['id'])) {
			$id = $attributes['id'];
		} else {
			$id = PinaCode::get_var('global.dbt_filter_id');
		}
		/**
		 * @var $link 
		 */
		if (isset($attributes['link'])) {
			$link = PinaCode::get_registry()->short_code($attributes['link']);
		} else {
			$link = get_permalink();
		}
		/**
		 * @var $total_items 
		 */
		if (isset($attributes['total_items'])) {
			$total_items = PinaCode::get_registry()->short_code($attributes['total_items']);
		} else {
			return '';
		}
		/**
		 * @var $limit il numero di elementi da visualizzare
		 */
		if (isset($attributes['limit'])) {
			$limit = PinaCode::get_registry()->short_code($attributes['limit']);
		} else {
			return '';
		}
		/**
		 * @var $method get|post|ajax
		 */
		if (isset($attributes['method'])) {
			$method = $attributes['method'];
		} else {
			$method = 'get';
		}
		/**
		 * @var $style pagination_style
		 */
		if (isset($attributes['style'])) {
			$style = $attributes['style'];
		} else {
			$style = '';
		}
		/**
		 * @var $color 
		 */
		if (isset($attributes['color'])) {
			$color = $attributes['color'];
		} else {
			$color = 'blue';
		}
		ob_start();
		
		$search_class->pagination($link, $total_items, $limit, $id, $method, $style, $color );
		return ob_get_clean();
	}
}
pinacode_set_functions('pagination', 'pinacode_fn_dbt_pagination');


/**
 * Carica una lista in pinacode
 * [^dbt_list id="" params... ]
 */

if (!function_exists('pinacode_fn_dbt_list')) {
	function pinacode_fn_dbt_list($short_code_name, $attributes) {
		/**
		 * @var $id l'id della lista
		 */
		if (isset($attributes['id'])) {
			$id = $attributes['id'];
			unset($attributes['id']);
		} else {
			$id = PinaCode::get_var('global.dbt_filter_id');
		}
		if (count($attributes) > 0) {
			foreach ($attributes as &$value) {
				$value = PinaCode::get_registry()->short_code($value);
			}
			PinaCode::set_var('params', $attributes);
		}
	
		$a = Dbt::get_list($id);
		return $a;
		  
		
	}
}
pinacode_set_functions('dbt_list', 'pinacode_fn_dbt_list');

/**
 * [^LIST_URL pri=[%primaries], dbt_id=xx, action="" ]
 */

if (!function_exists('pinacode_fn_list_url')) {
	function pinacode_fn_list_url($short_code_name, $attributes) {
		$primary_values = [];
		if (@array_key_exists('pri', $attributes)) {
			$primary_values['dbt_ids'] = PinaCode::get_registry()->short_code($attributes['pri']);
		} else {
			$primary_values['dbt_ids'] = PinaCode::get_var('primaries');
		}
		if (@array_key_exists('dbt_id', $attributes)) {
			$primary_values['dbt_id'] = PinaCode::get_registry()->short_code($attributes['dbt_id']);
		} else {
			$primary_values['dbt_id'] = PinaCode::get_var('global.dbt_id');
		}

		if (@array_key_exists('action', $attributes)) {
			$primary_values['action'] = $attributes['action'];
		} else {
			$primary_values['action'] = 'dbt_get_detail';
		}

		$link = esc_url(add_query_arg($primary_values, admin_url('admin-ajax.php')));
		
		return $link;
	}
}
pinacode_set_functions('list_url', 'pinacode_fn_list_url');
