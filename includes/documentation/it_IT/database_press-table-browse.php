<?php
/**
 * header-type:doc
 * header-title: Table Browse
 * header-order: 04
 * header-tags:sql browse query filtra merge, meta  data metadata edit inline create list.
 * header-description: Mostra i dati di una query. Puoi filtrarli modificare i dati, esportarli o salvare la query iniziando così la creazione di una nuova lista!
 * header-package-title: Manage DB
 * header-package-link: manage-db.php
 */

namespace DatabasePress;
if (!defined('WPINC')) die;

?>
<div class="dbp-content-margin">
<p>
La pagina browse mostra i risultati di una query. A partire da questi risultati è possibile:
modificare i dati, inserirne di nuovi o rimuoverli.<br>
RIcordati che ogni azione interviene su tutte le tabelle che sono state interessate nella query.</p>

<h4>I bottoni sotto la query</h4>
<p><b>GO</b>: Esegue la query inserita o aggiorna la pagina se non è stata modificata.
</p>
<p><b>EDIT INLINE</b>: Permette di modificare la query</p>
<p><b>ORGANIZE COLUMNS</b>: Legge la query che è stata fino ad ora scritta e permette di gestire la sezione del SELECT. Puoi scegliere quali campi visualizzare e in che ordine. Una volta modificato l'ordine bisogna eseguire nuovamente premendo il bottone go.</p>
<p><b>MERGE</b>: Collega due query attraverso un campo (ad esempio post_id o user_id).</p>
<p><b>ADD META DATA</b>: Se il sistema trova una tabella con il nome della tabella originale + meta e con la struttura tipica dei metadati (primary_key, table_id, meta_key, meta_value) allora permetterà di collegare i metadati.</p>

<p><b>SEARCH</b>: permette di cercare tra tutte le colonne estratte di una query. È possibile anche fare il replace dei dati, mantenendo la struttura dei dati serializzati.<br>Anche il replace è basato sulla query e non sulla tabella per cui è possibile fare il replace su più tabelle e solo sulle colonne visualizzate.</p>

<h4>BULK ACTIONS</h4>

<p>È Possibile scaricare i risultati delle query o dei campi selezionati usando la funzione bulk che trovi alla fine della tabella.</p>
<p>Si possono così anche cancellare più campi o tutti quelli estratti dalla query.<br> Fai sempre attenzione che se stai farendo un'estrazione da più tabelle ti verrà proposto di cancellare i dati da tutte le tabelle. è possibile comunque sempre scegliere da quali tabelle cancellare i dati.</p>

</div>