<?php

namespace VoightKampff\FrontEnd;

/**
 * Captcha frontend abstract class
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
abstract class Abstr {
    
    /**
     * @var Options $options : captcha options
     */
    protected $options;
    
    /**
     * @var array $collection : symbols collection
     */
    protected $collection;
    
    
    
    /**
     * Set symbols collection
     * @param array $collection : symbols collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }
    
    
    
    /**
     * Constructor
     * @param string $options    : options containing 'cbPrefix' and 'colors' keys
     */
    public function __construct($options) 
    {    
        $this->options = $options;
        $this->collection = array();
    }
    
    
    
    /**
     * Create CSS code
     * @return string CSS code
     */
    public function createCss()
    {
        if ($this->options->debug === true) {
            $css = file_get_contents(__DIR__.'/Assets/captcha.css');
        } else {
            $css = file_get_contents(__DIR__.'/Assets/captcha.min.css');
        }
        
        $colors = $this->options->frontend['colors'];
        
        $css = str_replace('%BG_COLOR%', $colors['background'], $css);
        $css = str_replace('%ERR_COLOR%', $colors['error'], $css);
        $css = str_replace('%SELECT_COLOR%', $colors['selection'], $css);
        
        return $css;
    }
    
    
    
    /**
     * Create JS code
     * @return string JS code
     */
    public function createJs()
    {
        if ($this->options->debug === true) {
            return file_get_contents(__DIR__.'/Assets/captcha.js');
        }
        
        return file_get_contents(__DIR__.'/Assets/captcha.min.js');
    }
    
    
    
    /**
     * Create HTML code
     * 
     * @param array $content : containing 'directive' and/or 'error' messages
     * @return string HTML code
     */
    public function createHtml($content = array(), $cssEnable = true, $jsEnable = true)
    {
        $html_code = '';
        
        if ($cssEnable === true) {
            $html_code .= '<style type="text/css">'.$this->createCss().'</style>';
        }
        
        if (isset($content['error']) && !empty($content['error'])) {
            $html_code .='<div class="vk-form has-error"><p class="vk-error">'.nl2br($content['error']).'</p>';
        } else {
            $html_code .= '<div class="vk-form">';
        }
        
        if (isset($content['directive']) && !empty($content['directive'])) {
            $html_code .= '<p class="vk-directive">'.nl2br($content['directive']).'</p>';
        }
        
        $html_code .= $this->createFormInputs().'</div>';
        
        if ($jsEnable === true) {
            $html_code .= '<script type="text/javascript">'.$this->createJs().'</script>';
        }
        
        return $html_code;
    }
    
    
    
    /**
     * Create form inputs for HTML
     * @return string form inputs HTML code
     */
    protected function createFormInputs() 
    {
        $inputs = '';
        
        $count = 0;
        foreach ($this->collection as $select) {
            
            // initiate select content with default value if its not initiated yet
            if (!isset($select['label']) || empty($select['label'])) {
                $select['label'] = '<span class="vk-img vk'.$select['key'].'"></span>';
            }
            
            $inputs .= '<input class="" type="checkbox" id="'.$this->options->cbPrefix.$count
                    .'" name="'.$this->options->cbPrefix.$count.'" value="'.$select['key'].'">'
                    .'<label for="'.$this->options->cbPrefix.$count.'">'.$select['label'].'</label>';
            $count++;
        }
        
        return $inputs;
    }
}