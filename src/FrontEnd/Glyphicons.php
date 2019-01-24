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
     * 
     * @param array  $collection : symbols collection
     * @param string $options    : options containing 'cb_prefix' and 'colors' keys
     */
    public function __construct($collection, $options) {
        parent::__construct($collection, $options);
        
        foreach ($this->collection as $key => $select) {
            $this->collection[$key]['label'] = '<span class="sc-img glyphicon sc'.$select['key'].'"></span>';
        }
    }
    
    /**
     * Create CSS code
     * @return string CSS code
     */
    public function create_css_code()
    {
        $prefix = '';
        if ($this->debug === true) {
            $prefix = "\n\n";
        }
        
        $css = parent::create_css_code();
        foreach ($this->collection as $col) {
            $css .= $prefix.'.sc'.$col['key'].':before {content:"'.$col['id_str'].'";}';
        }
        
        return $css;
    }
    
}