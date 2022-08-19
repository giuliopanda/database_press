# database_tables 
database press is designed to manage the administration and publication of new MySQL (or Maria Db) tables through an integrated template engine.

-------------

This plugin is an open source project and was developed to help people who build websites manage and view database data on the website.

### How does it work
Write a query and save it. At this point you will be able to manage the view and the editing form in the backend (deciding which roles can modify the content).
On your site show the result through a simple shortcode.

### Database management:
- Browse and edit table data
- utility for writing queries
- Advanced search filters
- Create new tables
- A simple filter management system
- Import export data in sql and csv.
- search and replace with serialized data support

### You can save a query by creating a new list. Through the lists you can have other features:
- Select the type of field when editing data (select, checkbox, lookup, post, image, checkbox etc ...)
- You have the ability to modify the data of multiple tables linked by left joins.
- In administration you can view a list in a separate menu item and choose who can manage that table.
- Display the data in the frontend through a shortcode.

### You then have an integrated template engine at your disposal.
- You have a shortcode in which you can write your code.
- You can choose how to display the data extracted from a query
- You have all the typical instructions of programming languages ​​such as loops, conditions, variable management and so on.
- You can access data related to wordpress, such as the post you are viewing or upload images or the current user.


### Differences with other systems

- The modification of posts and users is entrusted to the native functions of wordpress thus preventing any errors in the data structure.

Compared to phpmyadmin or adminer database press:
- allows easier management of filters.
- Protects the structure of native wordpress tables.
- Allows partial management of serialized data.

- If you want to show a table in the frontend most other systems require the loading of datatables and jquery to work, database press for the frontend part uses pure javascript.


# Installation

The plugin is not yet published in the wordpress repository

# Versions

= 0.9.1 - 2022-08-19 =
- Fixbug: Warning in import sql
- Fixbug: slow codemirror when copying and pasting long texts.
- Fixbug: Warning in class-dbt-list-admin.php on line 670
- improvement: On the 'organize columns' added the title to read the field if it is too long
- Fixbug: sorting form with new columns
- Fixbug list view formatting does not allow to change the ID
- Improvement: on merge query removed join type and the explanations have been simplified

= 0.9.0 - 2022-08-17 =
- Note: the template engine logic has changed so I cannot guarantee compatibility with previous versions.
Previously the template engine would delete a shortcode if it couldn't find it. This created a problem with regular expression like \[^/\] that the template engine recognized as shortcode and deleted them. Now if it doesn't find the shortcode it prints it as is. The verification test is in pina-test.php.
- Improvement: The css of the column sizes have been changed. This can lead to incompatibility with version v0.8. To correct the problem just go to the list and save list view formatting again.
- Feature: Search & replace. Added search on all fields in lists and the ability to search & replace in queries (not in lists!).
- Improvement: improved search filters.
- Improvement: Added option to align fields in frontend tables.
- Fixbug: clean warning & notice
- Improvement: improved the
sidebar navigation and added collapse option
- Improvement: In 'List view formatting' added the 'Choose column to show' button. This button gives the possibility to change the query select and add or remove columns.
- Fixbug: Tips for tables that have no primary key.
- Fixbug: Restored the primary key icon in the query results view.
- Improvement: Added an alert after you modify queries with Organize columns, Merge
- Improvement: On list browse, if the content editing window is open, it does not allow you to open the column search filters!
- Removed: Deleted the checkbox to display the primary key in the Add meta data
- Fixbug: Instead of showing the query of an update it shows the select of a '_transient'
- Note: The default editor is disabled in the form if the user has selected in his profile: Disable the visual editor when writing
- Rebuild: the delete system from sql.
- Improvement: Removed the choice of query type when creating a list from query
- Fixbug:frontend view Show if no longer worked.

= 0.8.1 - 2022-07-29 =
- Fixbug: The codeMirror did not appear on all wordpress configurations.

= 0.8.0 - 2022-07-29 =
- Improvement: Changed the page titles of the "list view formatting" and added the column types User, Post and Link popup detail.
- Improvement: Removed ids added secretly in browse table queries
- Fixbug: In the table Structure page when you changed the column to primary key, you could select multiple primary keys and sometimes the select disappeared.
- Fixbug: Form: I create a form with a required field. I open the modification of the contents and save leaving the mandatory field not filled in. The form disappears, but does not reappear with the error message.
- Improvement: Management of the decimal field.
- Fixbug: Test import creation of temporary table with correct ids
- Fixbug: List view formatting in showing titles and field types.


= 0.7.0 - 2022-07-24 =
- Improved help and translation
- Created a new class dbt_render_list in place of html-table-frontend. This made it possible to manage pagination and search in lists with more flexibility.
- On frontend> list type editor it is now possible to add shortcode in the template engine
[% html.pagination], [% html.search].
- Added dbt_frontend_get_list filter: It allows you to redesign the display of a list in php.
- fixbug: no longer save data due to _default_alias_table in default value
- figbug: Not all primary key columns appeared in the frontend tables
- Improved the management of multiple tables in the frontend through the addition of the prefix parameter. Now you can use filters and pagination on multiple tables within the same page.
- Added in the template engine: admin_url and counter.