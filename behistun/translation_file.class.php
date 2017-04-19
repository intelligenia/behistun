<?php

/**
 * Abstraction of a translation file.
 * This translation file contains all translation for a language for a website.
 *  */
class TranslationFile {

    public $siteName = "web";
    public $language = "en_US";
    public $translationFilesDirectory = "locales";
    
    public function __construct($language, $translationFilesDirectory="locales") {
        $this->siteName = "web";
        $this->language = $language;
        $this->translationFilesDirectory = $translationFilesDirectory;
    }
    
    /**
     * Creates all the directories needed for the locales of this web.
     *      */
    public function createDirectories(){
        if(!file_exists($this->translationFilesDirectory)){
            mkdir($this->translationFilesDirectory);
        }
        if(!file_exists("{$this->translationFilesDirectory}/{$this->language}")){
             mkdir("{$this->translationFilesDirectory}/{$this->language}");
        }
        if(!file_exists("{$this->translationFilesDirectory}/{$this->language}/LC_MESSAGES")){
             mkdir("{$this->translationFilesDirectory}/{$this->language}/LC_MESSAGES");
        }
        if(!file_exists("{$this->translationFilesDirectory}/{$this->language}/LC_MESSAGES/{$this->siteName}.translation.php")){
            file_put_contents("{$this->translationFilesDirectory}/{$this->language}/LC_MESSAGES/{$this->siteName}.translation.php", "<?php\r\n");
        }
        if(!file_exists("{$this->translationFilesDirectory}/{$this->language}/LC_MESSAGES/{$this->siteName}.source.php")){
             file_put_contents("{$this->translationFilesDirectory}/{$this->language}/LC_MESSAGES/{$this->siteName}.source.php", "<?php\r\n");
        }
    }


    /**
     * Sets the site name.
     */
    public function setSiteName($siteName){
        $this->siteName = $siteName;
    }


    /**
     * Output path of the translation file.
     * Translation file is a PHP file that contains a lone variable:
     * $TRANSLATIONS. This variable is an associative array with pairs key-value
     * where the key is the id of the string and the value is the translation.
     * 
     * @return string Path of the output path.
     */
    public function get_output_file(){
        //$this->createDirectories();
        return "{$this->translationFilesDirectory}/{$this->language}/LC_MESSAGES/{$this->siteName}.translation.php";
    }
    
    /**
     * Output path of the original strings.
     * 
     * @return string Original strings file path.
     */
    public function get_source_texts_file(){
        //$this->createDirectories();
        return "{$this->translationFilesDirectory}/{$this->language}/LC_MESSAGES/{$this->siteName}.source.php";
    }
    
    /**
     * Save the translations in the translations file and original string file.
     */
    public function save_translations($translations, $source_texts){
        // Saving translations
        file_put_contents($this->get_output_file(), "<?php\r\n".'$TRANSLATIONS = ' . var_export($translations, true) . ";\r\n");
        
        // Saving original texts
        file_put_contents($this->get_source_texts_file(), "<?php\r\n".'$SOURCE_TEXTS = ' . var_export($source_texts, true) . ";\r\n");
    }
    
    /**
     * Load translations in an array.
     * @return array Array with the translations extracted from the
     * translation file.
     *      */
    public function load_translations(){
       $output_file = $this->get_output_file();
       if(!file_exists($output_file)){
           file_put_contents($output_file, "<?php\r\n".'$TRANSLATIONS = [];'. "\r\n");
       }
       require_once $output_file;
       if(isset($TRANSLATIONS)){
           return $TRANSLATIONS;
       }
       return [];
    }
}


?>
