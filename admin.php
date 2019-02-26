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
