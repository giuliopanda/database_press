<?php
/**
 *header-type:doc
 * header-title: [SHORTCODE] Le immagini
 * header-tags:post ^post image ^image  attachment
 * header-description: Come gestire le immagini
 * header-package-title: Template engine Functions
 * header-package-link: pina-fn-index.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
  <a href="<?php echo $link; ?>" class="pina-doc-breadcrumb">HOME</a> &gt; <a href="<?php echo add_query_arg('get_page','fn-index.php', $link); ?>" class="pina-doc-breadcrumb">Funzioni</a>
  <br><br>
  <h1>Carica le immagini [^IMAGE]</h1>
  <p>Carica le immagini </p>
  <b>Gli attributi sono gli stessi di <a href="<?php echo add_query_arg('get_page','post.php', $link); ?>" >^POST</a></b>
  <p>Viene aggiunto:</p>
  <p><b>post_id</b>= Trova tutte le immagini collegate ad un singolo post<br></p>
  <p><b>light_load</b>= è impostato su 0 (nei post è impostato su 1). Se si vuole caricare tutto si deve inserire light_load=0<br></p>
  <h3>La funzione ritorna</h3>
  <p>Ritorna gli stessi parametri di POST</p>
  <ul class="pina-return-properties">
    <li>image: html</li>
    <li>image_link: text</li>
    <li>image_id: int</li>
    <li>*attachment_width: int</li>
    <li>*attachment_height: int</li>
    <li>*attachment_file: int</li>
    <li>*attachment_sizes: Array</li>
    <li>*attachment_image_meta: Array</li>
    <li>id: int</li>
    <li>author: text</li>
    <li>*author_id: int</li>
    <li>*author_name: text</li>
    <li>*author_roles: array</li>
    <li>*author_email: text</li>
    <li>*author_link</li>
    <li>date: date</li>
    <li>content: text</li>
    <li>title: text</li>
    <li>*title_link: link</li>
    <li>*permalink: link</li>
    <li>guid: link</li>
    <li>excerpt: text</li>
    <li>status: text</li>
    <li>comment_status: text</li>
    <li>name: text</li>
    <li>modified: date</li>
    <li>parent: int</li>
    
    <li>menu_order: int</li>
    <li>type: text</li>
    <li>mime_type: text</li>
    <li>comment_count: int</li>
    <li>filter: text</li>
  
    <li>*read_more_link: link</li>

    <li>*[postmeta]</li>
    </div>
    <p>* Se viene inserito light_load=0 questi dati non vengono caricati. Altrimenti non verranno caricati. 
    <p>A questi si aggiungono tutti i post meta.</p>   
      
  <h3>Esempi</h3>
  <pre class="dbt-code">
  [^IMAGE class=my_gallery print=[%item.image] attr={"id":"myGallery"} ]
    :]]
  </pre>
</div>