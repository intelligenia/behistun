<?php

require_once __DIR__ . '/init.php';

require_once __DIR__ . '/translation_file.class.php';
require_once __DIR__ . '/translatable.php';


/**
 * Translate the texts of the system.
 *  */
class Translator {

    public $language = "en_US";
    public $translationFile = null;
    public $translatableTexts = [];

    public function __construct($language) {
        $this->language = $language;
        $this->translationFile = new TranslationFile($this->language);
        Translatable_Translations::init($this->translationFile);
    }
}


?>
