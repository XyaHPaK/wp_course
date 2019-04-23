<?php 
get_header();

foreach (get_field('layout') as $section) {
    require __DIR__ . '/inc/sections/' . $section['stype'] . '.php';
}
get_footer();