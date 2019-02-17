<?php

namespace VoightKampff;

/**
 * VoightKampff captcha
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Captcha {
    
    /**
     * @var \VoightKampff\Opions $options : config options
     */
    protected $options;
    
    /**
     * @var \VoightKampff\Collection $collection : captcha collection
     */
    protected $collection = null;
    
    /**
     * @var \VoightKampff\Security $security : captcha security
     */
    protected $security = null;
    
    /**
     * @var \Scribe\Location\Collection $errorCol : error collection
     */
    protected $errorCol = null;
    
    /**
     * @var \VoightKampff\Directive $directive : directive container
     */
    protected $directive = null;
    
    /**
     * @var \Scribe\Location\Container $error : error container
     */
    protected $error = null;
    
    
    
    /**
     * Get posted images from HTML form
     * 
     * @param integer $count  : number of images to obtain
     * @param string  $prefix : checkbox prefix (id + cbPrefix)
     * 
     * @return array : posted images identifiers
     */
    static public function obtainPostedImages($count, $prefix)
    {
        $postedImages = array();

        for ($i = 0; $i < $count; $i++) {
            $key = $prefix.$i;
            
            if (!isset($_POST[$key])) {
                continue;
            }
            
            $postedImages[] = filter_var($_POST[$key], FILTER_VALIDATE_INT);
        }

        return $postedImages;
    }
    
    
    
    /**
     * Get collection of images to display
     * @return array : image collection
     */
    public function getImages()
    {
        if ($this->collection !== null) {
            return $this->collection->getImages();
        }
        
        return array();
    }
    
    /**
     * Get directive message for the selected language
     * 
     * @param string $lang : language tag
     * @return string : selected language directive message if it exist
     *                  default language directive otherwise
     */
    public function getDirective($lang = null)
    {
        if ($this->collection !== null) {
            return $this->directive->getMessage($lang);
        }
        
        return '';
    }
    
    /**
     * Get error
     * @return \Scribe\Location\Container : captcha error container
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * Get options
     * @return \VoightKampff\Options : captcha options
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    
    
    /**
     * Constructor : create new captcha
     * 
     * @param string $id    : captcha identifier
     * @param array  $param : optional parameters
     */
    public function __construct($id, $param = array()) 
    {
        $this->options = new Options($param);
        $this->options->cbPrefix = $id.'-'.$this->options->cbPrefix;
        
        $this->errorCol = new \Scribe\Location\Collection(
                $this->options->errorCollection, 
                $this->options->defaultLang);
        
        $this->collection = new Collection($id, $this->options);
        $this->security = new Security($this->options);
        
        $this->directive = new Directive($this->options, $this->collection);
    }
    
    
    
    /**
     * Verify captcha user answers
     *
     * @param array $userAnswers : user answers to check and certifiates 
     * @return boolean true in case of success, false otherwise
     */
    public function verify(array $userAnswers = null) 
    {
        if ($userAnswers === null) {
            $userAnswers = self::obtainPostedImages(
                $this->options->imageCount, $this->options->cbPrefix);
        }
        
        if ($this->checkInactivity()) {
            return false;
        }
        
        if (empty($userAnswers)) {
            $this->error = $this->errorCol->createContainer('emptyAnswers');
            return false;
        }
        
        $this->security->addAttempt();
        
        if ($this->checkAnswers($userAnswers)) {
            $this->collection->clear();
            $this->security->clear();
            return true;
        }
        
        $this->error = $this->errorCol->createContainer('wrongAnswers');
        return false;
    }
    
    /**
     * Display captcha
     * 
     * @param string $lang : display language
     * @return string : html code
     * 
     * @throws \Exception if captcha id does not exist
     */
    public function display($lang = null)
    {
        if (!$this->checkTimeout()) {
            $this->checkInactivity(true);
        }
        
        $error = '';
        if ($this->error !== null) {
            $error = $this->error->getMessage($lang);
        }
        
        $display = new Display($this->options, $this->getImages());
        return $display->createHtml($this->getDirective($lang), $error);
    }
    
    
    
    /**
     * Check user answers
     * 
     * @param array $userAnswers : user answers
     * @return boolean true if anwsers are those expected, false otherwise
     */
    private function checkAnswers($userAnswers)
    {
        $expectedAnswers = $this->collection->getAnswers();
        
        // return false if user does not send count of expected answers
        if (count($userAnswers) !== $this->options->requestCount) {
            return false;
        }
        
        $count = 0;
        foreach ($userAnswers as $as) {
            $key = array_search($as, $expectedAnswers);

            if ($key !== false) {
                unset($expectedAnswers[$key]);
                $count++;
            }
        }

        if ($count === $this->options->requestCount) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check timeout status
     * @return boolean true if user has been timeouted, false otherwise
     */
    private function checkTimeout()
    {
        if ($this->security->getTimeoutRemaining() > 0) {
            $this->error = $this->errorCol->createContainer('timeout', 
                array('%TIME%' => $this->security->getTimeoutRemaining()));

            $this->collection->clear();
            return true;
        } 
        
        if ($this->security->getTimeoutRemaining() < 0) {
            $this->security->resetTimout();
            $this->collection->reset();
        }
        
        return false;
    }
    
    /**
     * Check inactivity status
     * 
     * @param boolean $throwError : set false if error doesn't have to be throw
     * @return boolean true if session is active, false otherwise
     */
    private function checkInactivity(bool $throwError = true)
    {
        if ($this->security->isSessionActive()) {
            return false;
        }
        
        if ($throwError) {
            $this->error = $this->errorCol->createContainer('inactive');
        }
        
        $this->security->resetInactivity();
        $this->collection->reset();
        return true;
    }
}