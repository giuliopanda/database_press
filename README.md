# database_tables
Database Tables is a wordpress plugin designed to manage the administration and publication of new MySQL tables.

# Installation

The plugin is not yet published in the wordpress repository

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