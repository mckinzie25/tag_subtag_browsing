Better Browsing With Tags and Subtags

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Installation
 * Requirements
 * Recommended modules
 * Installation
 * Configuration
 * Troubleshooting
 * FAQ
 * Maintainers


INTRODUCTION
------------

Current Maintainer:  Matthew McKinzie (matthew@webmechanic.org / drupal id:  mckinzie25)

This module creates a form that allows the user an easier way to browse content on sites that have many articles categorized by taxonomy terms and sub-terms.  With this form, the user selects a taxonomy term (e.g., "Drupal") from the first select field, and then in the second select field, sub-terms of that term are displayed (e.g., "Drupal 8" and "Drupal 9").  This form only works with two levels of terms.  This form allows the user a structured way of browsing content without having to sort through endless categories.


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
------------

To use this module, navigate to the Better Browsing With Tags and Subtags Configuration Form configuration page at /admin/config/browsing/tag_subtag_browsing and select the taxonomy that you are using to categorize content.  Since the module creates a block to contain the browsing form, you must then place the block in whatever region and page(s) you want it to appear.


REQUIREMENTS
------------

This module requires the core module Block to be enabled.