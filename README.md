# Better Slugs Addon for Cockpit CMS

This addon enhances Cockpit CMS by providing a slug field type that can be used to generate automatically slugs for your collections. Tokens are supported in order to dynamically set values (e.g. dates, fields, etc..).

Idea was partially taken by the Unique Slugs addon https://github.com/raffaelj/cockpit_UniqueSlugs

## Installation

### Manual

Download [latest release](https://github.com/pauloamgomes/CockpitCMS-BetterSlugs) and extract to `COCKPIT_PATH/addons/BetterSlugs` directory

### Git

```sh
git clone https://github.com/pauloamgomes/CockpitCMS-BetterSlugs.git ./addons/BetterSlugs
```

### Cockpit CLI

```sh
php ./cp install/addon --name Honeypot --url https://github.com/pauloamgomes/CockpitCMS-BetterSlugs.git
```

### Composer

1. Make sure path to cockpit addons is defined in your projects' _composer.json_ file:

  ```json
  {
      "name": "MY_PROJECT",
      "extra": {
          "installer-paths": {
              "cockpit/addons/{$name}": ["type:cockpit-module"]
          }
      }
  }
  ```

2. In your project root run:

  ```sh
  composer require pauloamgomes/cockpitcms-betterslugs
  ```

---

## Usage

Add a new field of type slug to your collection and configure a format, e.g:

```json
{
  "format": "[collection:name]/[date:Y]/[field:title]"
}
```

For a collection named post it will result in something like `post/2019/your-post-title`, if you want to start with `/` like `/post/2019/your-post-title` use:

```json
{
  "format": "/[collection:name]/[date:Y]/[field:title]"
}
```

You can also use static values in the slug e.g. starting with `blogs` like `blogs/2019/01/your-post-title`:

```json
{
  "format": "blogs/[date:Y]/[date:m]/[field:title]"
}
```

If you are using localization and want to have your slug prefixed with the corresponding language id like `en/post/2019/your-post-title`

```json
{
  "format": "[lang:id]/blogs/[date:Y]/[date:m]/[field:title]"
}
```

And using a custom callback function:

```json
{
  "format": "blogs/[callback:slugUniqId]/[field:title]"
}
```

Assuming you have the slugUniqId function (e.g. in a boostrap.php addon file):

```php
function slugUniqId($entry, $app, $lang = FALSE) {
  return uniqid();
}
```

it will return in something like `blogs/5c2ccc816619b/your-post-title`

The callback function receives the $entry array as argument.

Currently the following tokens are supported:

- collection:value - where value is present in the collection structure (e.g. name)
- date:value - where value is any valid php date char (e.g. Y, m, d, YMD, etc..)
- field:value - where value is the field name (e.g. title)
- lang:id - replaces token with language id (e.g. en, fr, pt, etc..)
- linkedField:value|param - where value is the linked field collection name and param is the value from the linked collection, e.g.:
```json
"format": "countries/[linkedField:country|name]]/[field:title]"
```
If we have a field named country that is a collection link, it will retrieve the collection link entry values and extract the name field, so it would result in something like: `countries/netherlands/my-title`

- callback:value - where value is a custom callback function

By default the generated slugs are unique, so if you have a slug field configured with format:

```json
"format": "blogs/[date:Y]/[date:m]/[field:title]"
```

and your field title is "Blog Test" `blogs/2019/01/blog-test`, if you insert another entry with same title it will result on `blogs/2019/01/blog-test-1`, and next one on `blogs/2019/01/blog-test-2`.

The slug is only autogenerated when the field value is empty, so it can be overriden by the user with a non generated value.

Localization is supported, just enable in the field definition and the corresponding field language names (e.g. slug, slug_en, slug_pt) will be automatically populated.


A CLI command can be used to update all slugs in a collection:

```bash
$ ./cp refresh-slugs --name  blog_post

Collection 'blog_post' - Refreshing slugs...
Slug for 5c2cbece164bfc004d0b7595 updated to 'blogs/2019/01/test'
Slug for 5c2cbf82164bfc0044192afc updated to 'blogs/2019/01/test-1'
Slug for 5c2cbfaf164bfc00455447b4 updated to 'blogs/2019/01/test-2'
Slug for 5c2cc22a164bfc00455447b7 updated to 'blogs/2019/01/another-post'
Slug for 5c2ccc81164bfc00455447b9 updated to 'blogs/2019/01/testing-callback'
Done! 9 entries updated in 0.032s
```

## Copyright and license

Copyright 2019 pauloamgomes under the MIT license.
