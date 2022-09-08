<?php
/**
 * header-type:doc
 * header-title: Modulo di inserimento dati
* header-tags:
* header-description: Una volta salvata una query è possibile modificare la visualizzazione dei dati dal tab List view formatting
* header-lang:ITA
*/

namespace DatabasePress;
if (!defined('WPINC')) die;

?>
<div class="dbp-content-margin">
    <p>Gestisci i moduli per l'inserimento dei dati</p>
    <p>Qui puoi scegliere il tipo di campi delle tabelle interessate dalla query.</p>

    <div id="dbp_help_attrs" class="dbp_help_div">
        <h4>Table attributes</h4>
        <p>
            Ogni modulo di inserimento è composta da uno o più blocchi che identificano le tabelle dalle quali i dati sono estratti. Il titolo mostra la tabella e l'alias usato nella query. <br>
            Per ogni tabella cliccando su show attributes è possibile modificare alcuni parametri di visualizzazione
        </p>
    </div>

    <div id="dbp_help_toggle" class="dbp_help_div">
        <h4>Show/Hide</h4>
        <p>Scegli se il campo deve essere mostrato nel modulo di inserimento dei dati</p>
    </div>

    <div id="dbp_help_js" class="dbp_help_div">
        <h4>JS Script</h4>
        <p>Inserisci i javascript per personalizzare l'esperienza di inserimento.</p>
        <p>Esempio: Valido il campo solo se è compreso tra 0 e 100</p>
        <pre class="dbp-code">
 field.valid_range(0,100);
        </pre>
        <p>Esempio: Mostro il campo se il valore di un'ipotetica checkbox è uguale a 1</p>
        <pre class="dbp-code">
 field.toggle(form.get('mycheckboxlabel').val() == 1);
        </pre>
        <a href="<?php echo admin_url("admin.php?page=dbp_docs&section=js-controller-form") ?>" target="blank" class="js-simple-link">Approfondisci</a>
    </div>
    
    <div id="dbp_help_default" class="dbp_help_div">
        <h4>Default Value</h4>
        <p>È il valore che viene presentato quando si inserisce un nuovo record. È possibile inserire shortcode del template engine.</p>
        <p> Example:</p>
        <pre class="dbp-code">[^user.id]
[^NOW]
[^request.xxx]</pre>
    </div>
    <div id="dbp_help_class" class="dbp_help_div">
        <h4>Custom css class</h4>
        <p>Aggiungi una o più classi css nel field.</p>
        <p>Puoi allineare uno accanto all'altro due campi aggiungendo ai due campi la classe <b>dbp-form-columns-2</b>.
        <p>Per i checkboxes e i radio è possibile impaginare le opzioni in più colonne aggiungendo uno dei seguenti custom css class:<br>
        dbp-form-cb-columns-2, dbp-form-cb-columns-3, dbp-form-cb-columns-4 </p>
    </div>

    <div id="dbp_help_lookup" class="dbp_help_div">
        <h4>Lookup field</h4>
        <p> I campi di ricerca vengono utilizzati per collegare i dati con altre tabelle utilizzando la chiave primaria. <br>
        Il "campo della query WHERE" viene utilizzato per limitare i dati che verranno visualizzati. </p>

    <div id="dbp_help_delete" class="dbp_help_div">
        <h4>Delete field</h4>
        <p>Se la tabella è in DRAFT mode allora dal form è possibile modificarne la struttura.</p>
        <p>Delete field rimuove il campo e tutti i dati inseriti in quel campo</p>
        <p>Se non si vuole rimuovere la colonna o non si può modificare la tabella si può nascondere il campo dal select show/hide</p>
    </div>
    <div id="dbp_help_new_field" class="dbp_help_div">
        <h4>New field</h4>
        <p>Se la tabella è in DRAFT mode allora è possibile creare un nuovo campo nella tabella. Se si vuole avere più controllo nella creazione dei campi si può andare nella struttura della tabella e modificarla.</p>
    </div>
    <div id="dbp_help_calc_field" class="dbp_help_div">
        <h4>Calculated Field</h4>
        <p>The calculated fields are filled in upon saving with the formula you entered into the formula. You can copy a field using the template engine variables [%data.variable_name] or create new fields by requesting data from posts or users eg [^POST.title id = [%data.post_id]. To calculate the number of the newly created row you can use [%row], while if you want to create a new identifier you can use [^COUNTER]</p>
    </div>
    <div class="dbp_help_div">
        <h4>PHP Filter</h4>
        <p><a href="<?php echo admin_url("admin.php?page=dbp_docs&section=hooks") ?>" target="_blank" class="js-simple-link">apply_filters('dbp_save_data', $query_to_execute, $dbp_id, $origin)</a></p>
    </div>
    <br><br>
</div>
