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
     * @var array $fa5_styles : fontawesome 5 style (solid, regular, light) 
     */
    private $fa5_styles = array(
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
        
        $fa5_options = $this->options['options'];
        
        if (!isset($fa5_options['fa5_style']) || empty($fa5_options['fa5_style'])) {
            $fa5_options['fa5_style'] = 'fas';
            
        } elseif (array_key_exists($fa5_options['fa5_style'], $this->fa5_styles) !== false) {
            $fa5_options['fa5_style'] = $this->fa5_styles[$fa5_options['fa5_style']];
        }
        
        foreach ($this->collection as $key => $select) {
            $style_class = $fa5_options['fa5_style'];
            
            if (isset($select['style_class']) && !empty($select['style_class'])) {
                $style_class = $select['style_class'];
            }
            
            $this->collection[$key]['label'] = '<i class="vk-img '.$style_class.' vk'.$select['key'].'"></i>';
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
