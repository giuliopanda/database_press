<?php
/**
 * header-type:doc
 * header-title: [SHORTCODE] Le variabili
 * header-order: 01
 * header-tags:variables % array
 * header-description: Gestione delle variabili
 * header-lang:ITA
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>Le variabili vengono richiamate con shortcode che iniziano per %. Le funzioni invece ocn shortcode che iniziano con ^</p>
    <pre class="dbt-code">[%myvar]</pre>
    Stampa il contenuto di myvar.
    <p>Per impostare il contenuto di una variabile è possibile scrivere:</p>
    <pre class="dbt-code">[^SET myvar="FOO" mynewvar="bar"]
        [%myvar set="FOO"]</pre> 
    <p>La differenza tra le due scritture è che nel primo caso la funzione [^SET ...] imposta una o più variabili senza stamparle, mentre nel secondo esempio la variabile viene impostata e stampata</p>
    <p>Gli shortcode possono lavorare anche con contenuti non impostati in variabili:</p>
    <pre class="dbt-code">[%"Ecco il mio testo"] [//  stampa il testo //]
        [%[1,2,3,4,5,6,7,8,9] ] [// stampa il json dell'array //]</pre>

    <h2>Gli oggetti</h2> 
    <p>Tra oggetti e array non c'è distinzione in pinacode. Tutti i contenuti che hanno al loro interno più informazioni (tipo un post di wordpress) sono oggetti. Questi possono essere settati scrivendo i dati in json (con virgolette doppie). </p>   
    <pre class="dbt-code">
    [^SET person={"name":"Foo", "age":"24"}]
    name: [person.name] age:[person get=age]
    </pre>

    <p>Per aggiungere nuove righe:</p>
    <pre class="dbt-code">
    [^SET people.[]=[%person]]
    [^SET people.[]=[%person2]]
    </pre>
    oppure
    <pre class="dbt-code">
    [^SET people.0=[%person]]
    [^SET people.1=[%person2]]
    </pre>
    <p>Attenzione non è possibile aggiungere parametri dinamici dentro un json</p>
    <pre class="dbt-code">
    [^SET people=[[%person],[%person2]]] [// NON FUNZIONA //]
    </pre>
    <p>Se si vuole modificare una poprietà di un oggetto bisognerà scrivere </p>
    <pre class="dbt-code">[^SET person.age=32]</pre> 

    <p>Creando un attributo con il punto Aggiunge una proprietà ad una variabile.</p>
    <pre class="dbt-code">[^POST type=post .myvar="custom"]</pre>


    <h2>IN PHP</h2>
    <pre class="dbt-code">
    &lt;?php 
        PinaCode-&gt;set_var(&quot;myvar&quot;,&quot;foobar&quot;); 
        echo PinaCode-&gt;get_var(&quot;myvar&quot;,&quot;default&quot;); 
    ?&gt;
    </pre>

    <h2>GET DATA</h2>
    <h3>TRAMITE SHORTCODE</h3>
    <p>Ogni variabile genera uno shortcode con il nome della variabile stessa. Per stampare una variabile basterà quindi scrivere nell'articolo stesso</p>
    <pre class="dbt-code">[%myval]</pre>

    <p>Se è un oggetto è possibile richiamare le proprietà dell'oggettro tramite</p>
    <pre class="dbt-code">[%person.age]</pre>
    <p>Questo ritornerà l'età della persona.</p>

    <p>Se è un array di oggetti allora ritornerà l'elenco delle età di tutte le persone</p>
    <pre class="dbt-code">[%people.age]</pre>

    <p>Per avere l'età di una persona specifica (ad esempio la prima) bisognerà scrivere</p>
    <pre class="dbt-code">[%people.0.age]</pre>

    <pre class="dbt-code">
        [^POST.title type=post] [// La funzione post estre gli articoli di wordpress, se si chiede il titolo verranno estratti solo i titoli degli articoli selezionati //]
    </pre>

    <h3>IN PHP</h3>
    <pre class="dbt-code">
    &lt;?php 
        $myreg = new PcRegistry();
        echo PinaCode-&gt;get_var(&quot;myvar&quot;);
        echo $myreg-&gt;get_var(&quot;people.0.age&quot;);
    ?&gt;
    </pre>

    <h2>USO PARTICOLARE DEI DATI</h2>
    <pre class="dbt-code">
    [^SET person=giulio]
    Stampa person: [[person] uppercase]
    </pre>
    <pre class="dbt-code">
    Stampa person: ["Giulio" uppercase]
    </pre>
    Array:
    <pre class="dbt-code">
    [SET person={"name":"Foo", "age":"24"}]
    [[person.name]]<br>
    [person get=age]
    [SET person2={"name":"Bar", "age":"32"}]
    <hr>
    <p> PEOPLE 1 NAME: [people.1.name]</p>
    </pre>
</div>