=== database tables ===
Contributors: giuliopanda 
Donate link: https://www.paypal.com/donate/?cmd=_donations&business=giuliopanda%40gmail.com&item_name=wordpress+database+tables
Tags: database tables
Requires at least: 5.9
Tested up to: 6.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 0.7.0

Manage the administration and publication of new MySQL tables.

== Description ==

Use the database tables plug-in to create new mysql tables, manage their contents and publish them on your site.

== Installation ==

The plugin is not yet published in the wordpress repository

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