<?php
/** Styles and scripts enqueues */
include_once 'inc/enqueue.php';
/*post types registration*/
include_once  'inc/post_types_reg.php';
/*meta boxes*/
include_once  'inc/meta_box_reg.php';
/** Customizer */
include_once 'inc/customizer.php';
/** Custom Modules for Divi Page Builder */
include_once 'divi_modules/custom-divi-module.php';
/*Api connection*/
include_once 'inc/api_connect.php';
/** API options */
include_once 'inc/api_options.php';
include_once 'inc/instagram_api_options.php';
include_once 'inc/GoogleApiOptions.php';
/*Slug exist checking*/
include_once 'inc/db_slug_checking.php';
/*Cron tasks*/
include_once 'inc/cron.php';
/*Api testing template*/
include_once 'inc/test.php';
/*custom shortcodes inclusion*/
include_once 'custom_shortcodes/custom_shortcodes.php';
