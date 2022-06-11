<?php
/**
 * Crea e gestisce la pagina delle opzioni
 */

/**
 * Aggiunge una voce di menu sotto le opzioni. in amministrazione 
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;

function pinacode_plugin_menu() {
	add_options_page( 'PinaCode options', 'PinaCode', 'manage_options', 'pinacode_options', 'pinacode_options' );
}
add_action( 'admin_menu', 'pinacode_plugin_menu' );

/** 
 * La pagina html del form delle opzioni
 */
function pinacode_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    // Carico l'html del form
    require_once(dirname(__FILE__) . "/view-option-page.php");
}

/** 
 * REGISTRO I PARAMETRI: Serve per il salvataggio outomatico dei parametri da parte di wordpress
 */
function register_pinacode_plugin_settings() {
	//register our settings
	register_setting( 'pinacode-group', 'pinacode_when_execute' );
}
add_action( 'admin_init', 'register_pinacode_plugin_settings' );