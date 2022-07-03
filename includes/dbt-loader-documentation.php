<?php
/**
 * Gestisco il filtri e hook per la documentazione.
 *
 * @package    DATABASE TABLE
 * @subpackage DATABASE TABLE/INCLUDES
 * @internal
 */
namespace DatabaseTables;

class Dbt_loader_documentation {
	public function __construct() {
		add_action( 'wp_ajax_dbt_get_documentation', [$this, 'fn_get_documentation'] );   
	}

	/**
	 * Aggiunge la voce di menu e carica la classe che gestisce la pagina amministrativa
	 * 
	 */
	public function fn_get_documentation() {
		require_once(__DIR__.'/dbt-function-documentation.php');
		$dir_scan = __DIR__. "/documentation/" .get_user_locale();
		if (!is_dir($dir_scan)) {
			$dir_scan = __DIR__."/documentation/en_GB";
		}
		$doc = "";

		$get_page = @$_REQUEST['get_page'];
		$search = strtolower(@$_REQUEST['get_search']);
		$page = str_replace(".php","", $get_page);
		$link = add_query_arg(['action'=>'dbt_get_documentation'], admin_url( 'admin-ajax.php' ));
		if ($get_page != "" ) {
			// APRO UNA PAGINA SPECIFICA
			$get_page = str_replace(array("/", "\\", "..", "&", "?", "="), "", $get_page);
			$get_page = str_replace(".php",'', $get_page).".php";
			if (substr($get_page, 0, 8) == "dbt_docs") {
				$get_page = "dbt_docs-menu.php";
			}
			if ( is_file($dir_scan . "/" . $get_page) ) {
				$temp_data = get_file_data($dir_scan . "/" . $get_page, ['header-type'=>'header-type', 'header-title'=>'header-title','header-tags'=>'header-tags', 'header-description'=>'header-description', 'header-package-link'=>'header-package-link', 'header-package-title'=>'header-package-title']) ;
				ob_start();
				?>
				<div class="dbt-content-margin dbt-sidebar-breadcrumb">
					<?php
					if (@$temp_data['header-package-link'] && @$temp_data['header-package-title'] && @$temp_data['header-title']) {
						?><a href="<?php echo add_query_arg('get_page',$temp_data['header-package-link'], $link); ?>"><?php echo $temp_data['header-package-title']; ?></a> &gt; <b><?php echo strtoupper($temp_data['header-title']); ?></b><?php
					} elseif (@$temp_data['header-title']) {
						echo '<b>'.strtoupper($temp_data['header-title']).'</b>'; 
					}
					?>
					<hr>
				</div>
				<?php
				require ($dir_scan ."/". $get_page);
				$doc = ob_get_clean();
			}
		}
	 
		if ($doc == "") {
			ob_start();
			?>
			<div class="dbt-content-margin">
				<a href="<?php echo add_query_arg('get_page','index-doc.php', $link); ?>" class="pina-doc-breadcrumb">Cerca</a>
				<hr>
			</div>
			<?php
			$page = 'index-doc';
			require ( $dir_scan ."/index-doc.php");
			$doc = ob_get_clean();
		} 
		wp_send_json(['doc'=>$doc, 'page'=> $page]);
	}
}


 
