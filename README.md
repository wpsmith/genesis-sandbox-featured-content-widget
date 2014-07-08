# Genesis Sandbox Featured Content Widget #
**Contributors:** wpsmith 
  
**Donate link:** http://wpsmith.net

**Tags:** genesis, genesiswp, studiopress, featured post, cu
  
**Requires at least:** 3.6
  
**Tested up to:** 3.6
  
**Stable tag:** trunk
  

Genesis Featured Content with support for custom post types, taxonomies, and so much more.

## Description ##

Genesis Sandbox Featured Content Widget adds additional functionality to the Genesis Featured Posts Widget.  Specifically it:

*   Supports Custom Post Types
*   Supports Custom Taxonomies
*   Exclude Term by ID field
*   Supports Pagination
*   Supports Meta Key Values
*   Supports Sorting by Meta Key
*   Multiple Hooks and Filters for adding additional content

New features include:

*   More filters/actions
*   Everything in a single class
*   Addition of column classes (including option for fifths class) & custom classes
*   WP_Query optimization for speed including site transients and term cache updating
*   Pushed everything to a framework approach so it can all be removed easily
*   HTML5 & Genesis 2.0 updated
*   Excerpt length & cutoffs options added
*   White labeled naming option
*   Added remove displayed posts option (won't display a post that already exists on the page)

## Installation ##

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

1.  Upload the entire \`genesis-sandbox-featured-content-widget\` folder to the \`/wp-content/plugins/\` directory
2.  Activate the plugin through the \'Plugins\' menu in WordPress
3.  Go Widget Screen
4.  Drag Widget to desired sidebar
5.  Fill in widget settings

## Frequently Asked Questions ##

###Why a new version of GFWA?###
Well, I bother Nick all the time to change things. He's busy. I'm busy. So, instead of hassling with the plugin, convincing Nick or whomever, I decided it was time just to create my own.

###What Hooks are available?###

1. gsfc_widget_name - replace 'Genesis Sandbox' by returning a string with a new prefix name
1. gsfc_before_loop - before the query is formulated
1. gsfc_before_post_content - before the content
1. gsfc_post_content - standard content output
1. gsfc_after_post_content - after content
1. gsfc_endwhile - after the endwhile but before the endif
1. gsfc_after_loop - after the loop endif
1. gsfc_after_loop_reset - after the loop reset
1. gsfc_before_list_items - before additional list item loop
1. gsfc_list_items - within additional list item loop
1. gsfc_after_list_items - after the additional list item loop, where list items are output
1. gsfc_category_more - within the archive more link conditional block
1. gsfc_taxonomy_more (alias of gsfc_category_more) - within the archive more link conditional block
1. gsfc_[TAXONOMY]_more (alias of gsfc_category_more) - within the archive more link conditional block
1. gsfc_after_category_more - after the archive more conditional block
1. gsfc_after_taxonomy_more (alias of gsfc_after_category_more) - after the archive more conditional block
1. gsfc_after_[TAXONOMY]_more (alias of gsfc_after_category_more) - after the archive more conditional block
1. gsfc_show_content - create your own custom content block

### What Filters are available? ###

1. gsfc_query_args - filter the main query args
1. gsfc_exclude_post_types - used to prevent post types from appearing in the post type list in the widget form
1. gsfc_exclude_taxonomies - used to prevent taxonomies and related terms from appearing in the terms and taxonomies list in the widget form
1. gsfc_extra_post_args - extra post args for the extra posts list
1. gsfc_list_items - HTML markup for the list items
1. gsfc_form_fields - Add custom fields to widget form
