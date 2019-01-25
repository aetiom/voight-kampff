<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VoightKampff
 *
 * @author Alexandre
 */
class VoightKampff {
    
    const ERR_UNKNOWN_CAPTCHA_ID = 0;
    
    const ERR_CAPTCHA_ID_ALREADY_USED = 0;
    
    
    /**
     * @var array $options : captcha options
     */
    protected $options;
    
    
    protected $captcha = array();
    
    protected $lang;
    
    
    /**
     * @var array $defaultOpts : default captcha options
     */
    private $defaultOpts = array(
        'imageCount'   => 7,
        'requestCount' => 2,
        'defaultLang'  => '',
        'debug'        => false,
        'security'     => array(
            'maxAttempts' => 3,
            'timeoutTime' => 60,
            'inactivTime' => 600
        )
    );
    
    
    
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
     * 
     * @param array  $param : optional parameters
     */
    public function __construct($param = array()) 
    {
        $this->options = self::mergeWithDefaultOptions($param);
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
        
        $display = new \VoightKampff\Display($this->captcha[$id]->getImages(), array(
            'frontend'  => $this->options['frontend'],
            'cbPrefix'  => $this->options['cbPrefix'],
            'cssEnable' => true,
            'jsEnable'  => true,
            'debug'     => $this->options['debug']
        ));
        
        $error = '';
        if ($this->captcha[$id]->getError() !== null) {
            $error = $this->captcha[$id]->getError()->getMessage($this->lang);
        }
        
        return $display->getHtmlCode(
                $this->captcha[$id]->getDirective($this->lang), $error);
    }
}
