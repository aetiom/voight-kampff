<?php

namespace VoightKampff;

/**
 * VoightKampff options
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Options extends \Scribe\Options {
    
    /**
     * @var string defaultLang : default language used for keywords, directives and errors
     */
    public $defaultLang = 'en';
    
    /**
     * @var string debug : debug mode
     */
    public $debug = false;
    
    /**
     * @var integer imageCount : images to use and display
     */
    public $imageCount = 7;

    /**
     * @var integer requestCount : requests to ask from user
     */
    public $requestCount = 2;

    /**
     * @var string cbPrefix : prefix used for/by checkbox elements (for id, name and label)
     */
    public $cbPrefix = 'vk-cb';
    
    /**
     * @var security : security configuration (user max attemps and timeout system)
     */
    public $security = [
        /**
         * @var integer maxAttempts : maximum attempts before getting timeout
         */
        'maxAttempts' => 3,

        /**
         * @var integer timeoutTime : number of seconds system timeouts a user
         */
        'timeoutTime' => 60,

        /**
         * @var integer inactivTime : seconds of inactivy before reseting timeout system
         */
        'inactivTime' => 600,
    ];
    
    /**
     * @var array frontend : frontend configuration for HTML, CSS and JS generating
     */
    public $frontend = [
        
        /**
         * @var string class : frontend library class used for generating captcha
         */
        'class'  => 'Fontawesome',
        
        /**
         * @var array options : frontend library options
         */
        'options' => [
            
            /**
             * @var string fa5Style : fontawesome 5
             */
            'fa5Style' => 'regular',
        ],
        
        /**
         * @var array colors : set custom colors for the generated CSS
         */
        'colors' => [

            /**
             * @var string background : background color for the div container
             */
            'background' => 'whitesmoke',

            /**
             * @var string selection : color of the selected elements
             */
            'selection'  => 'coral',

            /**
             * @var string error : color of error message and container border in case of error
             */
            'error'      => 'orangered'
        ]
    ];
    
    /**
     * @var array directiveCollection : directive language collection
     *
     * each entry can contain an array containing each supported languages key
     * if only a string is given, then it will be used for each languages
     */
    public $directiveCollection = [
        
        /**
         * @var string|array start : begining of the question
         */
        'start' => [
            'en' => 'Select pictures ',
            'fr' => 'Sélectionnez les images '
        ],
        
        /**
         * @var string|array linkSimple : simple linking element 
         * (a word in most of cases) 
         */
        'linkSimple' => [
            'en' => ' and ',
            'fr' => ' et '
        ],
        
        /**
         * @var string|array linkMulti : multiple linking element 
         * (a punctuation in most of cases)
         */
        'linkMulti'  => ', ',
        
        /**
         * @var string|array end : ending of the question
         */
        'end'        => '.',
        
        /**
         * @var string|array keywordIn : begining of each keyword integration
         */
        'keywordIn'  => '<strong>',
        
        /**
         * @var string|array keywordOut : ending of each keyword integration
         */
        'keywordOut' => '</strong>',
    ];
    
    
    /**
    * @var array custom_errors : costumizing captcha errors for user
    */
    public $errorCollection = [
        /**
         * @var array answer_empty : error message for empty captcha answers
         */
        'emptyAnswers' => [
            'en' => 'Please, select images.',
            'fr' => 'Sélectionnez les images s\'il vous plaît.'
        ],

        /**
         * @var array wrongAnswers : error message for wrong captcha answers
         */
        'wrongAnswers' => [
            'en' => 'Wrong answers.',
            'fr' => 'Mauvaises réponses.'
        ],

        /**
         * @var array timeout : error message when user get timeouted
         */
        'timeout' => [
            'en' => 'Too much wrong answers, please try again in %TIME% second(s).',
            'fr' => 'Trop de mauvaises réponses, réessayez dans %TIME% seconde(s).'
        ],
        
        /**
         * @var array inactive : error message when user get inactive
         */
        'inactive' => [
            'en' => 'You were too long to answer, please try again.',
            'fr' => 'Vous avez été trop long à répondre, veuillez réessayer.'
        ]
    ];
    
    /**
     * @var array pool : images pool, containing 'idStr' for image id in string, 
     * 'lang' keys for human language meanings in string and 'styleClass' for 
     * frontend special class in string
     */
    public $pool = [
        ['idStr' => '\f6b0', 
            'lang'  => ['en' => 'unicorn', 'fr' => 'licorne']],
        ['idStr' => '\f6b4', 
            'lang'  => ['en' => 'badger',  'fr' => 'blaireau']],
        ['idStr' => '\f6b5', 
            'lang'  => ['en' => 'bat',     'fr' => 'chauve-souris']],
        ['idStr' => '\f6be', 
            'lang'  => ['en' => 'cat',     'fr' => 'chat']],
        ['idStr' => '\f6c8', 
            'lang'  => ['en' => 'cow',     'fr' => 'vache']],
        ['idStr' => '\f520', 
            'lang'  => ['en' => 'bird',    'fr' => 'oiseau']],
        ['idStr' => '\f78e', 
            'lang'  => ['en' => 'deer',    'fr' => 'cerf']],
        ['idStr' => '\f6d3', 
            'lang'  => ['en' => 'dog',     'fr' => 'chien']],
        ['idStr' => '\f6d5', 
            'lang'  => ['en' => 'dragon',  'fr' => 'dragon']],
        ['idStr' => '\f6d8', 
            'lang'  => ['en' => 'duck',    'fr' => 'canard']],
        ['idStr' => '\f6da', 
            'lang'  => ['en' => 'elephant','fr' => 'éléphant']],
        ['idStr' => '\f578', 
            'lang'  => ['en' => 'fish',    'fr' => 'poisson']],
        ['idStr' => '\f52e', 
            'lang'  => ['en' => 'frog',    'fr' => 'grenouille']],
        ['idStr' => '\f6ed', 
            'lang'  => ['en' => 'hippo',   'fr' => 'hippopotame']],
        ['idStr' => '\f6f0', 
            'lang'  => ['en' => 'horse',   'fr' => 'cheval']],
        ['idStr' => '\f6fb', 
            'lang'  => ['en' => 'monkey',  'fr' => 'singe']],
        ['idStr' => '\f706', 
            'lang'  => ['en' => 'pig',     'fr' => 'cochon']],
        ['idStr' => '\f708', 
            'lang'  => ['en' => 'rabbit',  'fr' => 'lapin']],
        ['idStr' => '\f711', 
            'lang'  => ['en' => 'sheep',   'fr' => 'mouton']],
        ['idStr' => '\f716', 
            'lang'  => ['en' => 'snake',   'fr' => 'serpent']],
        ['idStr' => '\f717', 
            'lang'  => ['en' => 'spider',  'fr' => 'araignée']],
        ['idStr' => '\f71a', 
            'lang'  => ['en' => 'squirrel','fr' => 'écureuil']],
        ['idStr' => '\f726', 
            'lang'  => ['en' => 'turtle',  'fr' => 'tortue']],
        ['idStr' => '\f72c', 
            'lang'  => ['en' => 'whale',   'fr' => 'baleine']]
    ];
}
