<?php
/**
 * Funzioni di supporto per la documentazione
 * I file di documentazione devono contenere quasi unicamente il testo!
 */
namespace DatabaseTables;
class Dbt_fn_documentation {
    /**
     * Genera la pagina della ricerca a partire da tutti gli header
     */
    static function echo_search() { 
        $dir_scan = dirname(__FILE__)."/documentation/".get_user_locale();
        if (!is_dir($dir_scan)) {
            $dir_scan = dirname(__FILE__)."/documentation/en_GB";
        }
        $doc = "";

        $link = add_query_arg(['action'=>'dbt_get_documentation'], admin_url( 'admin-ajax.php' ));
        // RICERCO I RISULTATI DEI CONTENUTI 
        $files = scandir($dir_scan);
        $data = [];
        foreach ($files as $file_name) {
            if (!in_array($file_name,['.','..'])) {
                if ( is_file($dir_scan . "/" . $file_name) ) {
                    $temp = get_file_data($dir_scan ."/".$file_name, ['header-type'=>'header-type', 'header-title'=>'header-title','header-tags'=>'header-tags', 'header-description'=>'header-description', 'header-order' => 'header-order']) ;
                    
                    if ($temp['header-type'] == 'doc' || $temp['header-type'] == 'rif' ) {
                        $temp['link'] = add_query_arg('get_page', $file_name, $link);
                        $data[@$temp['header-order'].$file_name] = '<li class="dbt-sidebar-li-search-'.$temp['header-type'].'"><a href="'.$temp['link'].'" class="dbt-sidebar-li-search-title">'.$temp['header-title'].'</a><a class="pina-doc-desc"  href="'.$temp['link'].'">'.@$temp['header-description'].'</a>
                        <div style="display:none" class="js-dbt-table-text">'.$temp['header-title'].' '.@$temp['header-description'].' '.@$temp['header-tags'].'</div></li>';
                    }
                }
            }
        }
        ksort ($data);
        ?> 
        <h4>Search</h4>
        <div class="dbt-form-row">
        <input type="text" class="form-input-edit " onkeyup="dbt_help_search(this)" data-idfilter="dbtHelpListSearch" >
        </div>
        <ul id="dbtHelpListSearch">
            <?php echo implode("\n", $data); ?>
        </ul>
        <?php
    }

    static function echo_menu($group_title) {
        $dir_scan = dirname(__FILE__)."/documentation/".get_user_locale();
        if (!is_dir($dir_scan)) {
            $dir_scan = dirname(__FILE__)."/documentation/en_GB";
        }
        $doc = "";

        $link = add_query_arg(['action'=>'dbt_get_documentation'], admin_url( 'admin-ajax.php' ));
        // RICERCO I RISULTATI DEI CONTENUTI 
        $files = scandir($dir_scan);
        $data = [];
        foreach ($files as $file_name) {
            if (!in_array($file_name,['.','..'])) {
                if ( is_file($dir_scan . "/" . $file_name) ) {
                    $temp = get_file_data($dir_scan ."/".$file_name, ['header-type'=>'header-type', 'header-title'=>'header-title','header-tags'=>'header-tags', 'header-description'=>'header-description', 'header-package-title' => 'header-package-title', 'header-order' => 'header-order']) ;
                    
                    if (($temp['header-type'] == 'doc' || $temp['header-type'] == 'rif') && strtoupper($temp['header-package-title']) == strtoupper($group_title) ) {
                        $temp['link'] = add_query_arg('get_page', $file_name, $link);
                        $data[@$temp['header-order'].$file_name] = '<li class="dbt-sidebar-li-search-'.$temp['header-type'].'"><a href="'.$temp['link'].'" class="dbt-sidebar-li-search-title">'.$temp['header-title'].'</a><a class="pina-doc-desc"  href="'.$temp['link'].'">'.@$temp['header-description'].'</a>
                        <div style="display:none" class="js-dbt-table-text">'.$temp['header-title'].' '.@$temp['header-description'].' '.@$temp['header-tags'].'</div></li>';
                    }
                }
            }
        }
        ksort ($data);
        ?> 
        <div class="dbt-form-row">
        <input type="text" class="form-input-edit " onkeyup="dbt_help_search(this)" data-idfilter="dbtHelpListSearch" >
        </div>
        <ul id="dbtHelpListSearch">
            <?php echo implode("\n", $data); ?>
        </ul>
        <?php
    }
}