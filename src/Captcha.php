<?php

namespace VoightKampff;

/**
 * VoightKampff captcha manager
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Captcha {
    
    /**
     * @var Array $options : captcha options
     */
    protected $options;
    
    /**
     * @var Collection $collection : captcha collection
     */
    protected $collection = null;
    
    /**
     * @var \aetiom\Go4\Session $session : captcha session
     */
    protected $session = null;
    
    /**
     * @var \aetiom\Go4\Collection $directiveCol : captcha directive collection
     */
    protected $directiveCol = null;
    
    /**
     * @var \aetiom\Go4\Collection $errorCol : captcha error collection
     */
    protected $errorCol = null;
    
    /**
     * @var \aetiom\Go4\Container $error : captcha error container
     */
    protected $error = null;
    
    
    
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
    public function getDirective($lang = '')
    {
        if ($this->collection == null) {
            return '';
        }
        
        return $this->formatDirective($lang);
    }
    
    /**
     * Get error
     * @return \aetiom\Go4\Container : captcha error container
     */
    public function getError()
    {
        return $this->error;
    }
    
    
    
    /**
     * Constructor : create new captcha
     * 
     * @param string $id    : captcha identifier
     * @param array  $param : optional parameters
     */
    public function __construct($id, $param = array()) 
    {
        $this->initiate($param);
        
        $this->collection = new Collection($id, $this->options);
        $this->security = new Security($this->options['security']);
        
        if ($this->security->get_timeout_status()) {
            $this->error = $this->errorCol->createContainer('timeout', 
                array('%TIME%' => $this->security->get_timeout_remaining()));

            $this->collection->clear();
        }
    }
    
    
    
    /**
     * Verify captcha user answers
     *
     * @param array $userAnswers : user answers to check and certifiates 
     * @return boolean true in case of success, false otherwise
     */
    public function verify($userAnswers) 
    {
        $answerList = $this->collection->getAnswers();
        
        if (empty($userAnswers)) {
            $this->error = $this->errorCol->createContainer('emptyAnswers');
            return false;
        }
        
        if ($this->checkAnswers($userAnswers, $answerList)) {
            $this->collection->clear();
            $this->security->clear();
            return true;
        }
        
        if (!$this->security->add_attempt()) {
            $this->collection->clear();
        }
        
        $this->error = $this->errorCol->createContainer('wrongAnswers');
        return false;
    }
    
    
    
    /**
     * Initiate options and collections
     * @param array $param : optional parameters
     */
    private function initiate($param)
    {
        $this->options = VoightKampff::mergeWithDefaultOptions($param);
        
        $this->directiveCol = new \aetiom\Go4\Collection(
                $this->options['directiveCollection'], 
                $this->options['defaultLang']);
        
        $this->errorCol = new \aetiom\Go4\Collection(
                $this->options['errorCollection'], 
                $this->options['defaultLang']);
    }
    
    /**
     * Check user answers
     * 
     * @param array $userAnswers     : user answers
     * @param array $expectedAnswers : system expected answers
     * 
     * @return boolean true if anwsers are those expected, false otherwise
     */
    private function checkAnswers($userAnswers, $expectedAnswers)
    {
        // return false if user check more answers than exepcted
        if (count($userAnswers) > $this->options['requestCount']) {
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

        if ($count === $this->options['requestCount']) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Format directive
     * 
     * @param string $lang : selected language
     * @return string formated directive
     */
    private function formatDirective($lang)
    {
        $start = $this->directiveCol->createContainer('start');
        $link1 = $this->directiveCol->createContainer('linkSimple');
        $link2 = $this->directiveCol->createContainer('linkMulti');
        $end   = $this->directiveCol->createContainer('end');
        $kwIn  = $this->directiveCol->createContainer('keywordIn');
        $kwOut = $this->directiveCol->createContainer('keywordOut');
        
        $dirStr = '';
        foreach ($this->collection->getKeyWords() as $key => $q) {
            if (!empty($dirStr)) {
                if ($this->options['requestCount'] > 2 
                        && $key < $this->options['requestCount'] - 1) {
                    $dirStr .= $link2->getMessage($lang);
                } else {
                    $dirStr .= $link1->getMessage($lang);
                }
            }

            
            $dirStr .= $kwIn->getMessage($lang).$q->getMessage($lang).$kwOut->getMessage($lang);
            
        }

        return $start->getMessage($lang).$dirStr.$end->getMessage($lang);
    }
    
}