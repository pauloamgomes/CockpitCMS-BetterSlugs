<?php

/**
 * @file
 * Cockpit module bootstrap implementation.
 */

$this->helpers['betterslugs'] = 'BetterSlugs\\Helper\\Utils';

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

    if ($localize) {
      // Get enabled locales.
      $locales = array_keys($this->app->retrieve('languages', []));

      foreach ($locales as $locale) {
        if ($locale === 'default') {
          $locFieldName  = $fieldName;
          $lang = $this->app->config['i18n'];
        }
        else {
          $locFieldName = "{$fieldName}_{$locale}";
          $lang = $locale;
        }

        if (!array_key_exists($locFieldName, $entry)) {
          continue;
        }

        if (empty($entry[$locFieldName])) {
          $slug = $this->app->helper('betterslugs')->generate($format, $name, $entry, $lang);
        }
        else {
          $slug = $entry[$locFieldName] ?? $entry[$fieldName] ?? '';
        }
        $entry[$locFieldName] = $slug;
      }
    }
    else {
      $entry[$fieldName] = $slug;
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
