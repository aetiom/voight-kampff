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
     * @param array $id    : collection id
     * @param array $param : captcha main parameters
     */
    public function __construct($id, $param)
    {
        $this->id = $id;
        $this->session = new \aetiom\PhpExt\Session('styx-captcha');
        
        $this->setCollection($param);
        
        shuffle($this->images);
        shuffle($this->keyWords);
        
        $this->session->select($this->id)
                ->update(array ('images'   => $this->images, 
                                'keyWords' => $this->keyWords, 
                                'answers'  => $this->answers));
    }
    
    
    
    /**
     * Clear actual collection
     */
    public function clear()
    {   
        $this->session->delete($this->id);
    }
    
    
    
    /**
     * Select images from captcha pool
     * 
     * @var array   $pool  : captcha pool
     * @var integer $count : number of images to select
     */
    protected function selectImages($pool, $count){
        
        $img = 0;
        while ($img < $count) {
            $randKey = mt_rand(0, 19);

            if (array_key_exists($randKey, $pool) 
                    && !array_key_exists($randKey, $this->images)) {
                $this->images[$img] = $pool[$randKey];
                $this->images[$img]['class'] = '.c'.$this->images[$img]['key'];
                unset($pool[$randKey]);

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
    protected function selectKeyWords($defaultLang, $count) {
        
        if (empty($this->images)) {
            throw new \Exception('image collection is empty,'.
                    ' cannot select key words');
        }
        
        $imageCount = count($this->images);
        $quest = 0;
        
        while ($quest < $count) {
            $rand = mt_rand(0, $imageCount - 1);

            $sK = array_search($this->images[$rand]['lang'], $this->keyWords);
            $sA = array_search($this->images[$rand]['key'], $this->answers);
            
            if ($sK !== false || $sA !== false) {
                continue;
            }
            
            if (!isset($this->images[$rand]['lang'][$defaultLang]) 
                    || empty($this->images[$rand]['lang'][$defaultLang])) {
                throw new \Exception('default language ['.$defaultLang.
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
    private function setCollection($param)
    {
        $currentCol = $this->session->select($this->id)->fetch();
        if (!empty($currentCol)) {
            $this->images   = $currentCol['images'];
            $this->keyWords = $currentCol['keyWords'];
            $this->answers  = $currentCol['answers'];
        }
        
        else {
            $initPool = $this->initiatePool($param['pool']);
            
            $this->selectImages($initPool, $param['imageCount']);
            $this->selectKeyWords($param['defaultLang'], $param['requestCount']);
            
            // remove 'lang' data from images selection for security reasons
            for ($i=0; $i<$param['imageCount']; $i++) {
                unset($this->images[$i]['lang']);
            }
        }
    }
    
    
    
    /**
     * Initiate captcha pool 
     * (generate unique random keys for each entry)
     * 
     * @param array $pool : captcha pool
     * @return array initiated and shuffled pool
     */
    private function initiatePool($pool)
    {
        $all_rand_num = array(0);
        
        foreach ($pool as $key => $array) {
            
            $new_rand = 0;
            while (array_search($new_rand, $all_rand_num) !== false) {
                $new_rand = mt_rand(1, 999999);
            }
            
            $all_rand_num[] = $new_rand;
            $pool[$key]['key'] = $new_rand;
        }
        
        shuffle($pool);
        return $pool;
    }
}
