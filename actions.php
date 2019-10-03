<?php

/**
 * On each collection.save.before populate slug.
 */
$app->on('collections.save.before', function($name, &$entry, $isUpdate) use($app) {
    // Get the collection.
    $collection = $app->module('collections')->collection($name);
    $found = FALSE;

    foreach ($collection['fields'] as $field) {
      if ($field['type'] === 'slug' && isset($field['options']['format'])) {
        $found = TRUE;
        break;
      }
    }

    if ($found) {
      $entry = $app->module('betterslugs')->slugify($name, $entry, $field, $isUpdate);
    }
});
