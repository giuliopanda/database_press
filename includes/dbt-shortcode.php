<?php
/**
 * Gestisco gli sortcode
 * qui una guida chiara su come si scrivono: https://pagely.com/blog/creating-custom-shortcodes/

 */
namespace DatabaseTables;

class Dbt_init_shortcode {
    /**
     * Imposta gli shortcode
     */
    public function __construct() {
        add_shortcode('dbt_list', [$this, 'dbt_list']);
        add_shortcode('dbt_tmpl_engine', [$this, 'dbt_tmpl_engine']);
    }

    /**
     * Mostra una lista frontend
     */
    public function dbt_list($attrs = []) {
        if (is_admin()) return;
        Dbt_fn::require_init();
        $prefix = "";
        if (isset($attrs['prefix']) && $attrs['prefix'] != "") {
            $prefix = $attrs['prefix'];
        }
        if ($attrs['id'] > 0) {
            $post_id = $attrs['id'];
            unset($attrs['id']);
        	return Dbt::get_list($post_id, false, $attrs, $prefix);
        }
    }
    
    /**
     * Esegue il codice di pinacode
     * attrs htmlentities è true di default e sostituisce gli htmlentities in testo, false non lo converte
     */
    public function dbt_tmpl_engine($attrs = [], $content = "") {
        if (is_admin()) return;
        Dbt_fn::require_init();
        // TODO se stampa gli errori oppure no
        $attrs = shortcode_atts(array(
            'debug' => 0,
            'htmlentities' => 0
        ), $attrs);
        if ($attrs['htmlentities'] == 0) {
            $content = str_replace(['&ldquo;','&rdquo;', '&bdquo;','&lsquo;','&rsquo;','&sbquo;'], ['"','"','"',"'","'","'"], $content);
            $content = str_replace(['&#8221;','&#8220;', '&#8222;','&#8216;','&#8217;','&#8218;'], ['"','"','"',"'","'","'"], $content);
            $content = str_replace(['\u201C;','\u201D', '\u201E','\u201E','\u2019','\u201A'], ['"','"','"',"'","'","'"], $content);
            $content = str_replace(['&Prime;','&#8243;', '\u2033','&prime;','&#8242;','\u2032'], ['"','"','"',"'","'","'"], $content);
            $content = str_replace(["<br />",'<br>'],' ',$content);
           $content = html_entity_decode($content);
        }
        if ($content != "") {
            $result = PinaCode::execute_shortcode($content);
           // PcErrors::echo();
        }
        if ($attrs['debug'] > 0) {
     
            if ($attrs['debug'] == 2) {
                $show = "error warning notice info debug";
            }
            if ($attrs['debug'] == 1) {
                $show = "error warning ";
            }
            $result .= PcErrors::get_html($show);
        }
        return $result;
    }
}

new Dbt_init_shortcode();