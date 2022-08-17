<?php 
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>

<div class="dbt-content-table dbt-docs-content  js-id-dbt-content dbt-tutorial"  >
    <h2 class="dbt-h2"> <a href="<?php echo admin_url("admin.php?page=dbt_docs") ?>">Docs </a><span class="dashicons dashicons-arrow-right-alt2"></span> Tutorials <span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('Related post','database_tables'); ?></h2>
    <p>In this tutorial I will show you how to create a table that displays posts that have at least one tag in common with the post you are viewing. <br> First we need to create some posts that have the same tags. For example, you can create a series of posts composed as follows:</p>
    <table class="dbt-tutorial-table">
        <tr><td>Post title</td><td>Tag</td></tr>
        <tr><td>Venice</td><td>Italy</td></tr>
        <tr><td>Rome</td><td>Italy, Capital</td></tr>
        <tr><td>Milan</td><td>Italy</td></tr>
        <tr><td>Paris</td><td>France, Capital</td></tr>
        <tr><td>Marseille</td><td>France</td></tr>
    </table>

    <h2 class="dbt-h2">Creation of the list</h2>
    <p>In Database_tables in the menu on the right inside the tab "database tables actions" click on the link "SQL Command".</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_01.png" class="dbt-tutorial-img">

    <p>We write the following query:</p>
    <pre class="dbt-code">SELECT `p`.`ID`, `p`.`post_title` FROM `wps_posts` p LEFT JOIN `wps_term_relationships` tr ON `tr`.`object_id` = `p`.`ID` WHERE `p`.`post_status` = 'publish' AND `p`.`post_type` = 'post' GROUP BY `p`.`ID` ORDER BY `p`.`ID` DESC</pre>

    <p>We press the "go" button to execute the query. The query shows only the post IDs and titles. <br> Now we create the list from the query by pressing the button "Create list from query"</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_02.png"  class="dbt-tutorial-img">
    <p>We write the name "Related posts" and save. </p>
     <p> Now we have created our list. We are automatically sent to the page for the management of the data entry form. In this case we are not interested in the form module, but we go directly to the "Settings" tab. We fill in the scheme as the picture shows:</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_03.png"  class="dbt-tutorial-img">

    <p> This list is not intended to modify data so we do not need to have a specific link in administration. </p>
    <p> The options are used to decide how many elements per page and the ordering. </p>
    <p> Filters are the most interesting part of this tutorial. We want all the posts that have at least one tag equal to that of the post being viewed to be extracted. To do this we want tr.term_taxonomy_id to be equal to one of the ids of the tags (term) of the post being viewed. To get the id of the post you are viewing you can use the shortcode [^ current_post.id] <br>
    To get the list of the tag ids of an post we can use the function [^ get_post_tags]. Then writing [^ get_post_tags.term_id post_id = [^ current_post.id]]. The filter type is IN (Match in array). </p>
    <p> The second filter instead says that the id of the extracted posts must be different from the id of the post you are viewing. This is to avoid showing the same post you are viewing in related posts. </p>

    <p>"Delete Options" defines which tables to remove when removing list rows. In this case we don't want them to be removed so we tell all tables no.</p>

    <p>Save the settings</p>

    <h2 class="dbt-h2">Publication</h2>
    <p>The shortcode to publish the list can be found at the top right.</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_04.png"  class="dbt-tutorial-img">
    <p>You can publish the shortcode in the template so that it can be applied to all pages or to individual posts</p>
    <p>Altrimenti puoi usare il php per caricare la lista e pubblicarla a tuo piacere. Ecco un esempio per caricare la lista in fondo ai post.</p>
    <pre class="dbt-code">function add_dbt_list_to_content($content) {
	$append = '';
	if (is_single()) {
		$append = DatabaseTables\Dbt::get_list({list_id});
	}
	return $content.$append;
}
add_filter('the_content', 'add_dbt_list_to_content');</pre>

<h2 class="dbt-h2">Advanced settings</h2>
    <p>Let's go and improve our work! First of all we want to change the columns we are viewing so always inside the list settings we go to the "List view formatting" tab.</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_05.png"  class="dbt-tutorial-img">
    <p>To hide the ID we select Hide in "show in frontend" <br>
     To create a link to the post, click the pencil next to post_title, then on column type select special field> Custom. We write in the textarea: </p>
    <pre class="dbt-code">&lt;a href="[^LINK id=[%data.ID]]"&gt;[%data.post_title]&lt;/a&gt;</pre></p>
    <p>Let's add a custom column, that is, not linked to the data of the tables. We click "Add custom column". We want to show post tags. We will use the function [^get_post_tags]. </p>
     <p> If we want to show only the first tag we can write: </p>
    <pre class="dbt-code">[^get_post_tags.0.html post_id=[%data.ID]]</pre>
    <p>Se scrivessimo:</p>
    <pre class="dbt-code">[^get_post_tags.html post_id=[%data.ID]]</pre>
    <p>If there is more than one tag it would return an array for which we have to loop the results. We can do this using the tmpl attribute.</p>
    <pre class="dbt-code">[^get_post_tags post_id=[%data.ID] tmpl=[%item.html]]</pre>
    <p style="color:red">Always remember that between the attributes and the value there must never be spaces! So [^get_post_tags.html post_id = [%data.ID]] will not work because before and after the = symbol there are two spaces.</p>
</div>