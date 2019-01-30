<?php

/**
 * VoightKampff default options
 * 
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */

return [
    /**
     * @var string defaultLang : default language used for keywords, directives and errors
     */
    'defaultLang' => 'en',
    
    /**
     * @var string debug : debug mode
     */
    'debug' => false,
    
    /**
     * @var integer imageCount : images to use and display
     */
    'imageCount' => 7,

    /**
     * @var integer requestCount : requests to ask from user
     */
    'requestCount' => 2,

    /**
     * @var string cbPrefix : prefix used for/by checkbox elements (for id, name and label)
     */
    'cbPrefix' => 'sc-cb',
    
    /**
     * @var security : security configuration (user max attemps and timeout system)
     */
    'security' => [
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
    ],
    
    /**
     * @var array frontend : frontend configuration for HTML, CSS and JS generating
     */
    'frontend' =>  [
        
        /**
         * @var string class : frontend library class used for generating captcha
         */
        'class'  => 'Fontawesome',
        
        /**
         * @var array options : frontend library options
         */
        'options' => [
            
            /**
             * @var string fa5_style : fontawesome 5
             */
            'fa5_style' => 'regular',
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
    ],
    
    /**
     * @var array directiveCollection : directive language collection
     *
     * each entry can contain an array containing each supported languages key
     * if only a string is given, then it will be used for each languages
     */
    'directiveCollection' => [
        
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
    ],
    
    
    /**
    * @var array custom_errors : costumizing captcha errors for user
    */
    'errorCollection' => [
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
        ]
    ]
];