<?php 
namespace DatabasePress;
if (!defined('WPINC')) die;
?>

<div class="dbp-content-table dbp-docs-content  js-id-dbp-content dbp-tutorial"  >
    <h2 class="dbp-h2"> <a href="<?php echo admin_url("admin.php?page=dbp_docs") ?>">Docs </a><span class="dashicons dashicons-arrow-right-alt2"></span> Tutorials <span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('Galleries','database_press'); ?></h2>
    <p>In questo tutorial vedremo come creare un sistema di gallerie di immagini. Le immagini verranno gestite tramite la Media Library di wordpress.<br>La struttura sarà composta da due tabelle. La prima tabella avrà l'elenco delle gallerie di immagini avrà titolo e descrizione.<br>La seconda tabella avrà invece l'elenco delle immagini per ogni galleria e le colonne saranno: immagine, titolo e il riferimento alla galleria a cui appartengono.</p>

    <h2 class="dbp-h2">Iniziamo creando l'elenco delle gallerie di immagini</h2>
    <p>In database_press nel menu a sinistra clicchiamo su list e poi sul bottone <b>"CREATE NEW LIST"</b>.<br>Come nome scriviamo Galleries e lasciamo "create a new Table". Premiamo quindi il bottone salva.</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_01.png"  class="dbp-tutorial-img" style="zoom: 0.8;">
  
    <p>All'interno del <b>TAB FORM</b> premiamo il bottone <b>new field</b> e creiamo i seguenti campi:<br>
     <b>title</b>: Text (single line) required.<br>
     <b>description</b>: Classic text editor.<br>
     Premiamo <b>Salva</b> e generiamo così la prima tabella.</p>
    <p>Per ora fermiamoci qui e andiamo a creare la seconda tabella.</p>
    
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_02.png"  class="dbp-tutorial-img">

    <h2 class="dbp-h2">Creiamo l'elenco delle immagini</h2>
    <p>In database_press nel menu a sinistra clicchiamo su list e poi sul bottone <b>"CREATE NEW LIST"</b>. Come nome scriviamo <b>gallery_images</b> e lasciamo "create a new Table". Premiamo quindi il bottone salva.</p>
    <p>Nel <b>TAB FORM</b> creiamo le seguenti colonne:<br>
    <b>image</b> Media gallery<br>
    <b>gallery_id</b> Lookup. Nei Lookup params: Choose table: Gallerie  Label: title. Default [%request.gal_id]. Servirà successivamente per selezionare la galleria in automatico quando si inserisce una nuova immagine da un elenco filtrato attraverso il link di galleries.
    </p>

    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_03.png"  class="dbp-tutorial-img">

    <h2 class="dbp-h2">CONFIGURAZIONE</h2>
    <p>Andiamo sulla tab <b>LIST VIEW FORMATTING</b> e configuriamo le colonne come segue:
        <b>dbp_id</b>: Hide <br>
        <b>image</b> column type: media gallery<br>
        <b>gallery_id</b> column type: Lookup. Nei Lookup params: Choose table: wp_dbp_galleries  Label: title
    </p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_04.png"  class="dbp-tutorial-img">
    <p>Salviamo e andiamo sul tab <b>SETTINGS</b>.</p>
    <p>Ora possiamo associare ogni immagine ad una galleria di immagini. Sul menu di sinistra in amministrazione dovrebbero essere apparse due nuove voci: Galleries e gallery_images. È già possibile creare gallerie di immagini, ma dobbiamo fare in modo di poter visualizzare nel sito non tutte le immagini insieme, ma solo quelle della galleria desiderata. Dobbiamo quindi poter assegnare allo shortcode un parametro specifico. Andiamo quindi nel tab settings nella sezione  Filter (in frontend And admin plugin) e configuriamo i parametri come segue:</p>
    <p>
    Selezioniamo il campo <b>dbp.gallery_id</b> l'operatore uguale e come parametri scriviamo:</p>
    <pre class="dbp-code">[%params.gal_id default=[%request.gal_id]]</pre>
    <p>[%params.gal_id] vuole dire che prende un eventuale parametro gal_id passato dallo shortcode. Se questo non esiste verifica se viene invece passato lo stesso parametro dall'url. Il default ci servirà per poter filtrare i dati nella parte amministrativa.</p>

    <p>Nella sezione query modifichiamo la query aggiungendo un'altra colonna con l'id dell'immagine. Il risultato dovrebbe essere qualche cosa tipo questa: </p>
    <pre class="dbp-code">SELECT `dbp`.*, `dbp`.image AS image_id FROM `wps_dbp_gallery_images` `dbp`</pre>
    <p>Aggiungiamo una colonna con l'id dell'immagine perché la colonna originaria (image) la visualizzeremo come miniatura per cui ci tornerà un html e non l'id originale dell'attachment</p>.
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_05.png"  class="dbp-tutorial-img">


    <h2>Ultimi ritocchi su galleries</h2>
    <p>Infine nell'elenco delle gallerie di immagini vogliamo avere una colonna che ci mostri il totale delle immagini inserite per una specifica galleria e il link per modificare la singola gallery.</p>
    <p>Andiamo su list, selezioniamo Galleries ed infine scegliamo il tab <b>SETTINGS</b>. Sulla sezione Query cliccando sul bottone edit inline. Aggiungiamo una subquery alla query di base. La query dovrebbe risultare simile a questa:</p>
    <pre class="dbp-code">SELECT `dbp`.*, (SELECT COUNT(img.dbp_id) FROM `wp_dbp_galleries_images` img WHERE img.gallery_id = `dbp`.`dbp_id`) as images FROM `wp_dbp_galleries` `dbp`</pre>
    <p>Premiamo salva. Se la query non ha errori i setting verranno salvati.</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_06.png"  class="dbp-tutorial-img">
    <p>Andiamo sul tab <b>LIST VIEW FORMATTING</b> e nella nuova colonna images selezioniamo Column type Custom. Nella textarea aggiungiamo il seguente codice:</p>
    <pre class="dbp-code">&lt;a href=&quot;[^ADMIN_URL id={xxx} gal_id=[%data.dbp_id]]&quot;&gt;[%data.images]&lt;/a&gt;</pre>
    <p>Sostituiamo {xxx} con l'id della lista galleries_images</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_07.png"  class="dbp-tutorial-img">

    <h2>Disegniamo il frontend</h2>
    <p>Essendo una galleria di immagini la visualizzazione a tabella non è la più appropriata per cui andiamo a disegnarne una personalizzata</p>
    <p>Andiamo sul tab Frontend della lista galleries_images e su <b>List type</b>: selezioniamo custom invece che table</p>
    <p>Sulla sezione Header scrivamo:</p>
    <pre class="dbp-code">&lt;style&gt;
    .dbp-gallery-columns { display: grid; grid-template-columns: 1fr 1fr 1fr; align-items: center; justify-items: center; grid-gap: 2px; line-height: 0;}
.dbp-gallery-columns > div { border: 1px solid; line-height: 0; padding:.5rem; height: 100%; box-sizing: border-box; display: flex; align-items: center;}
&lt;/style&gt;
[^GET_LIST_DATA id={xxx} gal_id=[%params.gal_id default=[%request.gal_id]] tmpl=[:
&lt;h3&gt;[%item.title]&lt;/h3&gt;
&lt;p&gt;[%item.description]&lt;/p&gt;
:]]
&lt;div class=&quot;dbp-gallery-columns&quot;&gt;</pre>
<p>Fate attenzione a sostituire {xxx} con l'id della lista gallery. GET_LIST_DATA carica i dati da un'altra lista</p>
<p>Su <b>Loop the data</b> scriviamo</p>
<div class="dbp-code">&lt;div&gt;&lt;a href=&quot;[^LINK_DETAIL]&quot; class=&quot;js-dbp-popup&quot;&gt;[^IMAGE.image id=[%data.image_id] image_size=fit]&lt;/a&gt;&lt;/div&gt;
</div>
<p>Da notare che per visualizzare il popup con l'immagine utilizzeremo il sistema integrato dei dettagli. Per richiamare il popup il aggiungere al link la classe js-dbp-popup e come link ci basterà richiamare la funzione del template engine [^LINK_DETAIL].</p>

<p>Sul <b>Footer</b> ci basterà chiudere il div che abbiamo aperto per ordinare le immagini</p>
<div class="dbp-code">&lt;/div&gt;</div>
<p>Su <b>Detail view</b> Possiamo impostare vari tipi di popup su popup_style, base, large o fit. Fit ci permette di avere una finestra che si adatterà alle dimensioni del contenuto. Scegliamo su <b>Popup type</b> <b>FIT</b></p>
<p>Su <b>View type</b> scegliamo <b>CUSTOM</b> e all'interno dell'editor richiamiamo l'immagine grande richiedendo come dimensioni che si adatti alla finestra (winfit). Nella documentazione sono descritte le varie opzioni di image_size. Da notare che non possiamo usare la colonna image che nativamente avrebbe contenuto l'id dell'immagine perché dentro list view setting abbiamo impostato image come media gallery per cui la colonna restituisce la miniatura in html e non l'id della galleria di immagini.</p>
<div class="dbp-code">[^IMAGE.image id=[%data.image_id] image_size=winfit]</div>

<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_08.png"  class="dbp-tutorial-img">

<h2>Mostra il risultato finale</h2>
<p>Prima di tutto prova ad inserire un po' di dati all'interno delle gallerie di immagini. Creiamo due gallerie "gal01" e "gal02".<br>
Su "gal01" clicchiamo sullo 0 di images e andiamo ad inserire le foto.<br>SE tutto è stato configurato correttamente la form dovrebbe ricordarsi la galleria a cui sono associate le foto quando crei un nuovo contenuto. </p> <p>Finite di inserire le foto crea un articolo e associa il tag 
[ dbp_list id=165 gal_id={xxx} ]
<p>Sostituisci {xxx} con l'id della galleria fotografica che vuoi visualizzare. Il risultato dovrebbe essere simile a questo. Cliccando sulle foto dovrebbero ingrandirsi.</p>

<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_09.png"  class="dbp-tutorial-img">

</p>
</div>