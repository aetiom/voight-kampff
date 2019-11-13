<?php

namespace VoightKampff;

/**
 * VoightKampff display controller
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Render {
    
    /**
     * @const integer ERR_FRONTEND_CLASS_MISSING :
     * Exception code for missing frontend class in options/parameters
     */
    const ERR_FRONTEND_CLASS_MISSING = 201;
    
    /**
     * @const integer ERR_FRONTEND_CLASS_UNKNOWN : 
     * Exception code for unknown frontend class, if frontend class does not 
     * exist for the current system
     */
    const ERR_FRONTEND_CLASS_UNKNOWN = 202;
    
    /**
     * @var \VoightKampff\Opions $options : config options
     */
    protected $options;
    
    /**
     * @var array $images : captcha collection
     */
    protected $images;
    
    
    
    public function __construct(Options $options, array $images) 
    {
        $this->options = $options;
        $this->images = $images;
    }
    
    
    
    /**
     * Create HTML code
     * 
     * @param string $directive : user directive
     * @param string $error     : captcha error
     * 
     * @return string : html code
     */
    public function createHtml(string $directive, string $error)
    {
        $frontend = $this->initiateFrontend();
        $frontend->setCollection($this->images);
        
        return $frontend->createHtml(array (
            'directive' => $directive,
            'error' => $error
        ));
    }
    
    
    
    /**
     * Initiate Frontend instance
     * @throws \Exception if frontend class is missing or unknown
     */
    private function initiateFrontend()
    {
        if (!isset($this->options->frontend['class']) 
                || empty($this->options->frontend['class'])) {
            throw new \Exception('frontend class is not set in options', 
                    self::ERR_FRONTEND_CLASS_MISSING);
        }
        
        $feClass = $this->options->frontend['class'];
        if (strstr('\\', $feClass) === false) {
            $feClass = '\\VoightKampff\\FrontEnd\\'.$feClass;
        }
        
        if (!class_exists($feClass)) {
            throw new \Exception('frontend class "'.$feClass.'" does not exist', 
                    self::ERR_FRONTEND_CLASS_UNKNOWN);
        }
        
        return new $feClass($this->options);
    }
}
