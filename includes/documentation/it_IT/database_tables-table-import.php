<?php
/**
* header-type:doc
* header-title: Import
 * header-order: 03
* header-description: Come importare dati sql o csv
* header-tags:Import, sql, csv
* header-package-title: Manage DB
* header-package-link: manage-db.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>Dal tab import è possibile importare istruzioni sql o serie di dati in csv.</p>
    <div id="dbt_help_sql" class="dbt_help_div">
        <p>I file sql vengono eseguiti senza fare nessun controllo sui dati.</p>
    </div>
    <div id="dbt_help_csv" class="dbt_help_div">
        <h3>Inserimento o aggiornamento dei dati tramite CSV </h3>
        <p>Fate attenzione a caricare i file sql o csv correttamente nei rispettivi moduli di inserimento.</p>
    </div>
    <div id="dbt_help_delimiter" class="dbt_help_div">
        <ul>
            <li><b>Delimiter</b> è il carattere usato per dividere i dati delle colonne nel csv</li>
            <li><b>Use first row as Headers</b> Se spuntato la prima riga non verrà importata</li>
            <li><b>Update Preview</b> Aggiorna le impostazioni scelte.</li>
        </ul>
    </div>
    <div id="dbt_help_choose_action" class="dbt_help_div">
        <h3>Choose Action</h3>
        <div id="dbt_help_create_table" class="dbt_help_div">
            <h4>Create table</h4> 
            <p>Crei una tabella nel database a partire dalle colonne del csv. Sceglierai in un secondo momento quali campi associare. </p>
        </div>
        <div id="dbt_help_insert_record" class="dbt_help_div">
            <h4>Insert/Update records</h4>
            <p>Seleziona la tabella e collega i campi del csv da inserire. Se associ la chiave primaria ad un campo, se questo esiste eseguirà l'aggiornamento della riga, altrimenti creerà un nuovo record.</p>
            <p>Puoi selezionare più tabelle e scegliere quali colonne del csv inserire in una o nell'altra tabella. Ogni volta che viene inserita o aggiornata una colonna viene generato un campo che potrai inserire nella tabella successiva per creare una relazione tra le due tabelle.</p>
            <p>Se vuoi modificare un campo che stai inserendo, dopo averlo selezionato nella tabella delle associazioni, cambia la selezione in [custom text]. Apparirà lo shortcode relativo. A questo punto puoi utilizzare tutte le istruzioni del <a href="<?php echo add_query_arg('get_page','pina-intro.php', $link); ?>">template engine integrato</a>.</p>
        </div>  
    </div>
    <h3>Test the import</h3> 
    <p>Genera ed esegue un test di importazione su delle tabelle temporanee e ne mostra il risultato. Verificalo con attenzione per non avere spiacevoli sorprese quando importerai i dati. Se una colonna non sembra contenere i dati attesi forse stai cercando di inserire un tipo di dato non corretto (ad esempio un numero in un campo data).</p>
    <h3>Mi sento fortunato, importa i dati</h3>
    <p>Esegue l'importazione dei dati. Una volta avviata l'operazione non si può tornare indietro. Una volta completata l'importazione scarica il report, questo conterrà le righe del csv con nuove colonne con gli id associati all'importazione e il risultato delle query.</p>
</div>