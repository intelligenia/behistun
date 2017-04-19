<?php

require_once __DIR__ . "/../translations_updater.class.php";


$AVAILABLE_LANGUAGES = ["en_US"=>true];


// Command line arguments
if(!isset($argv) or !isset($argv[1]) or !isset($argv[2]) or !isset($argv[2]) or !isset($AVAILABLE_LANGUAGES[$argv[2]])){
    $message = "Use: update_translations <path> <language> [purge]\n";
    $message .= "- <path>: path where the template files are\n";
    $message .= "- <language>: language: en_US, fr_FR, etc.\n";
    $message .= "- [purge]: if present, all orphan translations will be deleted. (*)\n";
    $message .= "- (*) Orphan translations are string not found in templates but that are present in translation files.\n";
    die($message);
}

$path = $argv[1];
$language = $argv[2];
$purge = isset($argv[3]) and $argv[3] == "purge";

print "Processing language {$language}\n";
$translator = new TranslationsUpdater($path, $language);

$translator->update_translatable_texts($purge);

?>
