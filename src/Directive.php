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
     * @var Asset $start : directive starters asset
     */    
    protected $start;
    
    /**
     * @var Asset $link1 : directive first linkers asset
     */    
    protected $link1;
    
    /**
     * @var Asset $link2 : directive second linkers asset
     */    
    protected $link2;
    
    /**
     * @var Asset $end : directive finisher asset
     */    
    protected $end;
    
    /**
     * @var Asset $kwIn : directive keyword in tag asset
     */
    protected $kwIn;
    
    /**
     * @var Asset $kwOut : directive keyword out tag asset
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
        
        $this->start = new Asset('start', $this->options->directives);
        $this->link1 = new Asset('linkSimple', $this->options->directives);
        $this->link2 = new Asset('linkMulti', $this->options->directives);
        $this->end   = new Asset('end', $this->options->directives);
        $this->kwIn  = new Asset('keywordIn', $this->options->directives);
        $this->kwOut = new Asset('keywordOut', $this->options->directives);
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
        /*
        var_dump($this->start);
        var_dump($this->link1);
        var_dump($this->link2);
        var_dump($this->end);
        var_dump($this->kwIn);
        var_dump($this->kwOut);
        exit;
        */
        
        $dirStr = '';
        foreach ($keyWords as $key => $word) {
            if (!empty($dirStr)) {
                $dirStr .= $this->selectLink($key, $lang);
            }

            $dirStr .= $this->kwIn->fetch($lang).$word
                    .$this->kwOut->fetch($lang);
        }

        return $this->start->fetch($lang).$dirStr.
                $this->end->fetch($lang);
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
            return $this->link2->fetch($lang);
        } else {
            return $this->link1->fetch($lang);
        }
    }
}
