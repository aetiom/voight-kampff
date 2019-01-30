<?php

/**
 * VoightKampff
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class VoightKampff {
    
    /**
     * @const integer ERR_UNKNOWN_CAPTCHA_ID : 
     * Exception code for unknown captcha id
     */
    const ERR_UNKNOWN_CAPTCHA_ID = 101;
    
    /**
     * @const integer ERR_CAPTCHA_ID_ALREADY_USED : 
     * Exception code for captcha id that are already used by the system
     */
    const ERR_CAPTCHA_ID_ALREADY_USED = 102;
    
    /**
     * @const integer ERR_FRONTEND_CLASS_MISSING :
     * Esception code for missing frontend class in options/parameters
     */
    const ERR_FRONTEND_CLASS_MISSING = 201;
    
    /**
     * @const integer ERR_FRONTEND_CLASS_UNKNOWN : 
     * Exception code for unknown frontend class, if frontend class does not 
     * exist for the current system
     */
    const ERR_FRONTEND_CLASS_UNKNOWN = 202;
    
    
    
    /**
     * @var array $options : captcha options
     */
    protected $options;
    
    /**
     * @var array $captcha : array of \VoightKampff\Captcha objects 
     * with captcha collection 'id' as key
     */
    protected $captcha = array();
    
    /**
     * @var \VoightKampff\Frontend $frontend : current frontend instance
     */
    protected $frontend;
    
    /**
     * @var string $lang : current language
     */
    protected $lang;
    
    
    
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
     * Merge custom parameters with default options
     * 
     * @param array $param : custom parameters
     * @return array : merged options
     */
    static public function mergeWithDefaultOptions($param)
    {
        $options = require(__DIR__.'Config/defaultOptions.php');
        return array_merge($options, $param);
    }
    
    
    
    /**
     * Constructor
     * @param array $param : optional parameters
     */
    public function __construct($param = array()) 
    {
        $this->options = $param;
        if (!isset($param['nomerge']) || $param['nomerge'] === false) {
            $this->options = self::mergeWithDefaultOptions($param);
        }
        
        $this->lang = $this->options['defaultLang'];
        
        if (!isset($this->options['pool']) || empty($this->options['pool'])) {
            $this->options['pool'] = require(__DIR__.'Config/defaultPool.php');
        }
    }
    
    
    
    /**
     * Create new captcha(s)
     * 
     * @param string|array $identifiers : unique identifier in string, 
     * or string identifier list in array for multiple captcha creating
     */
    public function create($identifiers = null)
    {
        if ($identifiers === null) {
            $this->captcha['vk'] = $this->createCaptcha('vk');
        }
        
        if (!is_array($identifiers)) {
            $identifiers = array($identifiers);
        }
        
        foreach ($identifiers as $id) {
            $this->captcha[$id] = $this->createCaptcha($id);
        }
    }
    
    /**
     * Verify captcha(s)
     * 
     * @param string|array $identifiers : unique identifier in string, 
     * or string identifier list in array for multiple captcha verifying
     * 
     * @return boolean|array : boolean state or list of boolean state 
     * in array with captcha id as key
     */
    public function verify($identifiers = null)
    {
        if ($identifiers === null) {
            return $this->verify(array_keys($this->captcha));
        }
        
        if (is_array($identifiers)) {
            if (count($identifiers) === 1) {
                return $this->verifyCaptcha($identifiers[0]);
            }
            
            $verif = array();
            foreach ($identifiers as $id) {
                $verif[$id] = $this->verifyCaptcha($id);
            }
            
            return $verif;
        }
        
        return $this->verifyCaptcha($identifiers);
    }
    
    /**
     * Display captcha(s)
     * 
     * @param string|array $identifiers : unique identifier in string, 
     * or string identifier list in array for multiple captcha displaying
     * 
     * @return string|array : html code or list of code 
     * in array with captcha id as key
     */
    public function display($identifiers = null)
    {
        $this->initiateFrontend();
        
        if ($identifiers === null) {
            return $this->display(array_keys($this->captcha));
        }
        
        if (is_array($identifiers)) {
            if (count($identifiers) === 1) {
                return $this->displayCaptcha($identifiers[0]);
            }
            
            $verif = array();
            foreach ($identifiers as $id) {
                $verif[$id] = $this->displayCaptcha($id);
            }
            
            return $verif;
        }
        
        return $this->displayCaptcha($identifiers);
    }
    
    
    
    /**
     * Create new captcha
     * 
     * @param string $id : captcha identifier
     * @return \VoightKampff\Captcha : captcha
     * 
     * @throws \Exception if captcha id is already used
     */
    private function createCaptcha($id) 
    {
        if (isset($this->captcha[$id]) && !empty($this->captcha[$id])) {
            throw new \Exception('captcha id "'.$id.'" is already used', 
                    self::ERR_CAPTCHA_ID_ALREADY_USED);
        }
        
        return new \VoightKampff\Captcha($id, $this->pool, $this->options);
    }
    
    /**
     * Verify captcha
     * 
     * @param string $id : captcha identifier
     * @return boolean : true in case of success, false otherwise
     * 
     * @throws \Exception if captcha id does not exist
     */
    private function verifyCaptcha($id)
    {
        if (!isset($this->captcha[$id]) && empty($this->captcha[$id])) {
            throw new \Exception('unknown captcha id "'.$id.'"', 
                    self::ERR_UNKNOWN_CAPTCHA_ID);
        }
        
        $anwsers = self::obtainPostedImages(
                $this->options['imageCount'], $this->options['cbPrefix']);
        
        return $this->captcha[$id]->verify($anwsers);
    }
    
    /**
     * Display captcha
     * 
     * @param string $id : captcha identifier
     * @return string : html code
     * 
     * @throws \Exception if captcha id does not exist
     */
    private function displayCaptcha($id)
    {
        if (!isset($this->captcha[$id]) && empty($this->captcha[$id])) {
            throw new \Exception('unknown captcha id "'.$id.'"', 
                    self::ERR_UNKNOWN_CAPTCHA_ID);
        }
        
        $this->frontend->setCollection($this->captcha[$id]->getImages());
        
        $error = '';
        if ($this->captcha[$id]->getError() !== null) {
            $error = $this->captcha[$id]->getError()->getMessage($this->lang);
        }
        
        return $this->frontend->createHtml(array (
            'directive' => $this->captcha[$id]->getDirective($this->lang),
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
        
        $this->frontend = new $feClass($this->options['frontend']);
    }
}
