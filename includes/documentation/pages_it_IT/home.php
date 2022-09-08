<?php 
namespace DatabasePress;
if (!defined('WPINC')) die;
?>
<div class="dbp-content-table dbp-docs-content  js-id-dbp-content" >
    <h2><?php _e('Database Press document','database_press'); ?></h2>
    <div class="dbp-help-p">
    Database Press permette di visualizzare e modificare i dati estratti da una  query mysql.
    <br>
    A partire da una query, una lista permette di gestire la modifica e la visualizzazione dei dati in modo più avanzato.<br>
    Da una lista viene generato uno shortcode per la visualizzazione del frontend.</div>
    <h3>Shortcodes</h3>
    <p>Per stampare la grafica di una lista puoi usare lo shortcode:</p>
    <p><b>[dbp_list id=list_id]</b> dove id è l'id della lista. Se vuoi visualizzare più liste all'interno della stessa pagina che derivano dalla stessa lista puoi impostare l'attributo prefix con un codice breve univoco tipo prefix="abc". 
    Se nel tab setting hai impostato dei filtri [%params.xxx] puoi passarli all'interno della lista per filtrare ulteriormente i risultati. Esempio:<br>
    [dbp_list id=list_id xxx=23]
    </p>
    <p><b>[dbp_tmpl_engine]</b> Per eseguire il template engine personalizzato</p>
    
    <h3>Template Engine</h3>
        <div class="dbp-help-p">Puoi modificare i dati che visualizzi attraverso un template engine integrato<br>
        Il template engine integrato può essere usato sia per modificare i dati delle tabelle come ad esempio nei campi calcolati, sia per generare template personalizzati nelle liste.<br>È possibile usare le funzioni del template engine anche all'esterno del plugin inserendo il codice tra gli shortcode <b>[dbp_tmpl_engine] {my code} [/dbp_tmpl_engine]</b>
        <br><br>
        <a href="<?php echo admin_url("admin.php?page=dbp_docs&section=pinacode") ?>">Approfondisci</a>
    </div>

    <h3>Form Javascript</h3>
        <div class="dbp-help-p">Nella gestione dei moduli di inserimento puoi usare il javascript per gestire azioni speciali nei campi come far apparire o sparire un campo o validarne il contenuto.
        <br><br>
        <a href="<?php echo admin_url("admin.php?page=dbp_docs&section=js-controller-form") ?>">Approfondisci</a>
    </div>

    <h3>Hooks & filters</h3>
    <div class="dbp-help-p">Modifica il comportamento del plugin direttamente da codice.<br><br>
        <a href="<?php echo admin_url("admin.php?page=dbp_docs&section=hooks") ?>">Approfondisci</a>
    </div>

    <h3>PHP</h3>
    <div class="dbp-help-p">Sviluppa usando direttamente le funzioni del programma. <br><br>
        <a href="<?php echo admin_url("admin.php?page=dbp_docs&section=code-php") ?>">Approfondisci</a>
    </div>

    <h3>Tutorials</h3>
    <div class="dbp-help-p">
    <a href="<?php echo admin_url("admin.php?page=dbp_docs&section=tutorial_01") ?>">Related post</a><br>
    <a href="<?php echo admin_url("admin.php?page=dbp_docs&section=tutorial_02") ?>">Galleries</a>
    </div>
</div>