<?php

namespace VoightKampff;

/**
 * VoightKampff display manager
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Display {
    
    /**
     * @var DEFAULT_CLASS : default frontend class
     */
    const DEFAULT_CLASS = 'Fontawesome';
    
    /**
     * @var VoightKampff\Frontend\ $frontend : frontend instance
     */
    protected $frontend;
    
    /**
     * @var array $options : display options
     */
    protected $options;
    
    /**
     * @var array $defaultOpts : default options
     */
    private $defaultOpts = array(
        'cssEnable' => true,
        'jsEnable'  => true,
        'cbPrefix'  => 'sc-cb'
    );
    
    
    
    /**
     * Get frontend instance
     * @return type
     */
    public function getFrontendInstance()
    {
        return $this->frontend;
    }
    
    
    
    /**
     * Constructor
     * 
     * @param array $symbols : images collection
     * @param array $options : display and frontend options
     */
    public function __construct(array $symbols = array(), array $options = array())
    {
        $feClass = self::DEFAULT_CLASS;
        if (isset($options['frontend']['class']) && !empty($options['frontend']['class'])) {
            $feClass = $options['frontend']['class'];
        }
        
        if (strstr('\\', $feClass) === false) {
            $feClass = 'FrontEnd\\'.$feClass;
        }
        
        $this->options = array_merge($this->defaultOpts, $options);
        
        $this->frontend = new $feClass($options);
        $this->frontend->setCollection($symbols);
    }
    
    
    
    /**
     * Get HTML code
     * 
     * @param string $directive : user directive
     * @param string $error     : captcha error
     * 
     * @return string : HTML generated code
     */
    public function getHtmlCode(string $directive, string $error)
    {   
        $content = array (
            'directive' => $directive,
            'error' => $error
        );
        
        return $this->frontend->createHtml($content, 
                $this->options['cssEnable'], $this->options['jsEnable']);
    }
}
