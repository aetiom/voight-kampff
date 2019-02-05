<?php

namespace VoightKampff;

/**
 * VoightKampff directive
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Directive {
    
    /**
     * @var \VoightKampff\Opions $options : config options
     */
    protected $options;
    
    /**
     * @var \VoightKampff\Collection $collection : captcha collection
     */
    protected $collection;
    
    /**
     * @var \aetiom\PhpExt\MultiLang\Container $start : directive starters
     */    
    protected $start;
    
    /**
     * @var \aetiom\PhpExt\MultiLang\Container $link1 : directive first linkers
     */    
    protected $link1;
    
    /**
     * @var \aetiom\PhpExt\MultiLang\Container $link2 : directive second linkers
     */    
    protected $link2;
    
    /**
     * @var \aetiom\PhpExt\MultiLang\Container $end : directive finisher
     */    
    protected $end;
    
    /**
     * @var \aetiom\PhpExt\MultiLang\Container $kwIn : directive keyword in tag
     */
    protected $kwIn;
    
    /**
     * @var \aetiom\PhpExt\MultiLang\Container $kwOut : directive keyword out tag
     */
    protected $kwOut;
    
    
    
    /**
     * Get directive message
     * 
     * @param string $lang : directive language
     * @return string directive message
     */
    public function getMessage($lang = null)
    {
        if (empty($this->collection->getKeyWords())) {
            return '';
        }
        
        if ($lang === null) {
            $lang = $this->options->defaultLang;
        }
        
        $kw = [];
        foreach ($this->collection->getKeyWords() as $words) {
            if (!isset($words[$lang]) || empty($words[$lang])) {
                $kw[] = $words[$this->options->defaultLang];
                continue;
            }
            
            $kw[] = $words[$lang];
        }
        
        return $this->format($kw, $lang);
    }
    
    
    
    /**
     * Constructor
     * 
     * @param \VoightKampff\Options    $options    : captcha options
     * @param \VoightKampff\Collection $collection : captcha collection
     */
    public function __construct(Options $options, Collection $collection) 
    {
        $this->options = $options;
        $this->collection = $collection;
        
        $dirCol = new \aetiom\PhpExt\MultiLang\Collection(
                $this->options->directiveCollection, 
                $this->options->defaultLang);
        
        $this->start = $dirCol->createContainer('start');
        $this->link1 = $dirCol->createContainer('linkSimple');
        $this->link2 = $dirCol->createContainer('linkMulti');
        $this->end   = $dirCol->createContainer('end');
        $this->kwIn  = $dirCol->createContainer('keywordIn');
        $this->kwOut = $dirCol->createContainer('keywordOut');
    }
    
    
    
    /**
     * Format message
     * 
     * @param array  $keyWords : captcha keywords
     * @param string $lang     : language
     * 
     * @return string formated message
     */
    private function format($keyWords, $lang)
    {
        $dirStr = '';
        foreach ($keyWords as $key => $word) {
            if (!empty($dirStr)) {
                $dirStr .= $this->selectLink($key, $lang);
            }

            $dirStr .= $this->kwIn->getMessage($lang).$word
                    .$this->kwOut->getMessage($lang);
        }

        return $this->start->getMessage($lang).$dirStr.
                $this->end->getMessage($lang);
    }
    
    /**
     * Select linker
     * 
     * @param integer $count : position of current word in keyword list
     * @param string  $lang  : language
     * 
     * @return string linker chain
     */
    private function selectLink($count, $lang)
    {
        if ($this->options->requestCount > 2 
                && $count < $this->options->requestCount - 1) {
            return $this->link2->getMessage($lang);
        } else {
            return $this->link1->getMessage($lang);
        }
    }
}
