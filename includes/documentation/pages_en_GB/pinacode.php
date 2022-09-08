<div class="dbp-content-table dbp-docs-content  js-id-dbp-content">
<h2 class="dbp-h2"> <a href="<?php echo admin_url("admin.php?page=dbp_docs") ?>">Doc</a><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('Template engine','database_press'); ?></h2>

<h1 style="border-bottom:1px solid #CCC">Introduction</h1>
<div class="dbp-help-p">
<p> In some parts of the plugin such as in the template or in the calculated fields it is possible to insert custom code through a template engine. <br> This is divided into two main concepts: variables and functions. </p>

<p> You can otherwise execute the template engine instructions via the <b> dbp_tmpl_engine </b> shortcode. Add a shortcode block from the editor and type:
<pre class="dbp-code">[dbp_tmpl_engine]2*2 = [^MATH 2 * 2] [/dbp_tmpl_engine]</pre></p>

<p> In lists, for example, the extracted data is saved in the variable [%data]. All variables are called with shortcodes starting with%. On the other hand, functions with shortcodes starting with ^ and always behave the same way in every part of the code. </p>
    <pre class="dbp-code">[%data.post_title]</pre>
    It will print the title of a post in a list where posts are extracted.  To retrieve the properties of an object via shortcodes, a period is used.
</div>

    <h1 style="border-bottom:1px solid #CCC">STATEMENTS AND FLOW CONTROL</h1>
    <div class="dbp-help-p">
        <h3>[^MATH]</h3>
        <div class="dbp-help-p">
            <pre class="dbp-code">[^MATH 3 + 1 + .5] [// 4.5 //]
[^MATH 3+2 * 2] [// 7 //]
[^MATH (3+2) * 2] [// 10 //]
[^MATH 2^3] [// 8 (2*2*2) //]
[^MATH 9^.5] [// 3 (square root) //]</pre>

            Relation operators can be used > >= < <= != o <> in, not in, ! 
            <pre class="dbp-code">[^MATH 2 > 1 ] [// 1 //]
[^MATH 2 <> 1 ] [// 1 //]
[^MATH 2 in ["1","3","4"] ] [// 0 //]
[^MATH 2 not in ["1","3","4"] ] [// 1 //]</pre>
        In math you can also use the logical operators: AND && OR || It will return 1 if true 0 if false
            <pre class="dbp-code"> [^MATH 4 > 5 OR (3 == 3 AND 2 == 2) ] [// 1 //]</pre>
        </div>
     

    <h3>[^FOR] ... [^ENDFOR]</h3>
    <div class="dbp-help-p">
        <p>Cycle the data.</p>
        <h4 class="dbp-h4">Attributes:</h4>
        <ul>
            <li><b>EACH=</b> sets the array to cycle. It also sets the name of the variable to which to pass the values automatically starting from the first word encountered.
            </li>
            <li><b>VAL=</b> The name of the variable to pass the data to.</li>
            <li><b>KEY=</b> If it is an associative array, the name of the array key.</li>
        </ul>
<pre class="dbp-code">&lt;ul&gt;
    [^FOR EACH=[%data] KEY=mykey VAL=myItem]
        &lt;li&gt;[%mykey] = [%myItem]&lt;/li&gt;
    [^ENDFOR]
&lt;/ul&gt;
[// Or if you don't use KEY and VAL //]

&lt;ul&gt;
    [^FOR EACH=[%data]]
        &lt;li&gt;[%key] = [%item]&lt;/li&gt;
    [^ENDFOR]
&lt;/ul&gt;
</pre>

    <p>Generate a list of linkable post titles. The break statement can be used to break a loop.</p>
        <pre class="dbp-code">&lt;ul&gt;
    [^FOR EACH=[^POST TYPE=post]]
        &lt;li&gt;[%item.title_link]&lt;/li&gt;
    [^ENDFOR]
&lt;/ul&gt;</pre>
       
        <pre class="dbp-code">&lt;ul&gt;
    [^FOR EACH=[&quot;foo&quot;,&quot;bar&quot;,&quot;pippo&quot;] VAL=var]
    [^break [%var] == &quot;bar&quot;]
    &lt;li&gt;[%var]&lt;/li&gt;
    [^ENDFOR]
&lt;/ul&gt;</pre>
        <b>The above example will output:</b>: <br>
        <div class="dbp-result">&bull; foo</div>
    </div>            
    <h2>[^WHILE] ... [^ENDWHILE]</h2>
    <div class="dbp-help-p">
        <p>It loops until the condition is met. For more information on the conditions you can see MATH</p>
        <pre class="dbp-code">[^WHILE [%var set+=1] < 10]
    [^SET ris.[]=[%var]]
    [^BREAK [%var]>=5]
[^ENDWHILE]
[^RETURN [%ris SEP=;]]</pre>
        <b>The above example will output:</b>: <br>
        <div class="dbp-result">1 , 2 , 3 , 4 , 5</div>
    </div>
    <h2>[^BREAK]</h2>
    <div class="dbp-help-p">
        <p> Stops execution of a FOR or WHILE loop or the block being executed. </p>
        <p> Break is executed if a condition is met. If no conditions are entered then it always blocks execution </p>
        <pre class="dbp-code">[^IF 3==3]
    Lorem ipsum dolor sit amet,
    [^BREAK]
    consectetur adipiscing
[^ENDIF]
Donec et accumsan nulla, at tempus metus
        </pre>
        <p> It does not print "consectetur adipiscing" because it is after the break, but it prints "Donec ..." because it is outside the if block. In fact, Break interrupts the execution of a block or a loop. </p>
        <b>The above example will output:</b>: <br>
        <div class="dbp-result">Lorem ipsum dolor sit amet, Donec et accumsan nulla, at tempus metus</div>
    </div>   
        
    <h3>[// COMMENT //]</h3>
    <div class="dbp-help-p">
        <p>Comments are not printed on the page</p>
        <pre>[// commento //]</pre>
    </div>
    <h3>[^BLOCK] ... [^ENDBLOCK]</h3>
    <div class="dbp-help-p">
        <p> Capture the code inside the tag and set it in a variable without executing it. The code will then be executed when the variable is called. That's the closest thing to a function there is. </p>
    </div>
</div>

<h1 style="border-bottom:1px solid #CCC">FUNCTIONS</h1>
<div class="dbp-help-p">
    <h3>[^USER]</h3>
    <div class="dbp-help-p">
        <p> Upload a wordpress user and his metadata. If no data is passed, it returns the current user<br>
         The parameters that can be passed are: <br>
             id | slug | email | login for single user upload. <br> If you do not pass any parameters then it is uploaded
             the user who logged in
         </p>
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
            <li>meta_*: other metadata</li>
        </ul>

        <pre class="dbp-code">[^user]</pre>
        <div class="dbp-result">{"login":"admin","email":"admin^admin.it","roles":["administrator"],"registered":"2020-01-08
            13:31:57","nickname":"admin"}</div>

        <pre class="dbp-code">[^IF [^user]==""]
    you are not logged in
[^else]
    You are logged in
[^endif]</pre>
        Runs a different code whether you are logged in or not.
    </div>


    <h3>[^NOW]</h3>
    <div class="dbp-help-p">
        <p>Today's date returns</p>
        <pre class="dbp-code">[^now]
[^now date-format="d-m-Y" date-modify="+2 days"]
[^now timestamp] </pre>
    </div>
    <h3>[^POST]</h3>
    <div class="dbp-help-p">
        <p> Retrieves post data given a post ID or other search parameters . </p>
         <h4> The attributes are </h4>
        <p> To check out a single article </p>
        <p> <b> id </b> = find the post with a certain id. If it's an array of ids, find all posts with those arrays <br> </p>
        <p> To check out more articles </p>
        <p>
        <b> type </b> = The post_type (post, page etc ...) By default it is post. If you want to upload images see the [^ POST] alias <br>
            <b> cat </b> = Find posts from a certain category or group of categories. Accept id, slug or array of ids <br>
            <b>!cat </b> = Find posts that are not in a particular category or group of categories. Accept the slug id or an array of ids <br>
            <b> author </b> = find posts for a particular author. If it is a number use the id otherwise the user_nicename (NOT THE NAME) <br>
            <b> slug </b> = Search for the slug. <br>
            <b> tag </b> = Certain a post that has at least one of the tags selected. you can write them in an object or in a string. <br>
            <b> parent_id </b> = The id of the parent post. <br>
            <b> limit </b> = Limit the number of articles to display. By default 10. put -1 to have them all. <br>
            <b> offset </b> = Display articles starting with <br>
            <b> order </b> = The field to order on <br>
            <b> ASC </b> Ascending order <br>
            <b> DESC </b> Descending order <br>
            Show associated articles over a certain period of time. <br>
            <b> year </b> = The articles of a specific year (eg 2020) <br>
            <b> month </b> = The articles of a particular month (1-12) <br>
            <b> week </b> = The articles of a specific week (week) <br>
            <b> day </b> = The articles of a certain day (day) <br>
            <b> first </b> = Show first articles posted. By default 5. Replaces order, asc, desc, limit <br>
            <b> last </b> = Shows the last articles posted. By default 5. Replaces order, asc, desc, limit <br>
        </p>
        <p> Search in postMeta: </p>
         <p> You can search postmetas by entering the filter type in the meta_query attribute. If you want to search for more parameters, these can be added divided by spaces. They will automatically be connected as AND. If you want to add OR and AND within the search, they are inserted as functions. The conditions within the function are linked by the inserted logical conjunction. </p>
        <pre class="dbp-code">meta_query=[: 
    AND(a>=b
    OR (
        b<=var c!=ccc L IN (3,2,5,3,52,34) ) .c LIKE ("% ") 
        .d=" [%foo]" param=)
    c> 2 
:]</pre>
        <p> Other parameters: </p>
         <p> <b> read_more </b> = The text to put in the link_read_more variable. If not present it adds .... If light_load is present the tag is unusable </p>
         <p> <b> image_size </b> = The size of the opening image: thumbnail, medium, large, full. If not set, upload post-thumbnail </p>
         <p> <b> light_load </b> Excludes post_meta and other added data from loading to simplify post management. Passing 0 or 1 is optional </p>
         <h4> The function returns </h4>
        <ul class="pina-return-properties">
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
        <li>*image: html</li>
        <li>*image_link: text</li>
        <li>*image_id: int</li>
        <li>*[postmeta]</li>
        </ul>
        <p> If the light_load attribute is present this data is not loaded. If the post type is attachment and mime_type is image, permalink, image and image_link are still loaded. <br>
         To these are added all the post meta. </p>
            
        <h4>Examples</h4>
        <pre class="dbp-code">[^POSt id=XX for=[:
    [%item for=[:&lt;p&gt;&lt;b&gt;[%key]&lt;/b&gt;: [%item trim_words] &lt;/p&gt;:]]
:]]</pre>

        <pre class="dbp-code">[^POST get={"id":"id","Titolo":"title_link", "Autore"=>"author_name"} type=post tmpl=table]</pre>


    </div>

    <h3>[^IMAGE]</h3>
    <div class="dbp-help-p">
        <p>Get the image data from the attached ID</p>
       
        <h3>Params</h3>
        <b> The attributes are the same as [^POST]. In particular you can use: </b>
        <p> <b> id </b> = find the image with a certain id. If it's an array of ids, find all images with those arrays <br> </p>
        <p>: <b> image_size </b> = The size of the opening image: thumbnail, medium, large, full, fit, winfit. If not set, upload post-thumbnail. <b>fit</b>: enlarge the image to the maximum size of its container (width = 100%). <b>winfit</b>: resizes the image in proportion to the screen. See tutorial 2 for more information. </p>
         
        <p> Added: </p>
        <p> <b> post_id </b> = Find all images linked to a single post <br> </p>
        <p> <b> light_load </b> = is set to 0 (in posts it is set to 1). If you want to load everything you have to enter light_load = 0 <br> </p>
        <h3> The function returns </h3>
        <p> Returns the same parameters as POST </p>
        <ul class="pina-return-properties">
            <li>image: html</li>
            <li>title: text</li>
            <li>*title_link: link</li>
            <li>*permalink: link</li>
            <li>url: string</li>
            <li>original_url: string</li>
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
        </ul>
        <p> * If light_load = 0 is entered this data is not loaded. Otherwise they will not be loaded.
        <p> To these are added all the post meta. </p>
        
        <h3> Examples </h3>
        <pre class="dbp-code">[^IMAGE.image id=[%data.image_id] class=my_gallery attr={"id":"myGallery"} sep=" " ]</pre>
    </div>

      
    
    <h3>[^CURRENT_POST]</h3>
    <p>Return the post you are viewing</p>

    
    <h3>[^LINK]</h3>
    <p>The equivalent of get_permalink()</p>
    <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>id</b><br>
                (integer) the post id</li>
                <li><b>some_other_params</b><br>
                (string) You can add other parameters that will be passed in the url</li>
        </ul>
    <div class="dbp-help-p">
        <p> Link as the name implies is used to generate a link to the site. <br>
        For the choice of the pagethe parameter to be inserted is page_id. For a post it is post_id. If page_id or post_id are not inserted, the generated link will be to the current page. all other parameters that you enter are registered as a new url element </p>

        <h3> Examples </h3>
        <pre class="dbp-code">&lt;a href=&quot;[^LINK id=xxx filter=yyy action=zzz]&quot;&gt;link&lt;/a&gt;
&lt;a href=&quot;[^LINK id=[%post.ID]]&quot;&gt;[%post.post_title]&lt;/a&gt;</pre>
        <p>
        If you link to posts with custom post_type, remember to register the post_type on function.php of your template</p>
            <pre class="dbp-code">add_action( 'init', 'register_my_post_type' );
function register_my_post_type() {
    register_post_type(
    'my_post_type',
    array (
        'public'             => true,
        'rewrite'            => array('slug' => 'my_post_type'),
        'show_in_menu'      => false,
        'show_in_nav_menus' => false,
        'show_in_rest' 		=> false
    )
    );
}</pre>

    </div>

    <h3>[^ADMIN_URL]</h3>
    <div class="dbp-help-p">
    <p>Create a link to an administrative list. You can pass additional parameters to filter past data</p>
        <h4 class="dbp-h4">Parameters</h4>
        <ul>
                <li><b>id</b><br>
                (integer) The id of the list</li>
                <li><b>some_other_params</b><br>
                (string) You can add other parameters that will be passed in the url</li>
        </ul>
        <p>Example: In a list it shows the data of another filtered list and the button to go to edit the data. <br>
                1. Create a list linked to posts with a post_id column. <br>
                2. Add some records linked to posts. <br>
                3. Create a second list where you extract wordpress posts <br>
                4. On the new list open List view formatting and click add row at the bottom<br>
        </p>
        <pre class="dbp-code">[^GET_LIST ID=xx post_id=[%data.ID]]
        &lt;a href=&quot;[^ADMIN_URL id=xx post_id=[%data.ID]]&quot;&gt;Edit&lt;/a&gt;</pre>
    </div>


    <h3>[^SET]</h3>
    <div class="dbp-help-p">
        <p> Define new variables </p>
        <pre class="dbp-code">[^SET variable=value var2=val2]</pre>
        <p> To set the content of several variables, you can write: </p>
        <pre class="dbp-code">[^SET myvar="FOO" mynewvar="bar"]
[%myvar set="FOO"]</pre> 
        <p> The difference between the two writes is that in the first case the [^ SET ...] function sets one or more variables without printing them, while in the second example the variable is set and printed </p>
        <p> Shortcodes can also work with content not set in variables: </p>
        <pre class="dbp-code">[%"Here is my text"] [// print the text //]
    [%[1,2,3,4,5,6,7,8,9] ] [// print the json of the array //]</pre>
        <pre class="dbp-code">[^SET 
   a="Foo | part1 | part2.3" 
   b=[%a split=|] 
   c=[%b.2 split=.]
]
[^RETURN [%c.1]] [// output 3 //]</pre>

    <p> you can insert instructions both in the values and in the names of the variables as long as you respect the rule of not putting spaces </p>
    <pre class="dbp-code">[^SET ser='a:3:{s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;}' unser={"d":4,"e":5,"f":6}]
[^SET nu=[%ser unserialize]]
[%nu.c] | [%unser.d]</pre>
    <p> The example sets an array (unser) and a serialize string (ser). Then a new variable nu is set with the array extracted from ser, finally unser is cycled and its values are inserted into the variable nu. </p>
  </div>

  <h3>[^COUNTER]</h3>
  <div class="dbp-help-p">
   <p> Each time it is called it returns a new number in a number sequence </p>
   <h4> The optional attributes are </h4>
   <p> <b> name </b> = The name of the counter that wants to be called </p>
   <p> <b> start </b> = The number you want the counter to start from </p>
   <p> <b> step </b> = the number of units to increase or decrease the counter each time it is called </p>
  </div>

  <h3>[^GET_THE_ID]</h3>
  <div class="dbp-help-p">
    <p>Torna l'id di un post, identico a get_the_id di wordpress</p>
  </div>
  <h3>[^RETURN]</h3>
    <div class="dbp-help-p">
        <p> Delete all the text up to that point and print only the return value. This function is very useful if you are writing code that consists of multiple lines to prevent them from being printed </p>
        <p> It won't print "My result", just the value of my_var. </p>
        <pre class = "dbp-code"> My result: [^ RETURN [%my_var]] </pre>
    </div>


    <h3>[^IS_USER_LOGGED_IN]</h3>
    <div class="dbp-help-p">
        <p> Return 1 if the user is logged in otherwise 0 </p>
    </div>

    <h3>[^IS_ADMIN]</h3>
    <div class="dbp-help-p">
        <p>Return 1 if is admin page</p>
    </div>

  <h3>is_*</h3>
    <div class="dbp-help-p">
        <p>[^IS_PAGE_AUTHOR], [^IS_PAGE_ARCHIVE], [^IS_PAGE_TAG], [^IS_PAGE_DATE], [^IS_PAGE_TAX]</p>
        <p> Return 1 if it is the requested page, 0 otherwise </p>
    </div>

    <h3>[^GET_TAG] or [^GET_CAT]</h3>
    <div class="dbp-help-p">
        <p>Returns the data of the tag or category</p>
        <h4 class="dbp-h4">Attributes</h4>
        <ul>
            <li><b>id or term_id</b></li>
            <li><b>slug</b></li>
            <li><b>name</b></li>
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <ul>
            <li><b>id </b></li>
            <li><b>term_id</b></li>
            <li><b>name</b></li>
            <li><b>slug</b></li>
            <li><b>term_group</b></li>
            <li><b>taxonomy</b></li>
            <li><b>parent</b></li>
            <li><b>link</b></li>
            <li><b>html</b></li>
        </ul>
        <pre class="dbp-code">[^GET_TAG.html id=2]</pre>
    </div>


    <h3>[^GET_POST_TAGS] or [^GET_POST_CATS]</h3>
    <div class="dbp-help-p">
        <p>Returns the tag or category data of a post</p>
        <h4 class="dbp-h4">Attributes</h4>
        <ul>
            <li><b>post_id</b></li>
        </ul>
        <h4 class="dbp-h4">Return</h4>
        <ul>
            <li><b>id </b></li>
            <li><b>term_id</b></li>
            <li><b>name</b></li>
            <li><b>slug</b></li>
            <li><b>term_group</b></li>
            <li><b>taxonomy</b></li>
            <li><b>parent</b></li>
            <li><b>link</b></li>
            <li><b>html</b></li>
        </ul>
        <pre class="dbp-code">[^GET_POST_CATS.html post_id=2]</pre>
    </div>



    <h1 style="border-bottom:1px solid #CCC">List specific functions</h1>
    <h3>[^LINK_DETAIL]</h3>
    <p>Params item=[%data], dbp_id=xx, action=""</p>
    <div class="dbp-help-p">
        <p> Create link to show page detail </p>
    </div>
    <h4>Examples</h4>
    <pre class="dbp-code">&lt;a href=&quot;[^LINK_DETAIL]&quot; class=&quot;js-dbp-popup&quot;&gt;popup detail&lt;/a&gt;</pre>

    <h3>[^UNIQ_CHARS_ID]</h3>
    <p>Params item=[%data], dbp_id=xx</p>
    <div class="dbp-help-p">
        <p>Generate a unique string to retrieve the record. <br> This solves the problem that, if the query is composed of multiple tables, to extract a specific row you have to pass the ids of all the tables.</p>
        <h4>Examples</h4>
        <p>1. In list view formatting create a custom code and add this code:</p>
            <pre class="dbp-code">&lt;a href=&quot;?k=[^uniq_chars_id]&quot;&gt;[^uniq_chars_id]&lt;/a&gt;</pre>
        <p>2. In functions.php add the following code by replacing {the_list_id} with the id of the list you are working on.</p>
        <pre class="dbp-code">if (isset($_REQUEST['k'])) {
	var_dump(DatabasePress\Dbp::get_detail({the_list_id}, $_REQUEST['k']));
    die;
}</pre>
    </div>

    <h3>[^GET_LIST id=""]</h3>
    <p> Print a specific list. </p>

    <h3>[^GET_LIST_DATA list_id=""]</h3>
    <p> Extract data from a specific list </p>
    <pre class="dbp-code">[^GET_LIST_DATA id=xxx tmpl=[:
    &lt;h3&gt;[%item.title]&lt;/h3&gt;
    &lt;p&gt;[%item.description]&lt;/p&gt;
:]]</pre>

</div>  

  <h1 style="border-bottom:1px solid #CCC">VARIABLES</h1>

    <div class="dbp-help-p">
        <p> You can add variables or jsons as attribute values </p>
        <pre class="dbp-code">[%myvar set=[^POST last]] 
[%myvar set=["foo","bar"]]
[%myvar set={"a":"foo","b":"bar"}]</pre>
        <p> You can't put variables in place of attribute names </p>
        <pre class="dbp-code">[%myvar [%var]="foo"] [// NOT CORRECT //] </pre>
        <p>This is only allowed inside the [^ SET] function as long as there are no spaces</p>
        <pre class="dbp-code">[^SET [%var]="foo"] [// CORRECT //] 
[^SET mypost.[%var]="foo"] [// CORRECT //] </pre>

        <p>You can call the parameters of an object via the .*</p>
        <pre class="dbp-code">[%post.title] 
[%post.0.title] </pre>
        <p>This will return a string if there is only one post, otherwise an array of titles.</p>
    </div>
    
    <div class="dbp-help-p">
        <h3 class="dbp-h3">Attributes</h3>
        <div class="dbp-help-p">
            <p> Attributes modify a variable or function. They can have an associated value. </p>
             <p> <b style="color:red"> You CANNOT put the space next to the = symbol. </b> </p>
            <pre class="dbp-code">[%"string" uppercase]</pre>
            <div class="code">
                [%"1602288000" date-format="Y-m-d"]
            </div>

            <h2>default=</h2>
            <p>If the value or object is empty, it returns the default</p>
            <pre class="dbp-code">[%novar default="foo"]</pre>

            <h2>*=[::]</h2>
            <p> Attribute values must not have spaces. </p>
             <p> If you have to put a space you have to insert the text in quotation marks or the symbols [::]. Quotation marks within already quoted text must be added with a backslash </p>
            <pre class="dbp-code">[%myvar default=foobar] [// CORRECT //]
[%myvar default=foo bar] [// NOT CORRECT //]</pre>

            <pre class="dbp-code">[%myvar default=[:foo bar:]] [// CORRECT //]
[%myvar default="foo bar"] [// CORRECT //]
[%myvar default='foo bar'] [//  CORRECT //]</pre>

        </div>   
    </div>

<div class="dbp-help-p">
    <h3 class="dbp-h3">Check if it is</h3>
    <div class="dbp-help-p">
        <h3>is_string</h3>
        <div class="dbp-help-p">
        Check if the variable is a string
        </div>
        <h3>is_date</h3>
        <div class="dbp-help-p">
        Check if a variable is a valid date
        </div>
        <h3>is_object</h3>
        <div class="dbp-help-p">
        Checks if a variable is an array or an object
        </div>
    </div>
    

    <h3 class="dbp-h3">Attributes of texts</h3>
    <div class="dbp-help-p">
    <h3>upper - <span class="dbp-help-synonyms">(Synonyms: uppercase strtoupper)</span></h3>
   
    <div class="dbp-help-p">
        <p>Transform a string to all uppercase</p>
        <pre class="dbp-code">[%"foo" upper]</pre>
        <div class="dbp-result">FOO</div>
    </div>
    <h3>lower - <span class="dbp-help-synonyms">(Synonyms: strtolower lowercase)</span></h3>
    <div class="dbp-help-p">
        <p>Make a string all lowercase</p>
        <pre class="dbp-code">[%"MY FOO" lower]</pre>
        <div class="dbp-result">my foo</div>
    </div>
    
    <h3>ucfirst  - <span class="dbp-help-synonyms">(Synonyms: capitalize)</span></h3>
    <div class="dbp-help-p">
        <p>Capitalize the first character of a string</p>
        <pre class="dbp-code">[%"my foo" ucfirst]</pre>
        <div class="dbp-result">My foo</div>
    </div>

    <h3>strip-comment - <span class="dbp-help-synonyms">(Synonyms: strip_comment stripcomment)</span></h3>
    <div class="dbp-help-p">
        <p>Remove comments &lt;!-- --&gt; o // o /* */ </p>
        <pre class="dbp-code">[^SET myvar=" &lt;div&gt;testo&lt;/div&gt;  &lt;!-- a comment --&gt; &lt;i&gt;testo&lt;/i&gt;"]
[%myvar htmlentities]&lt;br&gt;
[%myvar strip-comment htmlentities]</pre>
        <div class="dbp-result"> &lt;div&gt;testo&lt;/div&gt; &lt;!-- a comment --&gt; &lt;i&gt;testo&lt;/i&gt;<br>
            &lt;div&gt;testo&lt;/div&gt; &lt;i&gt;testo&lt;/i&gt;</div>
        <pre class="dbp-code">[^SET myvar="&lt;script&gt; a =\&quot;foo\&quot;; 
/* other comment 
* multiline
*/
alert(a);
&lt;/script&gt;"]
[%myvar htmlentities nl2br]&lt;br&gt;&lt;hr&gt;
[%myvar strip-comment htmlentities nl2br]</pre>
        
    </div>

    <h3>strip-tags  - <span class="dbp-help-synonyms">(Synonyms: strip_tags striptags)</span></h3>
    <div class="dbp-help-p">
        <p>Remove all html tags from text</p>
        <h2>nl2br</h2>
        <p>Transform the accapi into br</p>
        <h2>htmlentities</h2>
        <p>Transform special characters into html entities</p>
        <pre class="dbp-code">
        &lt;textarea&gt;[%&quot;&lt;/textarea&gt;&lt;b&gt;fff&lt;/b&gt;&quot; htmlentities]&lt;/textarea&gt;
        </pre>
        <p>The example shows how through the htmlentities attribute it is possible to write html tags inside a textarea</p>
    </div>
    <h3>left=</h3>
    <div class="dbp-help-p">
        <p>Accept a numeric parameter. <br> Takes the first n characters of the text. Accept a second "more" attribute to add text if left has actually cut the string</p>
        <pre class="dbp-code">    [%"A1B2C3D4E5F6G7H8I9" left=5 more=" ..."]</pre>
        <div class="dbp-result"> A1B2C ...</div>
        <p>If the text is cut, you can add text at the end of the line using the more attribute</p>
        <pre class="dbp-code">
        [%"Hello George" left=5 more=" ..."]
        [%"good afternoon" left=25 more=" ..."]
        </pre>
        <p>In the first case it cuts the text and then puts the text of the more attribute, in the second case it doesn't cut the text so it doesn't put the text of the more</p>
        <div class="dbp-result">Hello ... good afternoon</div>
    </div>
    <h3>right=</h3>
    <div class="dbp-help-p">
        <p>Accept a numeric parameter. <br> Takes the first n characters of the text</p>
        <pre class="dbp-code">[%"Hello George" right=6]</pre>
        <div class="dbp-result">George</div>
    </div>
    <h3>trim_words=</h3>
    <div class="dbp-help-p">
        <p>Accept a numeric parameter. <br> Takes the first n words of the text</p>
        <p>if the text is cut it is possible to add a text at the end of the line using the more attribute</p>
        <pre class="dbp-code">[%"Hello George how are you?" trim_words=2 more=" [^link id=2 text="..."]"]</pre>
        <div class="dbp-result">Hello George <a href="#">...</a></div>
    </div>
    <h3>sanitize</h3>
    <div class="dbp-help-p">
        <p>Executes the function wordpress sanitize_text_field</p>
    </div>
    <h3>esc_url</h3>
    <div class="dbp-help-p">
        <p>Executes the function wordpress esc_url</p>
    </div>
    <h3>trim</h3>
    <div class="dbp-help-p">
        <p>Removes spaces before and after in text or all fields in an array</p>
    </div>
    <h3>split=</h3>
    <div class="dbp-help-p">
        <p>Divide a text into an array</p>
        <pre class="dbp-code">[^SET a=[%"Hello | World" split="|"]][%a.1] [// World //]</pre>
    </div>
    <h3>Search=</h3>
    <div class="dbp-help-p">
        <p>Returns 1 if it finds the substring or 0 if it doesn't.</p>
        <pre class="dbp-code">[%"Nel mezzo del cammin di nostra vita" search="nostra" ]</pre>
        <div class="dbp-result">1</div>
        <p>If instead passed, the replace parameter replaces the string</p>
        <pre class="dbp-code">[%"Nel mezzo del cammin di notra vita" search="notra" replace="&lt;b&gt;nostra&lt;/b&gt;" ]</pre>
        <div class="dbp-result">Nel mezzo del cammin di <b>nostra</b> vita</div>
    </div>
    <h3>if=</h3>
    <div class="dbp-help-p">
        <p>
        Show the field if the condition is met. The condition can be placed in quotation marks or in square brackets with a colon [: ... :]
        </p>
        <pre class="dbp-code">
            [^POST type=post if=[: [%item.id]>30 :] length]
        </pre>
        <p>Count the number of articles with id > 30</p>
    </div>
    <h3>set=</h3>
    <div class="dbp-help-p">
        <p>Imposta il valore di una variabile</p>
        <pre class="dbp-code">[%myvar set="foo"]</pre>
        <div class="dbp-result">foo</div>

        <p>set+= o set-= to add or subtract the passed variable</p>
    </div>
    <h3>zero= - <span class="dbp-help-synonyms">(Synonyms: empty)</span></h3>
    <div class="dbp-help-p">
        <p>Print alternate text if variable is 0 or empty.</p>
    </div>
    <h3>one= - <span class="dbp-help-synonyms">(Synonyms: singular)</span></h3>
    <div class="dbp-help-p">
        <p>Print alternate text if variable is 1.</p>
        <pre class="dbp-code">[^SET a=["foo"] ]
[%a count] [%a singular="Item" plural="Items"]</pre>
    </div>
    <h3>plural=</h3>
    <div class="dbp-help-p">
        <p>Print alternate text if variable is greater than 1.</p>
        <pre class="dbp-code">[^SET a=["3","23","65"] ]
[%a count] [%a singular="Item" plural="Items"]</pre>
    </div>

    <h3>negative=</h3>
    <div class="dbp-help-p">
        <p>Print alternate text if variable is negative</p>
    </div>

    <h3>decode_ids</h3>
    <div class="dbp-help-p">
        <p>On the detail page, the array of the record ids is returned.</p>
        <pre class="dbp-code">[^SET ids=[%request.dbp_ids decode_ids]] [%ids]</pre>
    </div>



    </div>


    <h3 class="dbp-h3">Attributi per i numeri</h3>
    <div class="dbp-help-p">
        <h3>set+=  set-=</h3>
        <div class="dbp-help-p">
        To add or subtract the passed variable.
        </div>

        <h3>decimal=</h3>
        <div class="dbp-help-p">
            <p>Sets the number of values after the comma to show. It accepts two more parameters dec_point and thousands_sep</p>
            <pre class="dbp-code">[%"1203.23" decimal=1]&lt;br&gt;
[%"1203.23" decimal=1 dec_point=, thousands_sep=.]</pre>
            <div class="dbp-result">1203.2
1.203,2</div>
        </div>
        
        <h3>euro</h3>
        <div class="dbp-help-p">
        Format a number as euro currency
        </div>
        <h3>floor</h3>
        <div class="dbp-help-p">
        Rounds down the value of a number
        </div>

        <h3>round</h3>
        <div class="dbp-help-p">
        Round the value by a number
        </div>

        <h3>ceil</h3>
        <div class="dbp-help-p">
        Rounds up the value of a number
        </div>
      
    </div>

    <h3 class="dbp-h3">Attributes for dates</h3>
    <div class="dbp-help-p">

        <h3>date-format=</h3>
        <div class="dbp-help-p">
            <p>Accept a text parameter. <br> Change the date format</p>
            <pre class="dbp-code">
            [%"2020-10-10" date-format='Y']
            </pre>
            <div class="dbp-result">2020</div>
            <p>It accepts either dates, timestamps or strings year month day all attached or even timed</p>
            <pre class="dbp-code">[%"1602288000" date-format="Y-m-d"]</pre>
            <div class="dbp-result">2020-10-10</div>
            <p>The following example takes an item.post_date variable in text day/month/year format, converts it to year-month-day and prints Y-m-d H:i:S</p>
            <pre class="dbp-code">[^SET date=[:[%item.post_date left=10 right=4]-[%item.post_date left=5 right=2]-[%item.post_date left=2]:]][%date date-format="Y-m-d H:is"]</pre>
        </div>
        <h3>date-modify=</h3>
        <div class="dbp-help-p">
            <p>Accept a text parameter. <br> Change a date</p>
            <pre class="dbp-code">[%"2020-10-10" date-modify="+2 days"]</pre>
            <div class="dbp-result"> 2020-10-12</div>
        </div>

        <h3>last-day</h3>
        <div class="dbp-help-p">
            <p>It takes a date and sets the last day of the month</p>
            <pre class="dbp-code">[%"2020-10-10" last-day]</pre>
            <div class="dbp-result">2020-10-31</div>
        </div>

        <h3>timestamp</h3>
        <div class="dbp-help-p">
            <p>It takes a date and converts it to a timestamp</p>
            <pre class="dbp-code">[%"2020-10-10" timestamp]</pre>
            <div class="dbp-result">1602288000</div>
        </div>


        <h3>datediff-year=</h3>
        <div class="dbp-help-p">
            Returns the difference in years between two dates
        </div>
        <h3>datediff-month=</h3>
        <div class="dbp-help-p">
            Returns the difference in months between two dates
        </div>
        <h3>datediff-day=</h3>
        <div class="dbp-help-p">
            <p>Returns the difference in days between two dates </p>
           
            <pre class="dbp-code">They have passed: [%a set='2001-10-04 10:20:10' datediff-day='2001-09-02 10:30:00']</pre>
        </div>
        <h3>datediff-hour=</h3>
        <div class="dbp-help-p">
            Returns the difference in hours between two dates
        </div>
        <h3>datediff-minute=</h3>
        <div class="dbp-help-p">
            <p>Returns the difference in minutes between two dates </p>
        </div>
    </div>


    <h3 class="dbp-h3">Attributi negli array</h3>
    <div class="dbp-help-p">
        <h3>Tmpl= - <span class="dbp-help-synonyms">(Synonyms: for, print)</span></h3>
        <div class="dbp-help-p">
            <p>Print data within a template. This can be in a variable *, an external php template or written inside the attribute value. The data inside the template is looped into the variable [%item] and [%key] for the name or number of the passed variable. </p>
             <p> * The variables are transformed, but the code inside them is not executed due to performance problems ... you have to put [::] then the code inside if it is a single variable is reworked! </p>
        <pre class="dbp-code">[^POST post_type=post tmpl=[:&lt;p&gt;[%key]=[%item.title]&lt;/p&gt;:]]
        </pre>

        <pre class="dbp-code">[%[&quot;1&quot;,&quot;2&quot;,&quot;3&quot;,&quot;4&quot;,&quot;5&quot;] for=[: 
    &lt;h2&gt;ID=[%item]&lt;/h2&gt;
    [^POST id=[%item] tmpl=[:
        &lt;p&gt;[%key]=[%item.title]&lt;/p&gt;
    :]]
:]]</pre>
        </div>
        <h3>.(variable_name)=</h3>
        <div class="dbp-help-p">
        You can add values to an array simply by adding dot-preceded attributes
        </div>
        <h3>sep=</h3>
        <div class="dbp-help-p">
        Accept a text parameter. <br> merges the values of an array into separate text from the specified text. Synonym for implode in php
        </div>
        <h3>qsep=</h3>
        <div class="dbp-help-p">
        Same as sep but joins the text with quotation marks
        </div>

        <h3>if=</h3>
        <div class="dbp-help-p">
            <p>
            Show the field if the condition is met. The condition can be inserted in quotation marks or in square brackets with a colon [: ... :]
            </p>
            <pre class="dbp-code">[^POST type=post if=[: [%item.id]>30 :] length]</pre>
            <p>Count the number of articles with id> 30</p>

            <pre class="dbp-code">[^POST type=post if=[:[%item.author_name] == 'admin':] ]</pre>
            <p>Only extracts articles with author = 2</p>
        </div>

        <h3>sum</h3>
        <div class="dbp-help-p">
        Adds a vector.
        </div>

        <h3>mean</h3>
        <div class="dbp-help-p">
        It does the mathematical average
        </div>

        <h3>count - <span class="dbp-help-synonyms">(Synonyms: length)</span></h3>
        <div class="dbp-help-p">
            <p>If it is an array it counts the number of rows. If it is a string it counts the number of characters</p>
            <pre class="dbp-code">[%["bar","foo"] length]&lt;br&gt;
    [%"foo" length]</pre>
            <div class="dbp-result">2 // is an array of two elements
     3 // the number of characters in the string</div>
        </div>

        <h3>get=  - <span class="dbp-help-synonyms">(Synonyms: show, fields)</span></h3>
        <div class="dbp-help-p">
            <p>Returns only some certain fields of an array. If the array is associative it replaces the field name with the new key</p>
            <pre class="dbp-code">[^post id=3 fields={"post_title":"title"}]</pre>
            <div class="dbp-result">array (size=1)
    'post_title' => string '...' (length=14)</div>
            <pre class="dbp-code">[^post type=page fields=["id","author","title"]]</pre>
            <div class="dbp-result">    0 =>
        array (size=3)
        'ID' => int 1
            'author' => string '1' (length=1)
            'title' => string '...' (length=9)
            1 =>
            array (size=3)
            'ID' => int 2
            'author' => string '1' (length=1)
            'title' => string '...' (length=15)</div>
    
            <pre class="dbp-code">[^post type=page fields=id]</pre>
            <div class="dbp-result"> array (size=2)
                0 => int 1
                1 => int 2 </div>
            <pre class="dbp-code">[^post type=page fields={"id":"id", "author":"author"} tmpl=table]</pre>
            <p>Print a table whose titles are id and author.</p>
        </div>
    
    </div>
    </div>
</div>