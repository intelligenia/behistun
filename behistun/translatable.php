<?php

require_once __DIR__ . '/init.php';


/**
 * Translatable translations Store.
 *  */
class Translatable_Translations {
    
    private static $TRANSLATIONS = [];
    
    public static function init($translations_file){
        static::$TRANSLATIONS = $translations_file->load_translations();
    }
    
    public static function getTranslations(){
        return static::$TRANSLATIONS;
    }
}


/**
 * {% translatable %} {% endtranslatable %} Twig Tag.
 * Read http://stackoverflow.com/questions/26170727/how-to-create-a-twig-custom-tag-that-executes-a-callback
 * for more information
 *  */
class Translatable_TokenParser extends Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        if (!$stream->test(Twig_Token::BLOCK_END_TYPE)){   
           $values = $this->parser->getExpressionParser()
                          ->parseMultitargetExpression();
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideTranslatableEnd'), true);

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new Translatable_Node($body, $values, $token->getLine(), $this->getTag());
    }

    public function decideTranslatableEnd(Twig_Token $token)
    {
        return $token->test('endtranslatable');
    }

    public function getTag()
    {
        return 'translatable';
    }
    
    /**
    * Recovers all tag parameters until we find a BLOCK_END_TYPE ( %} )
    *
    * @param \Twig_Token $token
    * @return array
    */
   protected function getInlineParams($token)
   {
      $stream = $this->parser->getStream();
      $params = array ();
      while (!$stream->test(\Twig_Token::BLOCK_END_TYPE))
      {
         $params[] = $this->parser->getExpressionParser()->parseExpression();
      }
      $stream->expect(\Twig_Token::BLOCK_END_TYPE);
      return $params;
   }

}


/**
 * {% translatable %} {% endtranslatable %} Tag parser node.
 *  */
class Translatable_Node extends Twig_Node
{
    public function __construct($body, $values,
                                $line, $tag = null)
    {
        if ($values){
           parent::__construct(array('body' => $body, 'values' => $values),
                               array(), $line, $tag);
        }else{
           parent::__construct(array('body' => $body), array(), $line, $tag);        
        }
    }

    public function compile(Twig_Compiler $compiler)
    {
        $blockContent = addslashes(trim($this->getNode('body')->attributes["data"]));
        try{
            $textId = $this->getNode('values')->nodes[0]->attributes["value"];
        } catch (Exception $e){
            $textId = $blockContent;
        }
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->write("\$sourceTextId = \"$textId\";\n")
            ->write("\$blockContent = \"$blockContent\";\n")
            ->subcompile($this->getNode('body'))            
            ->write("Translatable_Node::translate(\$sourceTextId, \$blockContent, ob_get_clean()");

        $compiler->raw(");\n"); 
    }
    
    public static function translate($sourceTextId, $blockContent){
        $translations = Translatable_Translations::getTranslations();
        if(isset($translations[$sourceTextId]) and $translations[$sourceTextId]!==""){
            print $translations[$sourceTextId];
        }else{
            print $blockContent;
        }    
    }
}

?>
