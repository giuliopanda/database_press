<?php
/**
 * Queste sono funzioni specifiche per database table
 * 
 * TODO Questa parte deve essere ripensata anche in php
 * [^mialista id=5 params... tmpl=[:
 *  [item.html.start]
 *  [item.html.search]
 * 	[item.html.table]
 * 	[item.html.pagination]
 *  [item.html.order]
 *  item.html.end]
 * :]]
 * 
 */
namespace DatabasePress;
if (!defined('WPINC')) die;

/**
 * [^GET_LIST
 * ID per identificare la lista
 * table_id per identificare univocamente la tabella e i parametri da passare!
 */
if (!function_exists('pinacode_fn_dbp_list')) {
	function pinacode_fn_dbp_list($short_code_name, $attributes) {
		$ori_attributes = $attributes;
		$prefix = "";
		if (isset($attributes['prefix'])) {
			$prefix = $attributes['prefix'];
			unset($attributes['prefix']);
		}
		
		/**
		 * @var $id l'id della lista
		 */
		if (isset($attributes['id'])) {
			$id =  absint($attributes['id']);
			unset($attributes['id']);
		} else {
			$id =  absint(PinaCode::get_var('global.dbp_id'));
		}
		$params = [];
		if (count($attributes) > 0) {	
			foreach ($attributes as $key=>$value) {
				$params[$key] = PinaCode::get_registry()->short_code($value);
			}
			if (isset($attributes['table_id']) && $attributes['table_id'] != "") {
			//	$_REQUEST['dbp_div_id'] = $attributes['table_id'];
			//	PinaCode::set_var('params', [$attributes['table_id']=>$new_values]);
			}
		}
		$a = Dbp::get_list($id, false, $params, $prefix);
		return $a;	  
	}
}
pinacode_set_functions('get_list', 'pinacode_fn_dbp_list');


/**
 * [^GET_LIST_DATA id=
 * id per identificare la lista
 * 
 */
if (!function_exists('pinacode_fn_get_list_data')) {
	function pinacode_fn_get_list_data($short_code_name, $attributes) {
		$ori_attributes = $attributes;
		$prefix = "";
		if (isset($attributes['prefix'])) {
			$prefix = $attributes['prefix'];
			unset($attributes['prefix']);
		}
		
		/**
		 * @var $id l'id della lista
		 */
		if (isset($attributes['id'])) {
			$id = absint($attributes['id']);
			unset($attributes['id']);
		} else {
			$id =  absint(PinaCode::get_var('global.dbp_id'));
		}
		if ($id == 0) return [];
		$params = [];
		if (count($attributes) > 0) {	
			foreach ($attributes as $key=>$value) {
				$params[$key] = PinaCode::get_registry()->short_code($value);
			}
		}
		$ori_params =  PinaCode::get_var('params');
        $ori_globals = PinaCode::get_var('global');
        PinaCode::set_var('params', $params);
        $list =  new Dbp_render_list($id, null);
		PinaCode::set_var('global',  $ori_globals);
        PinaCode::set_var('params', $ori_params);
		$items = $list->table_model->items;
		if(count($items) > 1) {
			array_shift($items);
		}
		return $items;	  
	}
}
pinacode_set_functions('get_list_data', 'pinacode_fn_get_list_data');

/**
 * [^LIST_DETAIL item={}, dbp_id=xx, action="" ]
 */

if (!function_exists('pinacode_fn_link_detail')) {
	function pinacode_fn_link_detail($short_code_name, $attributes) {
		$primary_values = [];

		if (@array_key_exists('dbp_id', $attributes)) {
			$dbp_id = PinaCode::get_registry()->short_code($attributes['dbp_id']);
		} else {
			$dbp_id = PinaCode::get_var('global.dbp_id');
		}
		if (absint($dbp_id) == 0) return '';
		$post = dbp_functions_list::get_post_dbp($dbp_id);
		

		if (@array_key_exists('item', $attributes)) {
			if (is_array($attributes['item']) || is_object($attributes['item'])) {
				$item = $attributes['item'];
			} else {
				$item = PinaCode::get_registry()->short_code($attributes['item']);
			}
		} else {
			$item = PinaCode::get_var('data');
		}
		if (!is_array($item) && !is_object($item)) {
			return '';
		}
		if (is_array($item)) {
			$item = (object) $item;
		}

		$primary_values['dbp_ids'] = dbp_fn::ids_url_encode2($post->post_content, $item);
		$primary_values['dbp_id'] = $dbp_id;

		if (@array_key_exists('action', $attributes)) {
			$primary_values['action'] = $attributes['action'];
		} else {
			$primary_values['action'] = 'dbp_get_detail';
		}

		$link = esc_url(add_query_arg($primary_values, admin_url('admin-ajax.php')));
		
		return $link;
	}
}
pinacode_set_functions('link_detail', 'pinacode_fn_link_detail');


/**
 * [^UNIQ_CHARS_IDS item={}, dbp_id=xx ]
 */

if (!function_exists('pinacode_fn_uniq_chars_id')) {
	function pinacode_uniq_chars_id($short_code_name, $attributes) {
		
		if (@array_key_exists('dbp_id', $attributes)) {
			$dbp_id = PinaCode::get_registry()->short_code($attributes['dbp_id']);
		} else {
			$dbp_id = PinaCode::get_var('global.dbp_id');
		}
		if (absint($dbp_id) == 0) return '';
		$post = dbp_functions_list::get_post_dbp($dbp_id);
		
		if (@array_key_exists('item', $attributes)) {
			if (is_array($attributes['item']) || is_object($attributes['item'])) {
				$item = $attributes['item'];
			} else {
				$item = PinaCode::get_registry()->short_code($attributes['item']);
			}
		} else {
			$item = PinaCode::get_var('data');
		}
		if (!is_array($item) && !is_object($item)) {
			return '';
		}
		if (is_array($item)) {
			$item = (object) $item;
		}
		return dbp_fn::ids_url_encode2($post->post_content, $item);;
	}
}
pinacode_set_functions('uniq_chars_id', 'pinacode_uniq_chars_id');


/**
 * [^ADMIN_URL id=dbp_id]
 */
if (!function_exists('pinacode_fn_admin_url')) {
	function pinacode_fn_admin_url($short_code_name, $attributes) { 
		$link = '';
		if (@array_key_exists('id', $attributes)) {
			$id = PinaCode::get_registry()->short_code($attributes['id']);
			unset($attributes['id']);
			$link = admin_url("admin.php?page=dbp_".$id);
		
			if (count ($attributes) > 0) {
				foreach ($attributes as $key=>$attr) {
					$attributes[$key] = PinaCode::get_registry()->short_code($attr);
				}
				$link = add_query_arg($attributes, $link);
			}
		}
		return $link;
	}
}
pinacode_set_functions('admin_url', 'pinacode_fn_admin_url');