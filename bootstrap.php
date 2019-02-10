<?php

/**
 * @file
 * Cockpit module bootstrap implementation.
 */

$this->module('betterslugs')->extend([
  'slugify' => function($name, $entry, $isUpdate) {
    // Get the collection.
    $collection = $this->app->module('collections')->collection($name);

    // Iterate over collection fields and confirm we have a slug type.
    foreach ($collection['fields'] as $field) {
      if ($field['type'] !== 'slug') {
        continue;
      }
      if (empty($field['options']['format'])) {
        continue;
      }

      $fieldName = $field['name'];
      $parts = explode("/", $field['options']['format']);

      // We only automate slug generation if field is empty.
      if (empty($entry[$fieldName])) {
        $newParts = [];
        foreach ($parts as $part) {
          if ((bool) preg_match('/\[([a-zA-Z]+):([a-zA-Z-0-9_\|]+)\]/', $part)) {
            $part = str_replace(['[', ']'], '', $part);
            list($tokenKey, $tokenValue) = explode(":", $part);

            switch ($tokenKey) {
              case 'date':
                $part = date($tokenValue);
                break;

              case 'field':
                if (isset($entry[$tokenValue])) {
                  $part = $entry[$tokenValue];
                }
                break;

              case 'linkedField':
                list($colName, $colField) = explode("|", $tokenValue);
                if (!isset($colName, $colField)) {
                  break;
                }

                if (!empty($entry[$colName]['link'])) {
                  $link = $entry[$colName]['link'];
                  $id = $entry[$colName]['_id'];
                  $linkedEntry = $this->app->module('collections')->findOne($link, ["_id" => $id]);
                  if ($linkedEntry && isset($linkedEntry[$colField]) && !is_array($linkedEntry[$colField])) {
                    $part = $linkedEntry[$colField];
                  }
                }
                break;

              case 'collection':
                $part = $collection[$tokenValue] ?? $collection['name'];
                break;

              case 'callback':
                if (function_exists($tokenValue)) {
                  $part = call_user_func($tokenValue, $entry);
                }
                break;
            }
          }
          $newParts[] = $this->app->helper('utils')->sluggify($part);
        }
        $slug = trim(implode('/', $newParts));
      }
      else {
        $slug = $entry[$fieldName];
      }

      // Check if slug is unique.
      $criteria[$fieldName] = $slug;
      // If is an update exclude current entry.
      if ($isUpdate) {
        $criteria['_id'] = ['$ne' => (string) new \MongoDB\BSON\ObjectID($entry["_id"])];
      }
      $count = $this->app->module('collections')->count($name, $criteria);
      if ($count > 0) {
        $_slug = $slug;
        $slug = "{$slug}-{$count}";
        // Second check as we have now the numeric prefix value.
        $criteria[$fieldName] = new MongoDB\BSON\Regex("^{$_slug}-[0-9]+$");
        $count = $this->app->module('collections')->count($name, $criteria);
        if ($count > 0) {
          $count++;
          $slug = "{$_slug}-{$count}";
        }
      }

      // Update slug field with resulting slug value.
      $entry[$fieldName] = $slug;
    }

    return $entry;
  },
]);

// CLI includes.
if (COCKPIT_CLI) {
  $this->path('#cli', __DIR__ . '/cli');
  include_once __DIR__ . '/admin.php';
}

// Include admin.
if (COCKPIT_ADMIN && !COCKPIT_API_REQUEST) {
  include_once __DIR__ . '/admin.php';
}
