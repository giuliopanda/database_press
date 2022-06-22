<?php
/**
 * header-type:rif
 * header-title: [SHORTCODE] Le Funzioni
* header-tags: funzioni, Post, AUTHOR, IS_, LINK NOW POST IMAGE USER
* header-description: Indice delle funzioni
 * header-package-title: Template engine 
 * header-package-link: pina-intro.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>Se una variabile è un contenitore di informazioni, Le funzioni sono un sequenze di istruzioni. Le funzioni sono identificate dal simbolo ^</p>
    <p>La loro sintassi è:
    <pre class="dbt-code">[^POST attributes]</pre>

<h3>[^USER]</h3>
<p>Carica un utente wordpress e i suoi metadata</p>
<p>I parametri che possono essere passati sono:<br>
  id|slug|email|login per il caricamento di un singolo utente.<br>Se non si passa nessun parametro allora viene caricato
  l'utente che si è loggato
</p>

<pre class="dbt-code">[^user]</pre>
<div class="dbt-result">
    {"login":"admin","email":"admin^admin.it","roles":["administrator"],"registered":"2020-01-08
    13:31:57","nickname":"admin"}
</div>

<pre class="dbt-code">[^IF [^user]==""]
non sei loggato
[^else]
Sei loggato
[^endif]</pre>
Esegue un codice diverso se si è loggati oppure no.


<h3>[^NOW]</h3>
<p>Torna la data di oggi</p>
<pre class="dbt-code">[^now]
[^now date-format="d-m-Y" date-modify="+2 days"]
[^now timestamp]
</pre>
<a href="<?php echo add_query_arg('get_page','pina-attr-date.php', $link); ?>" class="pina-doc-link">Approfondimenti: Attributi delle date</a>

<h3>[^POST]</h3>
<div class="dbt-result">
  "id","author","date":"2020-01-08 14:31:57","content","title","excerpt","status","comment_status","name","modified":"2020-12-28
        14:27:07","parent","guid","menu_order","type","mime_type","comment_count","filter"
  </div>
  <p>A questi si aggiungono tutti i post meta e i seguenti tag speciali. Questo gruppo di informazioni viene caricato solo se non è presente il tag light_load</p>   
 

<pre class="dbt-code">
  [^POST get=["title","author"] type=post tmpl=table]
</pre>
<p>Estrae il titolo e l'autore di tutti post e lo stampa tramite tabella (la tabella è un purticolare tipo di template gestito tramite i filtri di wordpress </p>
<pre class="dbt-code">[^POST id=2 .custom_link=[:&lt;a href=&quot;[^link page_id=[%item.id]]&quot;&gt;[%item.title]&lt;/a&gt;":]] </pre>
<p>Creo un campo link_custom con un link personalizzato per i post</p>


Approfondimenti: <a href="<?php echo add_query_arg('get_page','pina-fn-post.php', $link); ?>" class="pina-doc-link">^POST</a>
    

  <br>
  <h3>[^LINK]</h3>
  <p>Link come dice il nome stesso serve a generare un link del sito.<br>
  <p>Per la scelta della pagina o del post il parametro è page_id. SE page_id non è inserito, il link è alla pagina corrente. Qualsiasi altro parametro inserito viene registrato come nuovo elemento dell'url</p>
  <pre class="dbt-code">&lt;a href=&quot;[^LINK page_id=xxx id=yyy action=zzz]&quot;&gt;link&lt;/a&gt;</pre>


  <h3>[^SET]</h3>
  <p>Definisce nuove variabili</p>
  <pre class="dbt-code">
    [^SET variable=value var2=val2]
  </pre>
  <p>è possibile inserire istruzioni sia nei valori che nei nomi delle variabili purché si rispetti la regola di non mettere spazi</p>
  <pre class="dbt-code">
    [^SET ser='a:3:{s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;}' unser={"d":4,"e":5,"f":6}]
    [^SET nu=[%ser unserialize]]
    [%unser tmpl=[:[^SET nu.[%key]=[%item]]:]]
    [%nu]
  </pre>
  <p>Nell'esempio vengono impostati un array (unser) e una stringa serializzaa (ser). Poi viene settata una nuova variabile nu con l'array estratto da ser, infine viene ciclato unser e i suoi valori vengono inseriti in nu.</p>
  
  <h3>[^GET_THE_ID]</h3>
  <p>Torna l'id di un post, identico a get_the_id di wordpress</p>

  <h3>[^RETURN]</h3>
  <p>Cancella tutta la parte di testo fino a quel punto e stampa unicamente il valore del return. Questa funzione è molto utile se state scrivendo un codice che è composto da più righe per evitare che vengano stampta</p>
  <p>Non stamperà "Il mio risultato", ma solo il valore di my_var.</p>
  <pre class="dbt-code">
  Il mio risultato: [^RETURN [%my_var]]
  </pre>
  <p>Se si è dentro un ciclo esce dal ciclo e stampa il return senza però interrompere il flusso del codice</p>

  <h3>[^IS_USER_LOGGED_IN]</h3>
  <p>Torna 1 se l'utente è loggato altrimenti 0</p>

  <h3>is_*</h3>
  <p>[^IS_PAGE_AUTHOR], [^IS_PAGE_ARCHIVE], [^IS_PAGE_TAG], [^IS_PAGE_DATE], [^IS_PAGE_TAX]</p>
  <p>Torna 1 se è la pagina richiesta, altrimenti 0 </p>

  <h1>STATEMENTS AND FLOW CONTROL</h1>

  <h3>IF ELSE ENDIF</h3>
  <p>Le possibili condizioni sono: "==","!=","and","or", "&&", "||", ">=", "<=", "<>", " not in ", " in ", ">", "<", "!"</p>
  <p>Per maggiori informazioni sulle condizioni puoi vedere MATH</p>
  <pre class="dbt-code">
    [^IF 2 < 5]
      due è minore di cinque
    [^ENDIF]
  </pre>


  <h3>MATH</h3>
  <pre class="dbt-code"> [^MATH 3 + 1 + .5] [// 4.5 //]</pre>
  <pre class="dbt-code">[^MATH 3+2 * 2] [// 7 //]
  [^MATH (3+2) * 2] [// 10 //]</pre>
  <pre class="dbt-code">[^MATH 2^3] [// 8 (2*2*2) //] </pre>
  <pre class="dbt-code">[^MATH 9^.5] [// 3 (radice quadra) //]</pre>

  Si possono usare gli operatori di relazione > >= < <= != o <> in, not in, ! 
  <pre class="dbt-code"> [^MATH 2 > 1 ] [// 1 //]</pre>
  <pre class="dbt-code"> [^MATH 2 <> 1 ] [// 1 //]</pre>
  <pre class="dbt-code"> [^MATH 2 in ["1","3","4"] ] [// 0 //]</pre>
  <pre class="dbt-code"> [^MATH 2 not in ["1","3","4"] ] [// 1 //]</pre>
  In math è possibile usare anche gli operatori logici: AND && OR || Tornerà 1 se vero 0 se falso
  <pre class="dbt-code"> [^MATH 4 > 5 OR (3 == 3 AND 2 == 2) ] [// 1 //]</pre>

  <h3>FOR...ENDFOR</3>
  <p>Cicla un array</p>
  <p><b>Attributi:</b><br>
      <b>EACH=</b> imposta l'array da ciclare. Setta anche il nome della variabile a cui passare i valori in automatico a partire dalla prima parola incontrata.<br>
      <b>VAL=</b> Il nome della variabile a cui passare i dati.<br>
      <b>KEY=</b> Se è un array associativo il nome della chiave dell'array.<br>
  </p>

  <pre class="dbt-code">&lt;ul&gt;
  [^FOR EACH=[^POST TYPE=post]]
  &lt;li&gt;[%item.title_link]&lt;/li&gt;
  [^ENDFOR]
  &lt;/ul&gt;
  </pre>
  <p>Genera un elenco di titoli linkabili dei post.</p>

  <p>Per interrompere un ciclo si può usare l'istruzione break.</p>
  <pre class="dbt-code">
<ul>
    [^FOR EACH=["foo","bar","pippo"] VAL=var]
    [^break [%var] == "bar"]
    <li>[%var]</li>
    [^ENDFOR]
</ul>
  </pre>
  <div class="dbt-result">&bull; foo</div>

  <h2>WHILE... ENDWHILE</h2>
  <p>Esegue un ciclo fin tanto che la condizione viene soddisfatta</p>
  <p>Per maggiori informazioni sulle condizioni puoi vedere MATH</p>
  <pre class="dbt-code">
[^WHILE [%var set+=1] < 10]
    [^SET ris.[]=[%var]]
    [^BREAK [%var]>5]
[^ENDWHILE]
[^RETURN [%ris SEP=;]]
  </pre>
  <div class="dbt-result">1 , 2 , 3 , 4 , 5 , 6</div>

  <h2>[^BREAK]</h2>
  <p>Interrompe l'esecuzione di un ciclo FOR o WHILE o del blocco che si sta eseguendo.</p>
  <p>Break viene eseguito se è soddisfatta una condizione. Se non vengono inserite condizioni allora blocca sempre L'esecuzione</p>
  <pre class="dbt-code">
      [^IF 3==3]
      Lorem ipsum dolor sit amet,
      [^BREAK]
      consectetur adipiscing
      [^ENDIF]
      Donec et accumsan nulla, at tempus metus
  </pre>
  <div class="dbt-result">Lorem ipsum dolor sit amet, Donec et accumsan nulla, at tempus metus</div>
  <p>Non stama "consectetur adipiscing" perché successivo al break, ma stamap "Donec ..." perché fuori dal blocco if. Break infatti interrompe l'esecuzione di un blocco o di un ciclo.</p>

  <h3>Commenti</h3>
  <p>I commenti non vengono stampati nella pagina</p>
  <pre>[// commento //]</pre>

  <h3>[^BLOCK... ENDBLOCK</h3>
  <p>Cattura il codice all'interno del tag e lo imposta in una variabile senza eseguirlo. Il codice verrà poi eseguito quando la variabile verrà richiamata. È la cosa più vicina ad una funzione che c'è.</p>

  <h3> Approfondimenti</h3>
  <?php Dbt_fn_documentation::echo_menu('Template engine Functions'); ?>
  
</div>


