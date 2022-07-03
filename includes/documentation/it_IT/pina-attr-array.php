<?php
/**
 * header-type:rif
 *header-title: [SHORTCODE] Attributi di gruppi di dati (Array)
 * header-description: I gruppi di dati sono i post, gli utenti ecc..
 * header-package-title: Shortcode Attributes
 * header-package-link: pina-attr-index.php
 * header-tags:get, sep, qsep, if, mean, count
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <h2>Tmpl=</h2>
    <p>Sinonimi for</p>
    <p>Stampa i dati all'interno di un template. Questo può essere in una variabile*, un template in php esterno oppure scritto all'interno del valore dell'attributo. I dati dentro il template vengono ciclati all'interno della variabile [%item] e [%key] per il nome o il numero della variabile passata.</p>
    <p>* Le variabili vengono trasformate, ma il codice al loro interno non viene eseguito per problemi di performace... bisogna mettere [::] allora il codice all'interno se è una singola variabile viene rielaborata!
    <pre class="dbt-code">
        [^POST post_type=post tmpl=[:
        &lt;p&gt;[%key]=[%item.title]&lt;/p&gt;
        :]]
    </pre>

    <pre class="dbt-code">
    [%[&quot;1&quot;,&quot;2&quot;,&quot;3&quot;,&quot;4&quot;,&quot;5&quot;] for=[: 
    &lt;h2&gt;ID=[%item]&lt;/h2&gt;
    [^POST id=[%item] tmpl=[:
        &lt;p&gt;[%key]=[%item.title]&lt;/p&gt;
    :]]
    :]]
    </pre>

    <h2>.(variable_name)=</h2>
    <p>È possibile aggiungere valori ad un array semplicemente aggiungendo attributi preceduti dal punto</p>



    <h2>sep=</h2>
    <p>Accetta un parametro di testo. <br>unisce i valori di un array in un testo separato dal testo indicato. Sinonimo di implode in php</p>

    <h2>qsep=</h2>
    <p>Uguale a sep ma unisce con le virgolette il testo</p>

    <h2>if=</h2>
    <p>
    Mostra il campo se la condizione viene rispettata. La condizione la si può inserire tra virgolette oppure tra parentesi quadre con due punti [: ... :]
    </p>
    <pre class="dbt-code">
        [^POST type=post if=[: [%item.id]>30 :] length]
    </pre>
    <p>Conta il numero di articoli con id > 30</p>

    <pre class="dbt-code">
    [^POST type=post if=[:[%item.author_name] == 'admin':] ]
    </pre>
    <p>Estrae solo gli articoli con autore = 2</p>


    h2>sum</h2>
    <p>Fa la somma di un vettore</p>
    <p>TODO può essere passato un parametro aggiuntivo per cui fa la somma di un campo di un oggetto (ad esempio age, fa le somme dell'età degli utenti)</p>

    <h2>mean</h2>
    <p>Fa la media matematica</p>

    <h2>count</h2>
    <h4>Sinonimi: length</h4>
    <p>Se è un array conta il numero di righe. Se è una stringa conta il numero di caratteri</p>
    <pre class="dbt-code">
    [%["bar","foo"] length]&lt;br&gt;
    [%"foo" length]
    </pre>
    <div class="dbt-result">
        2 // è un array di due elementi
        3 // il numero di caratteri della stringa
    </div>


    <h2>get=</h2>
    <h4>Sinonimi: show, fields</h4>
    <p>Restituisce solo alcuni determinati campi di un array. Se l'array è associativo sostituisce il nome del campo con la nuova chiave</p>
    <pre class="dbt-code">[^post id=3 fields={"titolo":"title"}]</pre>
    <div class="dbt-result">
        array (size=1)<br>
        'titolo' => string '...' (length=14)<br>
    </div>
    <pre class="dbt-code">[^post type=page fields=["id","author","title"]]</pre>
    <div class="dbt-result">
        0 =><br>
        array (size=3)<br>
        'ID' => int 1<br>
        'author' => string '1' (length=1)<br>
        'title' => string '...' (length=9)<br>
        1 =><br>
        array (size=3)<br>
        'ID' => int 2<br>
        'author' => string '1' (length=1)<br>
        'title' => string '...' (length=15)<br>
    </div>
    <pre class="dbt-code">[^post type=page fields=id]</pre>
    <div class="dbt-result">
        array (size=2)<br>
        0 => int 1<br>
        1 => int 2
    </div>

    <pre class="dbt-code">[^post type=page fields={"id":"id", "autore":"author"] tmpl=table]</pre>
    <p>Stampa una tabella i cui titoli sono id e autore.</p>
</div>