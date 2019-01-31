<?php

namespace VoightKampff\FrontEnd;

/**
 * Captcha image frontend
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Images extends Abstr {
    
    /**
     * Constructor
     * @param string $options : options containing 'cb_prefix' and 'colors' keys
     */
    public function __construct($options) {
        parent::__construct($options);
        
        foreach ($this->collection as $key => $select) {
            $this->collection[$key]['label'] = '<img class="sc-img sc'.$select['key'].'" src="'.$select['key'].'.jpg"/>';
        }
    }
    
    /**
     * Create CSS code
     * @return string CSS code
     */
    public function createCss()
    {
        $prefix = '';
        if ($this->debug === true) {
            $prefix = "\n\n";
        }
        
        $css = parent::createCss();
        $css .= 'div.sc-form label .sc-img.selected { border: 2px solid '.$this->colors['selection'].'; }';
        
        foreach ($this->collection as $col) {
            $css .= $prefix.'.sc'.$col['key'].':after {content:" url('.$col['idStr'].')";}';
        }
        
        return $css;
    }
}
