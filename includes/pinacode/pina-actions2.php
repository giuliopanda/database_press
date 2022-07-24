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
namespace DatabaseTables;
if (!defined('WPINC')) die;

/**
 * ID per identificare la lista
 * table_id per identificare univocamente la tabella e i parametri da passare!
 */
if (!function_exists('pinacode_fn_dbt_list')) {
	function pinacode_fn_dbt_list($short_code_name, $attributes) {
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
			$id = $attributes['id'];
			unset($attributes['id']);
		} else {
			$id = PinaCode::get_var('global.dbt_filter_id');
		}
		$params = [];
		if (count($attributes) > 0) {	
			foreach ($attributes as $key=>$value) {
				$params[$key] = PinaCode::get_registry()->short_code($value);
			}
			if (isset($attributes['table_id']) && $attributes['table_id'] != "") {
			//	$_REQUEST['dbt_div_id'] = $attributes['table_id'];
			//	PinaCode::set_var('params', [$attributes['table_id']=>$new_values]);
			}
		}
		$a = Dbt::get_list($id, false, $params, $prefix);
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


/**
 * [^ADMIN_URL id=dbt_id]
 */
if (!function_exists('pinacode_fn_admin_url')) {
	function pinacode_fn_admin_url($short_code_name, $attributes) { 
		$link = '';
		if (@array_key_exists('id', $attributes)) {
			$id = PinaCode::get_registry()->short_code($attributes['id']);
			unset($attributes['id']);
			$link = admin_url("admin.php?page=dbt_".$id);
		
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