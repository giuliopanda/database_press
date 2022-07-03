<?php 
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-table dbt-docs-content  js-id-dbt-content" >
    <h2><?php _e('Database Tables document','database_tables'); ?></h2>
    <div class="dbt-help-p">
    Database tables permette di visualizzare e modificare i dati estratti da una  query mysql.
    <br>
    A partire da una query, una lista permette di gestire la modifica e la visualizzazione dei dati in modo più avanzato.<br>
    Da una lista viene generato uno shortcode per la visualizzazione del frontend.</div>
    
    <h3>Template Engine</h3>
        <div class="dbt-help-p">Puoi modificare i dati che visualizzi attraverso un template engine integrato<br>
        Il template engine integrato può essere usato sia per modificare i dati delle tabelle come ad esempio nei campi calcolati, sia per generare template personalizzati nelle liste.<br>È possibile usare le funzioni del template engine anche all'esterno del plugin inserendo il codice tra gli shortcode <b>[dbt_tmpl_engine] {my code} [/dbt_tmpl_engine]</b>
        <br><br>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=pinacode") ?>">Approfondisci</a>
    </div>

    <h3>Form Javascript</h3>
        <div class="dbt-help-p">Nella gestione dei moduli di inserimento puoi usare il javascript per gestire azioni speciali nei campi come far apparire o sparire un campo o validarne il contenuto.
        <br><br>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=js-controller-form") ?>">Approfondisci</a>
    </div>

    <h3>Hooks & filters</h3>
        <div class="dbt-help-p">Modifica il comportamento del plugin direttamente da codice.<br><br>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=hooks") ?>">Approfondisci</a>
    </div>

    <h3>PHP</h3>
        <div class="dbt-help-p">Sviluppa usando direttamente le funzioni del programma. <br><br>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=code-php") ?>">Approfondisci</a>
    </div>
</div>