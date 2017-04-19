<?php

require_once __DIR__ . '/init.php';

require_once __DIR__ . '/translation_file.class.php';


/**
 * Updater of the translations files.
 * Will create a locales directory as a sibling directory of the template dir.
 *  */
class TranslationsUpdater {

    public $language = "en_US";
    public $translationFile = null;
    public $translatableTexts = [];

    public function __construct($template_path, $language) {
        $this->template_path = $template_path;
        $this->language = $language;
        $this->translationFile = new TranslationFile($this->language, $this->template_path."/../locales");
        $this->translationFile->createDirectories();
    }
    
    /**
     * Add all the translatable texts to the source and translation file.
     */
    public function add_translatable_texts(){
        $di = new RecursiveDirectoryIterator($this->template_path);
        foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
            if(preg_match("/^(.*).html$/", $filename)){
                echo "$filename - {$file->getSize()} bytes \n";
                $this->add_translatable_texts_from_file($filename);
            }
        }
    }

    /**
     * Add string from file $file_path to the source and translation file.
     */
    private function add_translatable_texts_from_file($file_path){
        
        $file_contents = file_get_contents($file_path);
        
        $matches_translatable_block = [];
        preg_match_all("/{%\s*translatable\s*[\"']([^\"']+)[\"']\s*%\}\s*([^\{]+)\s*\{%\s*endtranslatable\s*%\}/ims", $file_contents, $matches_translatable_block);
               
        if(is_array($matches_translatable_block) and
                count($matches_translatable_block) >= 3 and
                count($matches_translatable_block[1]) > 0 and
                count($matches_translatable_block[2]) > 0){
            
            $num_translations = count($matches_translatable_block[2]);
            for($i=0; $i<$num_translations; $i++){
                
                $translation_id = $matches_translatable_block[1][$i];
                $translatable_text = trim($matches_translatable_block[2][$i]);
                
                $this->translatableTexts[$translation_id] = $translatable_text;
            }
        }
    }

    /**
     * Update translatable textes in the translations file. This operation
     * only adds new texts, it does not delete texts unless $purge option
     * is true.
     */
    public function update_translatable_texts($purge_orphan_translations=false){
        // Getting translatable texts
        $this->add_translatable_texts();
        
        // Get current translations
        $current_translations = $this->translationFile->load_translations();
       
        // Processing all found texts in the files. If they are not found
        // in the translations file, set them as empty strings.
        $source_texts = [];
        $template_translations = [];
        foreach($this->translatableTexts as $text_id => $text){
            if(!isset($current_translations[$text_id])){
                $current_translations[$text_id] = "";
            }
            $template_translations[$text_id] = $current_translations[$text_id];
            $source_texts[$text_id] = $text;
        }
       
        // if it is needed a purge of the orphan translations, ignore
        // texts that appear in the translation file but have no original
        // in the template files.
        if($purge_orphan_translations){
            $current_translations = $template_translations;
        }

        $this->translationFile->save_translations($current_translations, $source_texts);
    }

}


?>
