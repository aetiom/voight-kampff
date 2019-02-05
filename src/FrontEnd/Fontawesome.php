<?php

namespace VoightKampff\FrontEnd;

/**
 * Captcha fontawesome frontend
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Fontawesome extends Abstr {
    
    /**
     * @var array $styles : fontawesome 5 style (solid, regular, light) 
     */
    private $styles = array(
        'solid'   => 'fas',
        'regular' => 'far',
        'light'   => 'fal'
    );
    
    
    
    /**
     * Set symbols collection
     * @param array $collection : symbols collection
     */
    public function setCollection($collection)
    {
        parent::setCollection($collection);
        
        $fa5Opts = $this->options->frontend['options'];
        
        if (!isset($fa5Opts['fa5Style']) || empty($fa5Opts['fa5Style'])) {
            $fa5Opts['fa5Style'] = 'fas';
            
        } elseif (array_key_exists($fa5Opts['fa5Style'], $this->styles) !== false) {
            $fa5Opts['fa5Style'] = $this->styles[$fa5Opts['fa5Style']];
        }
        
        foreach ($this->collection as $key => $select) {
            $styleClass = $fa5Opts['fa5Style'];
            
            if (isset($select['styleClass']) && !empty($select['styleClass'])) {
                $styleClass = $select['styleClass'];
            }
            
            $this->collection[$key]['label'] = '<i class="vk-img '.$styleClass.' vk'.$select['key'].'"></i>';
        }
    }
    
    /**
     * Create CSS code
     * @return string CSS code
     */
    public function createCss()
    {
        $prefix = '';
        if ($this->options->debug === true) {
            $prefix = "\n\n";
        }
        
        $css = parent::createCss();
        foreach ($this->collection as $col) {
            $css .= $prefix.'.vk'.$col['key'].':before {content:"'.$col['idStr'].'";}';
        }
        
        return $css;
    }
    
}
