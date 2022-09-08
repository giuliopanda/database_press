<?php
/**
 * header-type:doc
 * header-title: List Tab Settings
* header-tags:
* header-description: Definisci la query da eseguire, chi può modificare la lista ecc..
* header-package-title: Manage List
* header-package-link: manage-list.php
*/

namespace DatabasePress;
if (!defined('WPINC')) die;

?>
<div class="dbp-content-margin">
    <div id="dbp_help_admin_sidebar_menu" class="dbp_help_div">
        <h3>Admin sidebar menu</h3>
        Aggiungi una voce di menu nella tua amministrazione e decidi chi può accedere. <br>Se non vedi subito cambiare le impostazioni ricarica la pagina.
    </div>

    <div id="dbp_help_admin_sidebar_menu_icon" class="dbp_help_div">
        <h4>Add Icon</h4> 
        <p>Puoi scegliere un'icona tra quelle presenti in questa pagina: <a href="https://developer.wordpress.org/resource/dashicons" target="blank" class="js-simple-link">developer.wordpress.org/resource/dashicons</a> Clicca sull'icona che vuoi inserire e premi copia HTML. Nell'alert che apparirà prendi la seconda classe. es: <i>dashicons-image-rotate-right</i>. Copia la classe nel campo. </p>
    </div>

    <div id="dbp_help_admin_sidebar_menu_position" class="dbp_help_div">
        <h4>Position (number)</h4> 
        <p>Scegli a che altezza mostrare la voce di menu.  </p>
    </div>

    <div id="dbp_help_admin_sidebar_menu_permissions" class="dbp_help_div">
        <h4>Permissions</h4> 
        <p>Scegli chi può modificare la lista. Tra i permessi è presenta anche l'amministratore. Tu potrai sempre visualizzare e modificare la lista all'interno del plugin, ma puoi scegliere di non farla apparire nel menu.</p>
    </div>

    <div id="dbp_help_admin_query" class="dbp_help_div">
        <h3>Query</h3>
        <p>Puoi scegliere di estrarre tutti i dati di una tabella, oppure filtrarli aggiungendo clausule WHERE. Puoi estrarre solo alcuni campi o aggiungere campi calcolati. Alcune query più complesse potrebbero non funzionare correttamente. Se vuoi collegare altre tabelle usa la clausola LEFT JOIN ... ON.</p>
    </div>

    <div id="dbp_help_admin_filter" class="dbp_help_div">
        <h3>Filter</h3>
        <p>Aggiunge un filtro quando la lista riceve un determinato parametro. </p>
        <p> I parametri vengono scritti come shortcode e sono:</p>
        
        <ul>
            <li><b>[%params.xxx]</b> sono i parametri aggiunti negli shortcode</li>
            <li><b>[%request.xxx]</b> per i dati ricevuti nell'url</li>
        </ul> 
        <p>È possibile utilizzare anche tutte le funzioni del template engine come ad esempio [^current_post] per avere i dati de post che si sta visualizzando ([^current_post.id] per l'id).</p>

        <p>Se è selezionato 'required' ma non viene passato il valore la query non torna risultati. Se invece required non è selezionato e non viene passato nessun parametro la query torna i risultati non filtrati</p>
    </div>

    <div id="dbp_help_delete_options" class="dbp_help_div">
        <h3>DELETE OPTIONS</h3>
        <p>Se è presente una sola tabella scegli se è possibile eliminare o no i record dalla lista. <br> Se la query estrae più tabelle scegli quali devono essere rimosse quando si elimina un file.<br>Se selezioni no a tutte le tabelle, allora i record non possono essere rimossi nella lista.</p>
    </div>
    
</div>