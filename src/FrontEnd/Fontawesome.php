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
     * Constructor
     * 
     * @param array  $collection : symbols collection
     * @param string $options    : options containing 'cb_prefix' and 'colors' keys
     */
    public function __construct($collection, $options) {
        parent::__construct($collection, $options);
        
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
            
            $this->collection[$key]['label'] = '<i class="sc-img '.$style_class.' sc'.$select['key'].'"></i>';
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
