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
     * Set symbols collection
     * @param array $collection : symbols collection
     */
    public function setCollection($collection)
    {
        parent::setCollection($collection);
        
        foreach ($this->collection as $key => $select) {
            $this->collection[$key]['label'] = '<span class="vk-img glyphicon vk'.$select['key'].'"></span>';
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
            $css .= $prefix.'.vk'.$col['key'].':before {content:"'.$col['idStr'].'";}';
        }
        
        return $css;
    }
    
}