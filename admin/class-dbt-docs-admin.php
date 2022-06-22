<?php

/**
 * La sezione documentale
 * @internal
 */
namespace DatabaseTables;

class DBT_docs_admin 
{
	/**
	 * Viene caricato alla visualizzazione della pagina
     */
    function controller() {
        wp_enqueue_style( 'database-table-css' , plugin_dir_url( __FILE__ ) . 'css/database-table.css',[],rand());
		wp_enqueue_script( 'database-table-all-js', plugin_dir_url( __FILE__ ) . 'js/database-table-all.js',[],rand());
        $dtf = new Dbt_fn();
		Dbt_fn::require_init();
        $section =  $dtf::get_request('section', 'home');

        $base_dir ="/../../includes/documentation/pages_".get_user_locale();
		if (!is_dir($base_dir)) {
			$base_dir = "/../../includes/documentation/pages_en_GB";
		}

        switch ($section) {
            case 'hooks' :
                $render_content = $base_dir."/hooks.php";
				break;
            case 'code-php' :
                $render_content = $base_dir."/code-php.php";
				break;
			case 'pinacode' :
                $render_content = $base_dir."/pinacode.php";
				break;
			case 'js-controller-form' :
                $render_content = $base_dir."/js-controller-form.php";
				break;
            default :
            $render_content = $base_dir."/home.php";
            break;
        }
        require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
    }
}