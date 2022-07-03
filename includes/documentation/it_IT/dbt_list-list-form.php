<?php
/**
 * header-type:doc
 * header-title: Modulo di inserimento dati
* header-tags:
* header-description: Una volta salvata una query è possibile modificare la visualizzazione dei dati dal tab List view formatting
* header-lang:ITA
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>Gestisci i moduli per l'inserimento dei dati</p>
    <p>Qui puoi scegliere il tipo di campi delle tabelle interessate dalla query.</p>

    <div id="dbt_help_attrs" class="dbt_help_div">
        <h4>Table attributes</h4>
        <p>
            Ogni modulo di inserimento è composta da uno o più blocchi che identificano le tabelle dalle quali i dati sono estratti. Il titolo mostra la tabella e l'alias usato nella query. <br>
            Per ogni tabella cliccando su show attributes è possibile modificare alcuni parametri di visualizzazione
        </p>
    </div>

    <div id="dbt_help_toggle" class="dbt_help_div">
        <h4>Show/Hide</h4>
        <p>Scegli se il campo deve essere mostrato nel modulo di inserimento dei dati</p>
    </div>

    <div id="dbt_help_js" class="dbt_help_div">
        <h4>JS Script</h4>
        <p>Inserisci i javascript per personalizzare l'esperienza di inserimento.</p>
        <p>Esempio: Valido il campo solo se è compreso tra 0 e 100</p>
        <pre class="dbt-code">
 field.valid_range(0,100);
        </pre>
        <p>Esempio: Mostro il campo se il valore di un'ipotetica checkbox è uguale a 1</p>
        <pre class="dbt-code">
 field.toggle(form.get('mycheckboxlabel').val() == 1);
        </pre>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=js-controller-form") ?>" target="blank" class="js-simple-link">Approfondisci</a>
    </div>
    <div id="dbt_help_class" class="dbt_help_div">
        <h4>Custom css class</h4>
        <p>Aggiungi una o più classi css nel field.</p>
        <p>Puoi allineare uno accanto all'altro due campi aggiungendo ai due campi la classe <b>dbt-form-columns-2</b>.
        <p>Per i checkboxes e i radio è possibile impaginare le opzioni in più colonne aggiungendo uno dei seguenti custom css class:<br>
        dbt-form-cb-columns-2, dbt-form-cb-columns-3, dbt-form-cb-columns-4 </p>
    </div>

    <div id="dbt_help_lookup" class="dbt_help_div">
        <h4>Lookup field</h4>
        <p>I lookup fields servono per collegare i dati con altre tabelle tramite primary key. è importante che la lista collegata abbia un unico primary key. <br>
       Per visualizzare i dati nell'elenco bisogna cambiare la query aggiungendo un left join alla tabella che si è collegata.</p>
    </div>

    <div id="dbt_help_delete" class="dbt_help_div">
        <h4>Delete field</h4>
        <p>Se la tabella è in DRAFT mode allora dal form è possibile modificarne la struttura.</p>
        <p>Delete field rimuove il campo e tutti i dati inseriti in quel campo</p>
        <p>Se non si vuole rimuovere la colonna o non si può modificare la tabella si può nascondere il campo dal select show/hide</p>
    </div>
    <div id="dbt_help_new_field" class="dbt_help_div">
        <h4>New field</h4>
        <p>Se la tabella è in DRAFT mode allora è possibile creare un nuovo campo nella tabella. Se si vuole avere più controllo nella creazione dei campi si può andare nella struttura della tabella e modificarla.</p>
    </div>
    <div class="dbt_help_div">
        <h4>PHP Filter</h4>
        <p><a href="<?php echo admin_url("admin.php?page=dbt_docs&section=hooks") ?>" target="blank" class="js-simple-link">apply_filters('dbt_save_data', $query_to_execute, $dbt_id, $origin)</a></p>
    </div>
    <br><br>
</div>
