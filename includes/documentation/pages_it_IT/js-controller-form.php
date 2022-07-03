<?php 
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>

<div class="dbt-content-table dbt-docs-content  js-id-dbt-content" >
    
<h2 class="dbt-h2"> <a href="<?php echo admin_url("admin.php?page=dbt_docs") ?>">Doc</a><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('LIST FORM js script','database_tables'); ?></h2>

    <p>Usando il javascript puoi migliorare l'esperienza di inserimento ad esempio nascondendo campi o verificando i dati inseriti.</p>
    <hr>
    <p>All'interno delle liste vai nel tab form.</p>
    <p>Dentro i singoli campi c'è una textarea per inserire del javascript custom.</p>  
    <p>Il javascript inserito viene eseguito  al caricamento della form, quando viene inviata e ogni volta che un elemento della form viene modificato. Puoi utilizzare la variabile <b>status</b> per identificare quando viene chiamata la funzione.</p>
    <pre class="dbt-code">
    if (status == 'start') {
        // Viene avviato il form
    }
    if (status == 'field_change') {
        // Viene modificato il campo
    }
    if (status == 'form_change') {
        // Viene modificato un qualsiasi altro campo della form
    }
    if (status == 'submit') {
        // Viene inviata al form. Puoi bloccare l'invio impostando un campo come invalid.
    }
    </pre>
    <p>Puoi usare la variabile <b>field</b> per riferirti al campo in cui stai scrivendo il codice. Questa variabile estende l'oggetto dbt_field che ha le seguenti funzioni:</p> 
    
    <ul>
        <li><b>field.val(val)</b> // val opzionale se impostato setta la variabile</li>
        <li><b>field.valid(boolean, msg)</b> // msg opzionale</li>
        <li><b>field.isValid()</b> // ritorna se è valido</li>
        <li><b>field.toggle(boolean)</b></li>
        <li><b>field.dom()</b> // ritorna il dom del campo</li>
        <li><b>field.addClass(class_name)</b></li>
        <li><b>field.removeClass()</b></li>
        <li><b>field.msg(str)</b> // Inserisce un messaggio sotto il campo</li>
        <li><b>field.valid_date()</b></li>
        <li><b>field.valid_range(min,max)</b></li>
        <li><b>field.choices()</b> // modifica le opzioni di un select</li>
        <li><b>field.required(boolean);</b></li>
    </ul>

    <p>Puoi fare riferimento agli altri campi della form attraverso la variabile form che estend dbt_form</p> 
            <ul>
                <li><b>form.get(field)</b> // field name | field  label </li>
                <li><b>form.get(table.field)</b> // current occurence</li>
                <li><b>form.get(table.field.number_of_occurence)</b></li>
                <li><b>form.get(table.field.next)</b></li>
                <li><b>form.get(table.field.prev)</b></li>
            </ul>
        </p>
    <br>
    <p> Non devi inserire il codice tag &lt;script&gt;&lt;/script&gt;.</p>
    <br>
    <p>Attenzione un campo required anche se nascosto rimane required! In generale un campo invalid nascosto impedisce alla form di essere inviata.</p>
    <h4>Esempi:</h4>
    <p>Mostro un campo solo se il checkbox è selezionato. Per il checkbox singolo val ritorna null se non è selezionato, altrimenti ritorna il valore del checkbox. Nell'esempio il checkbox ha value="1"</p>
    <pre class="dbt-code">
    field.toggle(form.get('mycheckboxlabel').val() == 1);
    </pre>
    <p>Mostro un campo solo se un'opzione particolare di un checkboxex è selezionata.</p>
    <pre class="dbt-code">
    field.toggle(form.get('n.categories').val().indexOf('Blue') > -1);
    </pre>
    <p>Setto i checkboxes con i valori delle opzioni (deve essere un array). Se voglio un defaul uso l'array nel campo di default.</p>
    <pre class="dbt-code">
    field.val(["opt_val1","opt_val5"]);
    </pre>

    <p>Valido un campo solo se è maggiore di 10</p>
    <pre class="dbt-code">
        field.valid( field.val() < 10, 'Il campo deve essere maggiore di 10');
    </pre>
    <p>Valido una data solo se l'inizio è superiore al campo date_start
    <pre class="dbt-code">
    let a = form.get('r.date_start').val();
    field.valid_range(a);
    </pre>
    <p>Valido il campo solo se è minore di 100
    <pre class="dbt-code">
    field.valid_range(false,100);
    </pre>
    <p>In una tabella ripetuta valido il campo next_order dicendo che deve essere maggiore dell'istanza  precedente</p>
    <pre class="dbt-code">
    if (status == 'form_change') {
    let prev_val = parseInt(form.get('my_table.next_order.prev').val());
    if (!isNaN(prev_val)) {
        field.valid( field.val() > prev_val);
    }
    }
    </pre>
    <p>Dati due select cambia le opzioni del secondo ogni volta che viene cambiato il primo</p>
    <pre class="dbt-code">
    if (status == 'start' || status == 'form_change') {
        if (form.get('PROVIN').val() == 'scelta 1') {
            field.toggle(true).choices({'1':"Male",'2':"Female"});
        } else if (form.get('PROVIN').val() == 'scelta 2') {
            field.toggle(true).choices(["Red","Blue"]);
        } else if (form.get('PROVIN').val() == 'scelta "3') {
            field.toggle(true).choices(["A","B","C"]);
        } else {
            field.toggle(false);
        }
    }
    </pre>


</div>