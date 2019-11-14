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
     * @var \VoightKampff\Options $options : config options
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
     * @var \VoightKampff\Directive $directive : directive container
     */
    protected $directive = null;
    
    /**
     * @var string $errorCode : error code
     */
    protected $errorCode = null;

    /**
     * @var Asset $error : error container
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
     * Get collection of images to render
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
     * @return string : captcha error code
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get error
     * @return Asset : captcha error container
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
        
        $this->collection = new Collection($id, $this->options);
        $this->security = new Security($this->options);
        
        $this->directive = new Directive($this->options, $this->collection);
    }


    /**
     * Reset captcha
     * by creating a new set of images and clearing wrong attemps and timeouts
     *
     * @return void
     */
    public function reset()
    {
        $this->collection->reset();
        $this->security->clear();
    }
    
    
    protected function setError($code)
    {
        $this->errorCode = $code;
        $asset = $code;

        if (isset($this->options->errors[$code])) {
            $asset = $this->options->errors[$code];
        }

        $this->error = new Asset('error');
        $this->error->update($asset);
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
            $this->setError('emptyAnswers');
            return false;
        }
        
        $this->security->addAttempt();
        
        if ($this->checkAnswers($userAnswers)) {
            $this->collection->clear();
            $this->security->clear();
            return true;
        }
        
        $this->setError('wrongAnswers');
        return false;
    }
    
    /**
     * Render captcha
     * 
     * @param string $lang : rendering language
     * @return string : html code
     * 
     * @throws \Exception if captcha id does not exist
     */
    public function render($lang = null)
    {
        if (!$this->checkTimeout()) {
            $this->checkInactivity(true);
        }
        
        $error = '';
        if ($this->error !== null) {
            $error = $this->error->fetch($lang);

            $error = str_replace(
                '%TIMEOUT%', $this->security->getTimeoutRemaining(), $error);
        }
        
        $render = new Render($this->options, $this->getImages());
        return $render->createHtml($this->getDirective($lang), $error);
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
            $this->setError('timeout');
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
            $this->setError('inactive');
        }
        
        $this->security->resetInactivity();
        $this->collection->reset();
        return true;
    }
}