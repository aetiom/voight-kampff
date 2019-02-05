<?php

namespace VoightKampff;

/**
 * VoightKampff captcha collection
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Collection {
    
    /**
     * @var string $id : collection identifier
     */
    protected $id = '';
    
    /**
     * @var \VoightKampff\Opions $options : config options
     */
    protected $options;
    
    /**
     * @var array $images : list of captcha images
     */
    protected $images = array();
    
    /**
     * @var array $answers : list of expected answers
     */
    protected $answers = array();
    
    /**
     * @var array $keyWords : list of directive key words
     */
    protected $keyWords = array();
    
    /**
     * @var \aetiom\PhpExt\Session $session : captcha session
     */
    protected $session = null;
    
    
    
    /**
     * Get collection identifier
     * @return string : collection identifier
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get captcha images
     * @return array list of images
     */
    public function getImages()
    {
        return $this->images;
    }
    
    /**
     * Get expected answers
     * @return array list of expected answers
     */
    public function getAnswers()
    {
        return $this->answers;
    }
    
    /**
     * Get directive key words
     * @return array list of directive key words
     */
    public function getKeyWords()
    {
        return $this->keyWords;
    }
    
    
    /**
     * Constructor
     * 
     * @param string $id                     : collection id
     * @param \VoightKampff\Options $options : $options
     */
    public function __construct(string $id, Options $options)
    {
        $this->id = $id;
        $this->options = $options;
        
        $this->session = new \aetiom\PhpExt\Session('voight-kampff');
        $currentCol = $this->session->select($this->id)->fetch();
        
        if (!empty($currentCol)) {
            $this->images   = $currentCol['images'];
            $this->keyWords = $currentCol['keyWords'];
            $this->answers  = $currentCol['answers'];
        } else {
            $this->setNewCollection();
        }
        
        shuffle($this->images);
        shuffle($this->keyWords);
        
        $this->updateSession();
    }
    
    
    
    /**
     * Reset collection by creating a new one
     */
    public function reset()
    {
        $this->setNewCollection();
        $this->updateSession();
    }
    
    
    
    /**
     * Clear actual collection
     */
    public function clear()
    {   
        $this->images = array();
        $this->answers = array();
        $this->keyWords = array();
        
        $this->updateSession();
    }
    
    
    public function delete()
    {
        $this->images = array();
        $this->answers = array();
        $this->keyWords = array();
        
        $this->session->delete($this->id);
    }
    
    private function updateSession()
    {
        $this->session->select($this->id)
                ->update(array ('images'   => $this->images, 
                                'keyWords' => $this->keyWords, 
                                'answers'  => $this->answers));
    }
    
    
    
    /**
     * Select images from captcha pool
     * 
     * @var array   $pool  : captcha pool
     * @var integer $count : number of images to select
     */
    protected function selectImages(){
        
        $img = 0;
        while ($img < $this->options->imageCount) {
            $randKey = mt_rand(0, 19);

            if (array_key_exists($randKey, $this->options->pool) 
                    && !array_key_exists($randKey, $this->images)) {
                $this->images[$img] = $this->options->pool[$randKey];
                $this->images[$img]['class'] = '.c'.$this->images[$img]['key'];
                unset($this->options->pool[$randKey]);

                $img++;
            }
        }
    }
    
    
    
    /**
     * Select key words (lang values) from captcha pool
     * 
     * @var string  $defaultLang : default language
     * @var integer $count        : number of key words to select
     */
    protected function selectKeyWords() {
        
        if (empty($this->images)) {
            throw new \Exception('image collection is empty,'.
                    ' cannot select key words');
        }
        
        $imageCount = count($this->images);
        $quest = 0;
        
        while ($quest < $this->options->requestCount) {
            $rand = mt_rand(0, $imageCount - 1);

            $sK = array_search($this->images[$rand]['lang'], $this->keyWords);
            $sA = array_search($this->images[$rand]['key'], $this->answers);
            
            if ($sK !== false || $sA !== false) {
                continue;
            }
            
            if (!isset($this->images[$rand]['lang'][$this->options->defaultLang]) 
                    || empty($this->images[$rand]['lang'][$this->options->defaultLang])) {
                throw new \Exception('default language ['.$this->options->defaultLang.
                        '] is not set for pool entry '.$rand.
                        ' ('.$this->images['idStr'].')');
            }
            
            // store captcha directives and answers into session
            $this->keyWords[] = $this->images[$rand]['lang'];
            $this->answers[]  = $this->images[$rand]['key'];

            $quest++;
        }
    }
    
    
    
    /**
     * Set collection
     * 
     * Use collection already set in session if it exists, else use param to
     * construct a new collection set
     * 
     * @param array $param : collection parameters containing 'imageCount', 
     * 'defaultLang' and 'requestCount' keys
     */
    private function setNewCollection()
    {
        $this->initiatePool();

        $this->selectImages();
        $this->selectKeyWords();

        // remove 'lang' data from images selection for security reasons
        for ($i=0; $i<$this->options->imageCount; $i++) {
            unset($this->images[$i]['lang']);
        }
    }
    
    
    
    /**
     * Initiate captcha pool 
     * (generate unique random keys for each entry)
     * 
     * @param array $pool : captcha pool
     * @return array initiated and shuffled pool
     */
    private function initiatePool()
    {
        $all_rand_num = array(0);
        
        foreach ($this->options->pool as $key => $array) {
            
            $new_rand = 0;
            while (array_search($new_rand, $all_rand_num) !== false) {
                $new_rand = mt_rand(1, 999999);
            }
            
            $all_rand_num[] = $new_rand;
            $this->options->pool[$key]['key'] = $new_rand;
        }
        
        shuffle($this->options->pool);
    }
}
