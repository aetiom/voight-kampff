<?php

namespace VoightKampff\FrontEnd;

/**
 * Captcha glyphicons frontend
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Glyphicons  extends Abstr {
    
    /**
     * Constructor
     * @param string $options    : options containing 'cb_prefix' and 'colors' keys
     */
    public function __construct($options) {
        parent::__construct($options);
        
        foreach ($this->collection as $key => $select) {
            $this->collection[$key]['label'] = '<span class="sc-img glyphicon sc'.$select['key'].'"></span>';
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
        foreach ($this->collection as $col) {
            $css .= $prefix.'.sc'.$col['key'].':before {content:"'.$col['idStr'].'";}';
        }
        
        return $css;
    }
    
}