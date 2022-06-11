<?php
/**
* header-type:doc
* header-title: Table structure
* header-order: 02 
* header-description: Questa pagina permette di modificare la struttura di una tabella. È possibile aggiungere, rimuovere o modificare il tipo di un campo e gestire gli indici per ottimizzare le ricerche.
* header-tags:Import, sql, csv, alert table, struttura, add field, alert column
* header-package-title: Manage DB
* header-package-link: manage-db.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
<p>Qui puoi vedere, creare e modificare la struttura della tabella. Ci sono due condizioni che devono essere soddisfatte perché tu possa modificare una tabella: <br>
1. La tabella è in stato DRAFT.<br>
2. La tabella ha una colonna sola auto_increment primary.<br>
<p>Puoi cambiare lo stato di una tabella quando vuoi cliccando su "Edit Status & Description".</p>
<p> I tipi di dati che vengono memorizzati nelle tabelle non distinguono dal tipo di significato che poi avranno i dati all'interno di WORDPRESS. Questo vuol dire che non troverai un tipo user o post, né email o title. Se vuoi collegare una colonna con un utente o un post il campo sarà un numero e conterrà l'id dell'utente o del post collegato. Per i titoli, email e link potrai invece scegliere una riga di testo.</p>
<div id="dbt_help_status" class="dbt_help_div">
    <h3>Status</h3>
    <p>Questo concetto non è proprio di mysql, ma è stato aggiunto per garantire una maggiore sicurezza dei dati. Una tabella in stato <b>DRAFT</b> può essere modifcata o cancellata.</p>
    <p>Nello stato <b>PUBLISHED</b> una tabella non può essere più modificata, né eliminata e viene disabilitata la funzione per cancellare tutti i dati. Questo serve ad evitare la cancellazione  accidentale dei dati.</p>
    <p>Lo stato <b>CLOSE</b> non permette né la modifica della tabella, né l'inserimento dei dati.</p><p>Questo stato è utile per tabelle i cui dati devono rimanere invariati se non in casi eccezionali. Potrebbe essere l'elenco delle città di uno stato o l'elenco delle categorie di un particolare elenco</p>
</div>
<div id="dbt_help_indexes" class="dbt_help_div">
    <h3>Indexes</h3>
    <p>Gli indici sono prevalentemente di due tipi:</p>
    <p><b>Unique</b> serve ad indicare che una o un gruppo di colonne non devono ripetersi uguali su più righe</p>
    <p><b>INDEX</b> servono per velocizzare le query in cui si filtrano i dati per una o più colonne</p>
    <p>Se ad esempio la tabella che stai costruendo viene filtrata sempre per una colonna "categoria" potresti voler aggiungere un indice "categoria" per migliorare le performance.</p><p>Gli indici aumentano però il tempo che ci mette il database a salvare o modificare un dato e lo spazio usato.</p> 
</div>
</div>