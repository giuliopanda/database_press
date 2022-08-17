<?php
/**
 * header-type:doc
 * header-title: Information schema
 * header-order: 01
 * header-tags:Information schema, delete table cancel remove
 * header-description: Mostra L'elenco delle tabelle presenti nel database usato da wordpress. Da qui puoi creare nuove tabelle, svuotare o eliminare una tabella o scaricarne il contenuto. 
 * header-package-title: Manage DB
 * header-package-link: manage-db.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;

?>
<div class="dbt-content-margin">
    <p>Mostra tutte le tabelle del database. Le tabelle di wordpress hanno un prefisso che le identifica. Non tutte le tabelle possono essere modificate, questo dipende dal loro stato.</p>
    <p>Lo <b>stato di una tabella</b> è una caratteristica aggiunta da questo plugin e che funziona solo all'interno del plugin stesso.</p>
    <p>Questo serve a prevenire modifiche accidentali di una tabella o del suo contenuto.</p>
    <p>Puoi modificare lo stato di una tabella cliccando sul titolo e successivamente sul tab "structure".</p>
    <ul>
        <li><b>DRAFT</b> Permette qualsiasi operazione. Puoi modificare la struttura della tabella, cancellarne il contenuto o eliminarla.</li>
        <li><b>PUBLISH</b> Puoi gestire il contenuto di una tabella, ma non modificarla. Non è più permesso eliminare la tabella</li>
        <li><b>CLOSE</b> Non puoi più modificare i dati della tabella, ma puoi solo visualizzarli</li>
    </ul>
    <br>
    Esiste un hook per modificare lo stato da codice. Attraverso questo hook le tabelle di wordpress vengono bloccate allo stato publish. 
    <a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbt_docs&section=hooks") ?>" target="_blank">Approfondisci Hooks & filters</a>
</div>
