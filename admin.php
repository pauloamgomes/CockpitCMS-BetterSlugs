<?php

/**
 * @file
 * Cockpit BetterSlugs admin functions.
 */

/**
 * Initialize addon for admin pages.
 */
$app->on('admin.init', function () {
  // Add field slug.
  $this->helper('admin')->addAssets('betterslugs:assets/field-slug.tag');
});

/**
 * On each collection.save.before populate slug.
 */
$app->on('collections.save.before', function($name, &$entry, $isUpdate) use($app) {
  $entry = $app->module('betterslugs')->slugify($name, $entry, $isUpdate);
});
