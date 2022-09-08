<?php
/**
 * La sezione documentale
 * @internal
 */
namespace DatabasePress;

class  Dbp_docs_admin 
{
	/**
	 * Viene caricato alla visualizzazione della pagina
     */
    function controller() {
        wp_enqueue_style( 'database-press-css' , plugin_dir_url( __FILE__ ) . 'css/database-press.css',[],rand());
		wp_enqueue_script( 'database-press-all-js', plugin_dir_url( __FILE__ ) . 'js/database-press-all.js',[],rand());
        // $dbp = new Dbp_fn();
		dbp_fn::require_init();
        $section =  dbp_fn::get_request('section', 'home');

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
			case 'tutorial_01' :
                $render_content = $base_dir."/tutorial_01.php";
				break;
			case 'tutorial_02' :
                $render_content = $base_dir."/tutorial_02.php";
				break;
            default :
            $render_content = $base_dir."/home.php";
            break;
        }
        require(dirname( __FILE__ ) . "/partials/dbp-page-base.php");
    }
}