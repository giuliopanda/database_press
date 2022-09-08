<?php
/**
 * Classe per gestire la form di ricerca
 * In pratica html-table-frontend.php e pinacode (pina-action2.php) chiamanto questa classe per disegnare le form di ricerca. 
 * TODO: verifica gestione ajax per form in pinacode, gestione checkbox in verticale
 * gestione checkbox e select con scrittura tipo: [select][option][option][/select]
 * 
 */
namespace DatabasePress;

class  Dbp_search_form {

    /**
     * @var Boolean $form_open
     */
    static $form_open = false; 
    /**
     * @var String $form_id
     */
    static $form_id = ''; 
    /**
     * @var Boolean $pagination_hidden_field
     */
    static $pagination_hidden_field = false; 
    /**
     * @var String $pagination_unique_id
     */
    static $pagination_unique_id = ''; 

    /**
     * @var Integer $post_id
     */
    var $post_id = 0;
	/**
	 * Search link
	 */
    
	function classic_search_link($prefix_request, $base_link = false, $color = 'blue') {
		$uniqid = 'tmp_' . dbp_fn::get_uniqid();
        if ($base_link == false) {
            $base_link = get_permalink();
        }
		$link = $this->filter_order_pagination_add_query_args($base_link, $prefix_request, 'search');
		$conc = (strpos($link, "?") !== false) ? "&amp;" : "?";
        $complete_link = $link.$conc.$prefix_request."_search";
		?>
		<div class="dbp-search-row">
			<input id="<?php echo $uniqid; ?>" type="text" name="<?php echo $prefix_request; ?>_search" value="<?php echo dbp_fn::esc_request($prefix_request.'_search'); ?>" class="dbp-search-input">
			<div class="dbp-search-button dbp-search-button-<?php echo $color; ?>" onclick="window.location='<?php echo $complete_link; ?>='+encodeURIComponent(document.getElementById('<?php echo $uniqid ; ?>').value);">Search</div>
		</div>
		<?php
	}

    /**
     *  Genera una form di ricerca che invia in post di un solo campo per la tabella html-table-frontend 
     * @param String $prefix_request
     * @param String $btn_text Il testo 
     * @return Void
     */
    public function classic_search_post($prefix_request, $label = 'Search', $color = 'blue',  $btn = true) {
        $req_search =  @$_REQUEST[$prefix_request.'_search'];
        ?>
        <div class="dbp-search-row">
                <input class="dbp-search-input js-dbp-search-input" type="search" name="<?php echo $prefix_request; ?>_search" value="<?php echo esc_attr($req_search); ?>">
            <?php // TODO il submit deve essere passato ad una funzione js che pulisce il campo page e order? ?>
            <?php if ($btn) : ?>
                <div class="dbp-search-button dbp-search-button-<?php echo $color; ?>" onclick="dbp_submit_simple_search(this)"><?php echo $label; ?></div>
            
                <?php if ($req_search  != "") : ?>
                    <div class="dbp-search-button dbp-search-button-<?php echo $color; ?>" onclick="dbp_submit_clean_simple_search(this)"><?php _e('Clean', 'database_press'); ?></div>
                <?php endif; ?>
            <?php endif; ?>
           
        </div>
        <?php        
        
    }


    /**
     * Setto il post id. Mi serve per fare il distinct nel select
     */
    public function set_post_id($id) {
        $this->post_id = $id;
    }

    /**
     *  Genera una form di ricerca di un solo campo.
     * @param String $field_name prefix+'_field_name'
     * @param String $btn_text Il testo 
     * @param String $label 
     * @return Void
     */
    public function field_one_input_form($field_name_request, $label) {
        if ($label != "") : ?>
            <label><span class="dbp-search-label"><?php echo $label; ?></span>	
        <?php endif; ?>
        <input class="dbp-search-input js-dbp-search-input" type="search" name="<?php echo esc_attr($field_name_request); ?>" value="<?php echo esc_attr(@$_REQUEST[$field_name_request]); ?>">
        <?php if ($label != "") : ?>
            </label>
        <?php endif; 
    }



    
    /**
     *  Genera una form di ricerca di un solo campo.
     * @deprecated sostituito da field_one_input_form
     * @param String $link Il link del form
     * @param String $method (get|post)
     * @param String $field_name prefix+'_field_name'
     * @param String $btn_text Il testo 
     * @param String $div_row_attributes Gli attributi del div che contiene l'input e il bottone
     * @return Void
     */
    public function one_field_input_form($link, $method, $field_name, $btn_text, $div_row_attributes, $label) {
        $form_open = $this->open_form($link, $method);
        ?>
        <div <?php echo $div_row_attributes; ?>>
            <?php if ($label != "") : ?>
            <label><span class="dbp-search-label"><?php echo $label; ?></span>	
            <?php endif; ?>
                <input class="dbp-search-input" type="text" name="<?php echo esc_attr($field_name); ?>" value="<?php echo esc_attr(@$_REQUEST[$field_name]); ?>">
            <?php if ($label != "") : ?>
                </label>
            <?php endif; ?>
            <?php
        if ($form_open) {
            self::$form_open = false;
            ?>
            <button type="submit" class="dbp-search-button dbp-search-button-parent-attr"><?php echo esc_attr(_e($btn_text, "database_press")); ?></button>
             </div>
             </form><?php
        } else {
            ?>
        </div>
            <?php        
        }
    }

     /*
     *  Genera una form di ricerca di un solo campo.
     * @param String $link Il link del form
     * @param String $method (get|post)
     * @param String $field_name prefix+'_field_name'
     * @param String $btn_text Il testo 
     * @param String $div_row_attributes Gli attributi del div che contiene l'input e il bottone
     * @return Void
  
    public function one_field_select_form($link, $method, $field_name, $options, $btn_text, $div_row_attributes, $label) {
        $form_open = $this->open_form($link, $method);
        ?>
        <div <?php echo $div_row_attributes; ?>>	
            <?php if ($label != "") : ?>
                <label><span class="dbp-search-label"><?php echo $label; ?></span>	
            <?php endif; ?>	
            <?php
            if (is_object($options)) {
                $options = (array)$options;
            }
            // DISTINCT
            if (($options == "" || (is_string($options) && strtolower($options) == "distinct")) && $this->post_id > 0) {
                $post = dbp_functions_list::get_post_dbp($this->post_id);
                $sql = @$post->post_content['sql'];
                $field = str_replace('dbp'.$this->post_id."_",'', $field_name);
                $table =  dbp_fn::get_val_from_head_column_name($post->post_content['list_setting'], $field);
                $column =  dbp_fn::get_val_from_head_column_name($post->post_content['list_setting'], $field, 'name');
                if ($table && $column) {
                    $model = new Dbp_model($table);
                    $model->prepare($sql);
                    $result = $model->distinct($column);
                    if ($result) {
                        $options = [];
                        foreach ($result as $r) {
                            if ($r->p == -1) {
                                $options[$r->c] = $r->c;
                            } else {
                                // TODO oppure ^ e comunque da gestire nel like e =
                                $options['#'.$r->p] = $r->c;
                            }
                        }
                    }

                } else {
                    PcErrors::set('Distinct search is not possible to create for field <b>'. htmlentities(substr( $field,0, 90)).'</b>', '', -1, 'warning');
                }
                
            }
            if (is_array($options)) {
                $options = array_filter($options);
                $options = array_merge([''=>''], $options);
                echo dbp_fn::html_select($options, true,'name="'.esc_attr($field_name).'" class="dbp-search-select"', @$_REQUEST[$field_name]);
            } else {
                // error!
                PcErrors::set('Select Form Error values is not an array <b>'. htmlentities(substr( $options,0, 90)).'</b>', '', -1, 'warning');
    
            }
            if ($form_open) {
                ?>
                <button type="submit" class="dbp-search-button dbp-search-button-<?php echo 'blue'; ?>"><?php echo esc_attr(_e($btn_text, "database_press")); ?></button><?php
            }
            ?>
            <?php if ($label != "") : ?>
            </label>
            <?php endif; ?>
        </div>
        <?php 
        if ($form_open) { 
            self::$form_open = false;
            ?></form><?php 	
        }
    }
    */

    /*
     * Checkboxes
     * @param String $link Il link del form
     * @param String $method (get|post)
     * @param String $field_name prefix+'_field_name'
     * @param String $btn_text Il testo 
     * @param String $div_row_attributes Gli attributi del div che contiene l'input e il bottone
     * @return Void
    public function one_field_checkbox_form($link, $method, $field_name, $values, $btn_text, $div_row_attributes, $label) {
        $form_open = $this->open_form($link, $method);
        ?>
        <div <?php echo $div_row_attributes; ?>>	
            <?php if ($label != "") : ?>
                <label><span class="dbp-search-label"><?php echo $label; ?></span>	
            <?php endif; ?>	
            <?php
            if(is_array($values) || is_object($values)) {
                    $req_values = @$_REQUEST[$field_name];
                    if (!is_array($req_values)) {
                        $req_values = [];
                    }
                 
                    ?><div class="dbp-search-checkboxes-block dbp-search-block"><?php
                    foreach ($values as $okey=>$oval) {
                        $id = "dbpckb_".dbp_fn::get_uniqid();
                        ?><div class="dbp-checkbox"><input type="checkbox" id="<?php echo $id; ?>" name="<?php echo esc_attr($field_name); ?>[]" value="<?php echo esc_attr($okey); ?>" <?php echo (in_array($okey, $req_values)) ? ' checked="checked"' : ''; ?>><label for="<?php echo $id; ?>"><?php echo $oval;?></label></div><?php
                    }
                    ?></div><?php
                
            } else {

                $id = "dbpckb_".dbp_fn::get_uniqid();
                ?>
                <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo esc_attr($field_name); ?>" value="<?php echo esc_attr($values); ?>" <?php echo (@$_REQUEST[$field_name] == $values) ? ' checked="checked"' : ''; ?> class="dbp-search-checkbox">
                <?php
            } 
            if ($form_open) {
                ?>
                <button type="submit" class="dbp-search-button dbp-search-button-<?php echo 'blue'; ?>"><?php echo esc_attr(_e($btn_text, "database_press")); ?></button><?php
            }
            ?>
            <?php if ($label != "") : ?>
            </label>
            <?php endif; ?>
        </div>
        <?php 
        if ($form_open) { 
            self::$form_open = false;
            ?></form><?php 	
        }
    }
    */


     /**
      * Open form Stampa l'apertura del form php 
      * @return Boolean False se era già aperto, true se è stato appena creato
      */
     public function open_form($link, $method) {
        if (self::$form_open) return false; // era già aperto
        self::$form_id = 'dbp_' . dbp_fn::get_uniqid();
        self::$form_open = true;
        self::$pagination_hidden_field = false; 
        self::$pagination_unique_id = ''; 
        $link_a = explode("?", $link);
		$link = array_shift($link_a);
        if ($method == "link") {
            $method = "get";
        }
        dbp_fn::set_open_form();
        ?>
        <form method="<?php echo $method; ?>" action="<?php echo $link; ?>"  class="js-dbp-send-<?php echo strtolower($method); ?>">
        <?php
        if (count($link_a) > 0) {
            foreach ($link_a as $a) {
                $val = explode("=", $a);
                if (count($val) == 2) {
                    ?><input type="hidden" name="<?php echo $val[0]; ?>" value="<?php echo $val[1]; ?>" id="<?php echo self::$form_id ; ?>"><?php 
                }
            }
        }
        return true;
     }
   

     /**
      * Chiude la form
      * @param Mixed btn_text Se è false non fa apparire il box con il bottone di submit, altrimenti ne mostra il testo.
      */
    public function close_form($btn_text, $div_row_attributes = "", $color="blue") {
        if (!self::$form_open) return false; // Non era stato aperto un form!
        if ($btn_text != false) {
            ?>
                <div <?php echo $div_row_attributes; ?>>		
                <button type="submit" class="dbp-search-button dbp-search-button-<?php echo $color; ?>"><?php echo esc_attr(_e($btn_text, "database_press")); ?></button>
                </div>
            <?php
        }
        echo '</form>';
        self::$pagination_hidden_field = false; 
        self::$form_open = false;
        dbp_fn::set_close_form();
    }

    /**
     * Aggiunge gli input hidden di eventuali altri campi da passare
     * @param Array $array_exclude l'elenco dei parametri da escludere senza prefissi
     */
    public function add_params_list_per_post($post_id, $array_exclude) {
        // è stato appena aperto il form quindi ci passo i request 
        $exclude = [];
        foreach ($array_exclude as $ax) {
            $exclude[] =  'dbp'.$post_id."_".$ax;
        }
        if (self::$form_open && is_array($_REQUEST)) {
            foreach ($_REQUEST as $key=>$value) {
                if (substr($key,0, strlen('dbp'.$post_id."_")) == 'dbp'.$post_id."_" && !in_array($key, $exclude)) {
                    if (is_array($value)) {
                        foreach ($value as $v2) {
                            ?><input type="hidden" name="<?php echo $key; ?>[]" value="<?php echo $v2; ?>"><?php 
                        }
                    } else {
                        ?><input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"><?php 
                    }
                }
            }
        }
    }

	/**
	 * Aggiunge i parametri dei filtraggi del frontend ai link
	 * Lo uso su pagination, order, filter
	 * le query possono essere passate in $path_parametro oppure $path[parametro]
	 * @param String $link 
	 * @param String $path Il prefisso dei parametri
	 * @param String $exclude Se c'è un parametro da non inserire
	 * @return String Il nuovo link
	 */
	private function filter_order_pagination_add_query_args($link, $path, $exclude = "") {
		$length = strlen($path);
		if (isset($_REQUEST) && is_array($_REQUEST)) {
			foreach ($_REQUEST as $key=>$value) {
                if (is_string($value)) {
                    if (substr($key,0, $length) == $path && substr($key, $length) != "_".$exclude) {
                        $link = add_query_arg($key, urlencode(stripslashes($value)), $link);
                    }
                } else {
                    foreach ($value as $val) {
                        if (is_string($val)) {
                            if (substr($key,0, $length) == $path && substr($key, $length) != "_".$exclude) {
                                $link = add_query_arg($key."[]", urlencode(stripslashes($val)), $link);
                            }
                        }
                    }
                   
                }
			}
		}
		return $link;
	}


    /**
	 * Disegna la paginazione
     * @deprecated sostituita da dbp-render-list
	 * @return Void
	 */
	function pagination($link, $total_items, $limit, $post_id, $method, $pagination_style, $color ) {
       
        if ($method != "link") {
            $this->pagination_form($link, $total_items, $limit, $post_id, $pagination_style, $color, $method);
        } else {
            $this->pagination_link($link, $total_items, $limit, $post_id, $pagination_style, $color);
        }
       
    }


    /**
     * Disegna la paginazione come link
     */
    public function pagination_link($link, $total_items, $limit, $post_id, $pagination_style, $color) {
		$pages = ceil($total_items / $limit);
		$prefix = 'dbp'.$post_id;
		$link = $this->filter_order_pagination_add_query_args($link, $prefix, 'page');
		$conc = (strpos($link, "?") !== false) ? "&amp;" : "?";
		if (isset($_REQUEST[$prefix."_page"])) {
			$curr_page =$_REQUEST[$prefix."_page"];
		}
		if (!isset($curr_page) || !is_numeric($curr_page) || $curr_page < 1) {
			$curr_page = 1;
		}
		$prev_page = $curr_page - 1;
		$next_page = $curr_page + 1;
		$name_anchor = '#'. $prefix;
        $total_items_name = apply_filters('dbp_frontend_total_items', __(sprintf('%s items', $total_items), 'database_press'), $post_id, $total_items,  $limit, $curr_page, $pages );
		?>
		<div class="dbp-pagination dbp-pagination-<?php echo $color; ?>">
			<div class="dbp-pagination-total"><?php echo $total_items_name; ?></div>
            <?php if ($pages > 1) : ?>
                <?php if ( $pagination_style == 'numeric') : ?>
                    <div class="dbp-pagination-btns">
                        <?php if ($prev_page > 0) : ?>
                            <a href="<?php echo $link.$conc.$prefix."_page=1". $name_anchor; ?>">&laquo;</a>
                        <?php else : ?>
                            <span >&laquo;</span>
                        <?php endif; ?>
                        <?php if ($prev_page > 0) : ?>
                            <a href="<?php echo $link.$conc.$prefix."_page=".$prev_page. $name_anchor; ?>">&lsaquo;</a>
                        <?php else : ?>
                            <span >&laquo;</span>
                        <?php endif; ?>
                        <?php 
                        $draw = 0;
                        $num_btn = 5;
                        $draw_page = $curr_page - floor($num_btn/2);
                        if ($draw_page + $num_btn > $pages) {
                            $draw_page =  $pages - $num_btn +1;
                        }
                        if ($draw_page < 1 ) $draw_page = 1;
                        while ($draw < $num_btn && $draw_page <= $pages) {
                            $draw++;
                            ?><a href="<?php echo $link.$conc.$prefix."_page=".$draw_page. $name_anchor; ?>" <?php echo ($draw_page == $curr_page) ? 'class="active"' : ''; ?>><?php echo $draw_page; ?></a><?php
                            $draw_page++;
                            
                        }	
                        ?>
                        <?php if ($next_page <= $pages) : ?>
                            <a href="<?php echo $link.$conc.$prefix."_page=".$next_page. $name_anchor; ?>">&rsaquo;</a>
                        <?php else : ?>
                            <span>&rsaquo;</span>
                        <?php endif; ?>
                        <?php if ($curr_page < $pages) : ?>
                            <a href="<?php echo $link.$conc.$prefix."_page=".$pages. $name_anchor; ?>">&raquo;</a>
                        <?php else : ?>
                            <span>&raquo;</span>
                        <?php endif; ?>
                    
                    </div>
                <?php else : ?>
                    <div class="dbp-pagination-btns">
                        <?php if ($prev_page > 0) : ?>
                            <a href="<?php echo $link.$conc.$prefix."_page=".$prev_page. $name_anchor; ?>">&laquo;</a>
                        <?php else : ?>
                            <span >&laquo;</span>
                        <?php endif; ?>
                    </div>
                        <select class="dbp-select-pagination" onChange="window.location='<?php echo $link.$conc.$prefix."_page"; ?>='+this.value+'<?php echo $name_anchor; ?>';">
                            <?php for ($x = 1; $x <= $pages; $x++) : ?>
                                <?php $selected = ($x == $curr_page) ? ' selected="selected" ' : '' ; ?>
                                <option value="<?php echo $x; ?>" <?php echo $selected; ?>><?php echo  $x;?></option>
                            <?php endfor; ?>
                        </select>
                    <div class="dbp-pagination-btns">
                        <?php if ($next_page <= $pages) : ?>
                            <a href="<?php echo $link.$conc.$prefix."_page=".$next_page. $name_anchor; ?>">&raquo;</a>
                        <?php else : ?>
                            <span>&raquo;</span>
                        <?php endif; ?>
                    
                    </div>
                <?php endif; ?>
            <?php endif; ?>
		</div>
		<?php
	}


    /**
     * Disegna la paginazione come form
     */
    public function pagination_form($prefix, $total_items, $limit, $post_id, $pagination_style, $color, $method) {

        $form_open = $this->open_form(get_permalink(), $method);
		$uniqid = 'dbp_' . dbp_fn::get_uniqid();
        
        $pages = ceil($total_items / $limit);
		//$prefix = 'dbp'.$post_id;
		if (isset($_REQUEST[$prefix."_page"])) {
			$curr_page =$_REQUEST[$prefix."_page"];
		}
		if (!isset($curr_page) || !is_numeric($curr_page) || $curr_page < 1) {
			$curr_page = 1;
		}
		$prev_page = $curr_page - 1;
		$next_page = $curr_page + 1;
        
        $total_items_name = apply_filters('dbp_frontend_total_items', __(sprintf('%s items', $total_items), 'database_press'), $post_id, $total_items,  $limit, $curr_page, $pages );
		?>
      
		<div class="dbp-pagination dbp-pagination-<?php echo $color; ?>">
            <?php if (!self::$pagination_hidden_field) {
               
                self::$pagination_hidden_field = true;
                self::$pagination_unique_id = $uniqid;
            } else {
                $uniqid = self::$pagination_unique_id;
            }
             ?>
			<div class="dbp-pagination-total"><?php echo $total_items_name; ?></div>
            <?php if ($pages > 1) : ?>
                <?php if ( $pagination_style == 'numeric') : ?>
                    <div class="dbp-pagination-btns">
                        <?php if ($prev_page > 0) : ?>
                            <span class="pagination_click" onclick="dbp_submit_pagination(gp_parents(this, 'form'), '1')">&laquo;</span>
                        <?php else : ?>
                            <span >&laquo;</span>
                        <?php endif; ?>
                        <?php if ($prev_page > 0) : ?>
                        
                            <span class="pagination_click" onclick="dbp_submit_pagination(gp_parents(this, 'form'), '<?php echo $prev_page; ?>')">&lsaquo;</span>
                        <?php else : ?>
                            <span >&laquo;</span>
                        <?php endif; ?>
                        <?php 
                        $draw = 0;
                        $num_btn = 5;
                        $draw_page = $curr_page - floor($num_btn/2);
                        if ($draw_page + $num_btn > $pages) {
                            $draw_page =  $pages - $num_btn +1;
                        }
                        if ($draw_page < 1 ) $draw_page = 1;
                        while ($draw < $num_btn && $draw_page <= $pages) {
                            $draw++;
                            ?><span class="pagination_click<?php echo ($draw_page == $curr_page) ? ' active' : ''; ?>" onclick="dbp_submit_pagination(gp_parents(this, 'form'), '<?php echo $draw_page; ?>')"><?php echo $draw_page; ?></span><?php
                            $draw_page++;
                            
                        }	
                        ?>
                        <?php if ($next_page <= $pages) : ?>
                            <span class="pagination_click" onclick="dbp_submit_pagination(gp_parents(this, 'form'), '<?php echo $next_page; ?>')">&rsaquo;</span>
                        <?php else : ?>
                            <span>&rsaquo;</span>
                        <?php endif; ?>
                        <?php if ($curr_page < $pages) : ?>
                            <span class="pagination_click" onclick="dbp_submit_pagination(gp_parents(this, 'form'), '<?php echo $pages; ?>')">&raquo;</span>
                        <?php else : ?>
                            <span>&raquo;</span>
                        <?php endif; ?>
                    
                    </div>
                <?php else : ?>
                    <div class="dbp-pagination-btns">
                        <?php if ($prev_page > 0) : ?>
                            <span class="pagination_click" onclick="dbp_submit_pagination(gp_parents(this, 'form'), '<?php echo $prev_page; ?>')">&laquo;</span>
                        <?php else : ?>
                            <span >&laquo;</span>
                        <?php endif; ?> 
                    </div>
                        <select class="dbp-select-pagination" onchange="dbp_submit_pagination_selected(this)">
                            <?php for ($x = 1; $x <= $pages; $x++) : ?>
                                <?php $selected = ($x == $curr_page) ? ' selected="selected" ' : '' ; ?>
                                <option value="<?php echo $x; ?>" <?php echo $selected; ?>><?php echo  $x;?></option>
                            <?php endfor; ?>
                        </select>
                    <div class="dbp-pagination-btns">
                        <?php if ($next_page <= $pages) : ?>
                            <span class="pagination_click"  onclick="dbp_submit_pagination(gp_parents(this, 'form'), '<?php echo $next_page; ?>')">&raquo;</span>
                        <?php else : ?>
                            <span>&raquo;</span>
                        <?php endif; ?>
                    
                    </div>
                <?php endif; ?>
            <?php endif; ?>
		</div>
		<?php
        if ($form_open) { 
            self::$form_open = false;
            ?></form><?php 	
        }
	}
}