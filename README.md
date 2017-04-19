# behistun
Behistun is a simple PHP translation manager

# Installation

```sh
composer install
```

# Configuration

## Define the available languages

```php
// For example we are going to translate to Spanish and French
$LANGUAGES = ["es_ES", "fr_FR"];
```

## Load the file init.php file from the behistun package

Load the init.php file in your index.php or init.php file.

```php
require_once __DIR__ . '/vendor/intelligenia/behistun/behistun/init.php';
```

# Use

## Templates

This package relies on the Twig template system so you will have to use the following Twig tag to mark a block of text as translatable:

```html
{% translatable '<id>' %}
This text will be translated
{% endtranslatable %}
```

The id will be used to identify the block of translatable text when dealing with translations. You should set it to value that helps you recognize easily the block it represents.

## Compile all source texts

Run this PHP script that will get all the source texts and create a **locales/LANGUAGE_CODE/LC_MESSAGES/** directory in the same directory your templates:

```sh
php vendor/intelligenia/behistun/behistun/bin/update_translations.php <template-path> <language> [purge]
```

For example:
```sh
php vendor/intelligenia/behistun/behistun/bin/update_translations.php ~/projects/my-web/public_html/templates/ en_US
```
## Translation
This **locales/LANGUAGE_CODE/LC_MESSAGES/** directory contains two files:

## web.source.php
Original association between translatable ids and source texts.

## web.translation.php
Where each translation goes. Remember that each translation is identified by the id you used in the translatable tag.

## Changing language

```php
// Call Translator class with the language code you want to translate the texts
// this code only should be executed once, when you have a selected language that is
// different from the default language (e.g. English if your web is for English-speaking people)
$translator = new Translator($LANGUAGE);
```
