<?php
/**
 * La classe serve a gestire e renderizzare la form sql. e tutti i bottoni
 */
 namespace DatabaseTables;

 class Dbt_html_sql {
     /**
      * Mostra l'editor delle query

      *
      * @param [type] $table_model
      * @param boolean $show_editor Se mostrare l'editor aperto o chiuso
      * @return void
      */
    static function render_sql_from($table_model, $show_editor = false ) {
        $class_toggle=($show_editor) ? 'js-default-show-editor' : 'js-default-hide-editor';
        ?><div id="dbt_content_query_edit" class="dbt-content-query-edit <?php echo $class_toggle; ?>"><?php
        self::render_sql_script($table_model, $show_editor);
        ?><div class="dbt-query" id="dbt-query-box"><?php
        echo self::get_html_fields($table_model);
        ?></div><?php
        self::render_html_btns();
        ?></div><?php

    }
    static private function render_sql_script($table_model, $show_editor) {
        $dtf = new Dbt_fn();
        $list_of_tables = $dtf::get_table_list(); 
        $list_of_tables_js = [];
        foreach ($list_of_tables['tables'] as $lot) {
            $list_of_tables_js[] = $lot;
        }
        $list_of_columns = $dtf::get_all_columns();
        $editor_height =  apply_filters( 'dbt_render_sql_height', 250);
        ?>
       <script>
            var dtf_tables = <?php echo json_encode($list_of_tables_js); ?>;
            var dtf_columns = <?php echo json_encode($list_of_columns); ?>;
            var dtf_sql_editor_height = <?php echo $editor_height; ?>;
            <?php  if ($show_editor) : ?>
            jQuery(document).ready(function ($) {
                setTimeout (function() {check_toggle_sql_query_edit()}, 200);      
            });
            <?php endif; ?>
        </script>
        <?php
    }

    static public function get_html_fields($table_model) {
        ob_start();
        $table_model->remove_limit();
        $sql = $table_model->get_current_query();
        ?>
        <textarea style="display:none" id="sql_query_executed"><?php echo esc_textarea($sql); ?></textarea>
        <textarea style="display:none" id="sql_default_query"><?php echo esc_textarea($table_model->get_default_query()); ?></textarea>
        <textarea id="sql_query_edit" name="custom_query" style="display:none"><?php echo esc_textarea(stripslashes($sql)); ?></textarea>
        <div id="result_query" class="dbt-result-query js-dbt-mysql-query-text"> <?php echo htmlentities($sql); ?></div>
        <?php
        return ob_get_clean();
    }

    static private function render_html_btns() {
        $btns = [];
        // dbt-btn-show-sql-edit e dbt-btn-show-sql-view mostra nasconde il campo a seconda dello stato della query
        ?>
        <div class="dbt-result-query-btns js-result-query-btns">
            <?php 
            $btns['edit_inline'] = 
            '<div id="dbt-bnt-edit-query" class="button dbt-btn-show-sql-view" onclick="show_sql_query_edit()">'. __('Edit inline','database_tables').'</div>';
      
            $btns['cancel'] =
            '<div id="dbt-bnt-cancel-query"  class="button dbt-btn-show-sql-edit"  onclick="hide_sql_query_edit()">'. __('Cancel','database_tables').'</div>';
       
            $btns['organize'] =
            '<div id="dbt-bnt-columns-query" class="button js-show-only-select-query dbt-btn-disabled js-btn-disabled"  onclick="dbt_columns_sql_query_edit()">'. __('Organize columns','database_tables').'</div>';

            $btns['merge'] =
            '<div id="dbt-bnt-merge-query" class="button js-show-only-select-query dbt-btn-disabled js-btn-disabled"  onclick="dbt_merge_sql_query_edit()">'. __('Merge','database_tables').'</div>';

            $btns['metadata'] =
            '<div id="dbt-bnt-metadata-query" class="button js-show-only-select-query js-show-only-metada-query dbt-btn-disabled js-btn-disabled"  onclick="dbt_metadata_sql_query_edit()">'. __('Add meta data','database_tables').'</div>';

             echo implode("\n", apply_filters( 'dbt_render_sql_btns', $btns));
            ?>
        </div>
        <div id="dbt_sql_error_show" class="dbt-hide dtf-alert-sql-error"></div>
        <?php
    }
}
