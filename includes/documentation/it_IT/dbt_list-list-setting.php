<?php
/**
 * header-type:doc
 * header-title: List Tab Frontend
 * header-tags: list frontend
 * header-description: Scegli come mostrare i dati nei post del tuo sito.
 * header-package-title: Manage List
 * header-package-link: manage-list.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <div id="dbt_help_list_of_records" class="dbt_help_div">
        Ogni lista può essere visualizzata nel frontend attraverso uno shortcode, o richiamata da codice php. Puoi infatti chiamare il filtro <a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbt_docs&section=hooks") ?>" target="_blank">dbt_frontend_get_list</a> per ridisegnarne il contenuto.<br>
        Esistono due tipi di visualizzazioni che puoi generare, o una tabella o puoi disegnare la tua visualizzazione attraverso il <a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbt_docs&section=pinacode") ?>" target="_blank">template engine integrato</a>.
    </div>
    <div id="dbt_help_show_if" class="dbt_help_div">
        <h4>IF</h4>
        <p>
            Puoi decidere di mostrare i dati solo se una condizione è vera.
            Le condizioni vengono scritte attraverso le variabili del template engine.
            Alcuni esempi:
            <pre class="dbt-code">[%total_row] > 0</pre>
            <pre class="dbt-code">[^IS_USER_LOGGED_IN] == 1</pre>
        </p>
        <h4>ELSE</h4>
        <p>Se la condizione IF non è soddisfatta mostra il campo else. </p>
    </div>
    <div id="dbt_help_loop_data" class="dbt_help_div">
        <h4>Loop the data</h4>
        <p>Inserisci l'html per generare una visualizzazione personalizzata dei dati. puoi stampare i dati usando le istruzioni del <a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbt_docs&section=pinacode") ?>" target="_blank">template engine integrato</a> [%data.nome_colonna]. Alle variabili è possibile aggiungere una serie di attributi come tutto maiuscolo o il formato della data. Il blocco viene ripetuto tante volte quanti sono i risultati della query impostati nel tab setting.</p>
    </div>
    <div id="dbt_help_table_update" class="dbt_help_div">
        <h4>Table Update</h4>
        <p>Se viene inserita la paginazioni o la ricerca, qui viene definito il metodo con cui vengono aggiornati i dati. Se non avete particolari preferenze potete selezionare ajax.</p>
    </div>
</div>