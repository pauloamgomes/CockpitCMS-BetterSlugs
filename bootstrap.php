<?php

/**
 * @file
 * Cockpit module bootstrap implementation.
 */

require __DIR__ . '/vendor/autoload.php';

$this->helpers['betterslugs'] = 'Cockpit\\BetterSlugs\\Utils';

$this->module('betterslugs')->extend([
  'slugify' => function($name, $entry, $field, $isUpdate) {
    $localize = $field['localize'] ?? FALSE;
    $format = $field['options']['format'];
    $fieldName = $field['name'];

    // Only automate slug generation if field is empty.
    if (empty($entry[$fieldName])) {
      $slug = $this->app->helper('betterslugs')->generate($format, $name, $entry);
    }
    else {
      $slug = $entry[$fieldName];
    }

    $slug = $this->app->helper('betterslugs')->getUnique($name, $slug, $fieldName, $entry['_id'] ?? NULL);

    // Update slug field with resulting slug value.
    $entry[$fieldName] = $slug;

    if ($localize) {
      // Get enabled locales.
      $locales = array_keys($this->app->retrieve('languages', []));
      foreach ($locales as $locale) {
        $locFieldName = "{$fieldName}_{$locale}";
        if (!array_key_exists($locFieldName, $entry)) {
          continue;
        }
        if (empty($entry[$locFieldName])) {
          $slug = $this->app->helper('betterslugs')->generate($format, $name, $entry, $locale);
        }
        else {
          $slug = $entry[$locFieldName] ?? $entry[$fieldName] ?? '';
        }
        $entry[$locFieldName] = $slug;
      }
    }

    return $entry;
  },
]);

// CLI includes.
if (COCKPIT_CLI) {
  $this->path('#cli', __DIR__ . '/cli');
  include_once __DIR__ . '/actions.php';
}

// Admin includes.
if (COCKPIT_ADMIN && !COCKPIT_API_REQUEST) {
  include_once __DIR__ . '/admin.php';
  include_once __DIR__ . '/actions.php';
}

// API includes.
if (COCKPIT_API_REQUEST) {
  include_once __DIR__ . '/actions.php';
  include_once __DIR__ . '/cockpitql.php';
}
