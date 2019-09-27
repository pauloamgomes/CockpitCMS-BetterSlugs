<?php

use GraphQL\Type\Definition\Type;

$app->on('cockpitql.type.slug', function ($field, &$def) use ($app) {
  $def['type'] = Type::string();
});
