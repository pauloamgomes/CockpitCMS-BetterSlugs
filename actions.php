<?php

/**
 * On each collection.save.before populate slug.
 */
$app->on('collections.save.before', function($name, &$entry, $isUpdate) use($app) {
  $entry = $app->module('betterslugs')->slugify($name, $entry, $isUpdate);
});
