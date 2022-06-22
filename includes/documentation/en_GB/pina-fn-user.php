<?php
/**
 * header-type:rif
 * header-title: SHORTCODE [^USER]
 * header-tags:post ^post [^user wp_query query user user_login author username
 * allegati articoli pagine immagini image
 * header-description: Leggo i dati di un utente di wordpress. <br> Es: [^USER.login id=1]
 * header-package-title: Template engine Functions
 * header-package-link: pina-fn-index.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
  <h3>^USER</h3>
  <p>Carica i dati degli utenti</p>
  <h4>Gli attributi per filtrare l'utente sono:</h4>
  <p>id|slug|email|login</p>
  <p>Se non viene inserito nessun parametro ritorna l'utente loggato</p>
  <h4>La funzione ritorna</h4>
  <ul class="pina-return-properties">
    <li>id: int</li>
    <li>login: string</li>
    <li>email: string</li>
    <li>reoles: array</li>
    <li>registered: string</li>
    <li>nickname: string</li>
    <li>wpx_capabilities: array</li>
    <li>wpx_user_level: int</li>
    <li>meta_*: Altri metadata</li>
  </ul>
  <h3>Esempi</h3>
  <pre class="dbt-code">
[^user.login id=1]
  </pre>
  <pre class="dbt-code">
[^IF "administrator" IN [^user.roles id=[%data.author]]]
  [^RETURN is administrator]
[^ELSE]
  [^RETURN is not administrator]
[^ENDIF]
  </pre>
</div>

