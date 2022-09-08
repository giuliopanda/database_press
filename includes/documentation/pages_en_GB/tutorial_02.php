<?php 
namespace DatabasePress;
if (!defined('WPINC')) die;
?>

<div class="dbp-content-table dbp-docs-content  js-id-dbp-content dbp-tutorial"  >
    <h2 class="dbp-h2"> <a href="<?php echo admin_url("admin.php?page=dbp_docs") ?>">Docs </a><span class="dashicons dashicons-arrow-right-alt2"></span> Tutorials <span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('Galleries','database_press'); ?></h2>
    <p> In this tutorial we will see how to create an image gallery system. The images will be managed through the wordpress Media Library. <br> The structure will consist of two tables. The first table will have the list of image galleries will have title and description. <br> The second table will have the list of images for each gallery and the columns will be: image, title and the reference to the gallery they belong to. < / p>

    <h2 class="dbp-h2"> Let's start by creating the list of image galleries </h2>
     <p> In database_press in the menu on the left click on list and then on the button <b> "CREATE NEW LIST" </b>.<br> As the name we write Galleries and leave "create a new Table". We then press the save button.</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_01.png"  class="dbp-tutorial-img" style="zoom: 0.8;">
  
    <p> Inside the <b> TAB FORM </b> press the <b> new field </b> button and create the following fields: <br>
      <b> title </b>: Text (single line) required. <br>
      <b> description </b>: Classic text editor. <br>
      We press <b> Save </b> and thus generate the first table. </p>
     <p> For now, let's stop here and create the second table. </p>
    
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_02.png"  class="dbp-tutorial-img">

    <h2 class="dbp-h2"> Let's create the image list </h2>
     <p> In database_press in the menu on the left click on list and then on the button <b> "CREATE NEW LIST" </b>. As a name we write <b> gallery_images </b> and leave "create a new Table". We then press the save button. </p>
     <p> In the <b> TAB FORM </b> we create the following columns: <br>
     <b> image </b> Media gallery <br>
     <b> gallery_id </b> Lookup. In the Lookup params: Choose table: Galleries Label: title. Default [%request.gal_id]. It will then be used to automatically select the gallery when a new image is inserted from a list filtered through the galleries link.
     </p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_03.png"  class="dbp-tutorial-img">

    <h2 class="dbp-h2"> CONFIGURATION </h2>
     <p> Let's go to the <b> LIST VIEW FORMATTING </b> tab and configure the columns as follows:
         <b> dbp_id </b>: Hide <br>
         <b> image </b> column type: media gallery <br>
         <b> gallery_id </b> column type: Lookup. In the Lookup params: Choose table: wp_dbp_galleries Label: title
     </p>

    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_04.png"  class="dbp-tutorial-img">
    <p> Let's save and go to the <b> SETTINGS </b> tab. </p>
    <p> Now we can associate each image with an image gallery. Two new entries should have appeared on the left menu in administration: Galleries and gallery_images. It is already possible to create image galleries, but we must make sure that we can view not all the images in the site together, but only those of the desired gallery. We therefore need to be able to assign a specific parameter to the shortcode. So let's go to the settings tab in the Filter section (in frontend And admin plugin) and configure the parameters as follows: </p>
    <p>
    We select the <b> dbp.gallery_id </b> field the equal operator and as parameters we write: </p>
    <pre class="dbp-code"> [%params.gal_id default=[%request.gal_id]] </pre>
    <p> [%params.gal_id] means that it takes any gal_id parameter passed by the shortcode. If this does not exist, check if the same parameter is passed from the url instead. We will need the default to be able to filter the data in the administrative part. </p>

    <p> In the query section we modify the query by adding another column with the image id. The result should be something like this: </p>
    <pre class="dbp-code"> SELECT `dbp`.*,`dbp`.image AS image_id FROM `wps_dbp_gallery_images` `dbp` </pre>
    <p> We add a column with the id of the image because we will display the original column (image) as a thumbnail so we will return an html and not the original attachment id. </p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_05.png"  class="dbp-tutorial-img">


    <h2> Final touches on galleries </h2>
    <p> Finally, in the list of image galleries we want to have a column that shows us the total number of images entered for a specific gallery and the link to modify the single gallery. </p>
    <p> Let's go to list, select Galleries and finally choose the <b> SETTINGS </b> tab. On the Query section by clicking on the edit inline button. Let's add a subquery to the base query. The query should look something like this: </p>
    <pre class="dbp-code"> SELECT `dbp`. *, (SELECT COUNT (img.dbp_id) FROM` wp_dbp_galleries_images` img WHERE img.gallery_id = `dbp`.`dbp_id`) as images FROM` wp_dbp_galleries` ` dbp` </pre>
    <p> We press save. If the query has no errors the settings will be saved. </p>
    <img src="<?php echo plugin_dir_url (__DIR__);?>/assets/tutorial_02_06.png" class="dbp-tutorial-img">
    <p> Let's go to the <b> LIST VIEW FORMATTING </b> tab and select Column type Custom in the new images column. In the textarea we add the following code: </p>
    <pre class="dbp-code"> &lt;a href=&quot;[^ADMIN_URL id={xxx} gal_id=[%data.dbp_id]]&quot;&gt; [%data.images]&lt;/a&gt;</pre>
    <p> We replace {xxx} with the id of the galleries_images list.</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_07.png" class="dbp-tutorial-img">

    <h2> Let's draw the frontend </h2>
     <p> Being an image gallery, the table view is not the most appropriate so let's draw a custom one </p>
     <p> Let's go to the Frontend tab of the galleries_images list and to <b> List type </b>: select custom instead of table </p>
     <p> On the Header section we write: </p>
    <pre class="dbp-code">&lt;style&gt;
    .dbp-gallery-columns { display: grid; grid-template-columns: 1fr 1fr 1fr; align-items: center; justify-items: center; grid-gap: 2px; line-height: 0;}
.dbp-gallery-columns > div { border: 1px solid; line-height: 0; padding:.5rem; height: 100%; box-sizing: border-box; display: flex; align-items: center;}
&lt;/style&gt;
[^GET_LIST_DATA id={xxx} gal_id=[%params.gal_id default=[%request.gal_id]] tmpl=[:
&lt;h3&gt;[%item.title]&lt;/h3&gt;
&lt;p&gt;[%item.description]&lt;/p&gt;
:]]
&lt;div class=&quot;dbp-gallery-columns&quot;&gt;</pre>
<p> Be careful to replace {xxx} with the gallery list id. GET_LIST_DATA loads data from another list </p>
<p> On <b> Loop the data </b> we write </p>
<div class="dbp-code">&lt;div&gt;&lt;a href=&quot;[^LINK_DETAIL]&quot; class=&quot;js-dbp-popup&quot;&gt;[^IMAGE.image id=[%data.image_id] image_size=fit]&lt;/a&gt;&lt;/div&gt;
</div>
<p> Please note that we will use the built-in detail system to display the popup with the image. To call the popup, add the js-dbp-popup class to the link and as a link we just need to call the template engine function [^LINK_DETAIL]. </p>
<p> On the <b> Footer </b> we just need to close the div we opened to sort the images </p>
<div class="dbp-code"> &lt;/div&gt; </div>
<p> On <b> Detail view </b> We can set various types of popups on popup_style, base, large or fit. Fit allows us to have a window that will adapt to the size of the content. We choose on <b> Popup type </b> <b> FIT </b> </p>
<p> On <b> View type </b> we choose <b> CUSTOM </b> and inside the editor we recall the large image asking for dimensions that fit the window (winfit). The documentation describes the various image_size options. Note that we cannot use the image column which natively would have contained the image id because in the list view setting we have set image as media gallery so the column returns the thumbnail in html and not the id of the image gallery. </p>
<div class="dbp-code">[^IMAGE.image id=[%data.image_id] image_size=winfit]</div>

<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_08.png"  class="dbp-tutorial-img">

<h2> Show the final result </h2>
<p> First of all try to insert some data into the image galleries. Let's create two galleries "gal01" and "gal02". <br>
On "gal01" click on the 0 of images and go to insert the photos. <br> IF everything has been configured correctly, the form should remember the gallery to which the photos are associated when you create a new content. </p> <p> Once you have finished inserting the photos, create an article and associate the tag
[dbp_list id=165 gal_id={xxx}]
<p> Replace {xxx} with the id of the photo gallery you want to view. The result should look something like this. By clicking on the photos they should enlarge. </p>

<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_02_09.png"  class="dbp-tutorial-img">

</p>
</div>