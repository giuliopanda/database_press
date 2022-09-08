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

namespace DatabasePress;
if (!defined('WPINC')) die;

?>
<div class="dbp-content-margin">
    <p>Modifica la struttura della tabella. Puoi modificare solo le tabelle che soddisfano le seguenti regole: <br>
    1. La tabella è in stato DRAFT.<br>
    2. La tabella ha una colonna sola auto_increment primary key.<br>
    <p>Puoi cambiare lo stato di una tabella quando vuoi cliccando su "Edit Status & Description".</p>
    <p> I dati che vengono memorizzati nelle tabelle sono numeri, testi o date. Se vuoi collegare una colonna con un utente o un post il campo sarà un numero e conterrà l'id dell'utente o del post collegato. Per i titoli, email e link di wordpress potrai invece scegliere di creare una riga di testo.</p>
    <div id="dbp_help_status" class="dbp_help_div">
        <h3>Status</h3>
        <p>Questo concetto non è proprio di mysql, ma è stato aggiunto per garantire una maggiore sicurezza dei dati. Una tabella in stato <b>DRAFT</b> può essere modifcata o cancellata.</p>
        <p>Nello stato <b>PUBLISHED</b> una tabella non può essere più modificata, né eliminata. In più viene disabilitata la funzione per cancellare tutti i dati.</p>
        <p>Lo stato <b>CLOSE</b> non permette né la modifica della tabella, né l'inserimento dei dati.</p><p>Questo stato è utile per tabelle i cui dati devono rimanere invariati o modificabili solo dall'amministratore.</p>
    </div>
    <div id="dbp_help_indexes" class="dbp_help_div">
        <h3>Indexes</h3>
        <p>Gli indici sono prevalentemente di due tipi:</p>
        <p><b>Unique</b> serve ad indicare che una o un gruppo di colonne non devono ripetersi uguali su più righe</p>
        <p><b>INDEX</b> servono per velocizzare le query in cui si filtrano i dati per una o più colonne</p>
        <p>Se ad esempio la tabella che stai costruendo viene filtrata sempre per una colonna "categoria" potresti voler aggiungere un indice "categoria" per migliorare le performance.</p><p>Gli indici aumentano però il tempo che ci mette il database a salvare o modificare un dato e lo spazio usato.</p> 
    </div>
</div>
