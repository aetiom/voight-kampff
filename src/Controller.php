<?php

namespace VoightKampff;

/**
 * VoightKampff captcha manager
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Controller {
    
    /**
     * @const integer ERR_FRONTEND_CLASS_MISSING :
     * Esception code for missing frontend class in options/parameters
     */
    const ERR_FRONTEND_CLASS_MISSING = 101;
    
    /**
     * @const integer ERR_FRONTEND_CLASS_UNKNOWN : 
     * Exception code for unknown frontend class, if frontend class does not 
     * exist for the current system
     */
    const ERR_FRONTEND_CLASS_UNKNOWN = 102;
    
    
    
    /**
     * @var array $options : captcha options
     */
    protected $options;
    
    /**
     * @var \VoightKampff\Frontend $frontend : current frontend instance
     */
    protected $frontend;
    
    /**
     * @var string $lang : current language
     */
    protected $lang;
    
    
    
    /**
     * Get posted images from HTML form
     * 
     * @param integer $imageCount : number of images to obtain
     * @param string  $cbPrefix   : checkbox prefix
     * 
     * @return array : posted images identifiers
     */
    static public function obtainPostedImages($imageCount, $cbPrefix)
    {
        $postedImages[] = array();

        for ($i = 0; $i < $imageCount; $i++) {
            $key = $cbPrefix.$i;
            
            if (!isset($_POST[$key])) {
                continue;
            }
            
            $postedImages[] = filter_var($_POST[$key], FILTER_VALIDATE_INT);
        }

        return $postedImages;
    }
    
    
    
    /**
     * Get posted images from HTML form, using VoightKampff options
     * @return array : posted images identifiers
     */
    public function getPostedImages()
    {
        return self::obtainPostedImages(
                $this->options['imageCount'], $this->options['cbPrefix']);
    }
    
    /**
     * Set current language
     * @param string $lang : lang
     */
    public function setLang(string $lang)
    {
        $this->lang = $lang;
    }
    
    /**
     * Set debug state
     * @param boolean $state : debug state
     */
    public function setDebug(bool $state = true)
    {
        $this->options['debug'] = $state;
    }
    
    /**
     * Set pool
     * @param array $pool : pool items
     */
    public function setPool(array $pool)
    {
        $this->options['pool'] = $pool;
    }
    
    
    
    /**
     * Constructor
     * @param array $param : optional parameters
     */
    public function __construct($param = array()) 
    {
        $this->options = $param;
        
        if (!isset($param['nomerge']) || $param['nomerge'] === false) {
            $default = require(__DIR__.'Config/defaultOptions.php');
            $this->options = array_merge($default, $param);
        }
        
        $this->lang = $this->options['defaultLang'];
        
        if (!isset($this->options['pool']) || empty($this->options['pool'])) {
            $this->options['pool'] = require(__DIR__.'Config/defaultPool.php');
        }
    }
    
    
    
    /**
     * Create new captcha
     * 
     * @param string $id : captcha identifier
     * @return \VoightKampff\Captcha : captcha
     */
    public function createCaptcha(string $id = 'vk') 
    {
        return new \VoightKampff\Captcha($id, $this->options);
    }
    
    /**
     * Create many new captchas
     * 
     * @param array $idList : captcha identifier list (strings in array)
     * @return array : \VoightKampff\Captcha captcha in array
     */
    public function createManyCaptcha(array $idList) 
    {
        $captcha = array();
        
        foreach ($idList as $id) {
            $captcha[$id] = $this->createCaptcha($id);
        }
        
        return $captcha;
    }
    
    /**
     * Display captcha
     * 
     * @param \VoightKampff\Captcha $captcha : captcha to display
     * @return string : html code
     * 
     * @throws \Exception if captcha id does not exist
     */
    public function createHtmlCode(\VoightKampff\Captcha $captcha)
    {
        if ($this->frontend === null) {
            $this->initiateFrontend();
        }
        
        $this->frontend->setCollection($captcha->getImages());
        
        $error = '';
        if ($captcha->getError() !== null) {
            $error = $captcha->getError()->getMessage($this->lang);
        }
        
        return $this->frontend->createHtml(array (
            'directive' => $captcha->getDirective($this->lang),
            'error' => $error
        ));
    }
    
    
    
    /**
     * Initiate Frontend instance
     * @throws \Exception if frontend class is missing or unknown
     */
    private function initiateFrontend()
    {
        if (!isset($this->options['frontend']['class']) 
                || empty($this->options['frontend']['class'])) {
            throw new \Exception('frontend class is not set in options', 
                    self::ERR_FRONTEND_CLASS_MISSING);
        }
        
        $feClass = $this->options['frontend']['class'];
        if (strstr('\\', $feClass) === false) {
            $feClass = 'FrontEnd\\'.$feClass;
        }
        
        if (!class_exists($feClass)) {
            throw new \Exception('frontend class "'.$feClass.'" does not exist', 
                    self::ERR_FRONTEND_CLASS_UNKNOWN);
        }
        
        $feOpts = $this->options['frontend'];
        $feOpts['cbPrefix'] = $this->options['cbPrefix'];
        
        $this->frontend = new $feClass($feOpts);
    }
}
