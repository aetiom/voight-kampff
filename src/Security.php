<?php

namespace VoightKampff;

/**
 * Captcha security
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Security {
    
    /**
     * @var array $opts : config options
     */
    protected $opts;
    
    /**
     * @var \aetiom\Go4\Session $session : captcha session
     */
    protected $session;
    
    /**
     * Get timeout status
     * @return boolean ! true if user get timeouted, false otherwise
     */
    public function get_timeout_status()
    {
        $timeout = $this->session->fetch('timeout');
        
        if ($timeout !== 0 && $timeout > time()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get timeout remaining time
     * @return integer : remaining time
     */
    public function get_timeout_remaining()
    {
        if ($this->get_timeout_status()) {
            return $this->session->fetch('timeout') - time();
        }
        
        return 0;
    }
    
    /**
     * Get session activity status
     * @return boolean : true if session is active, false otherwise
     */
    public function is_session_active()
    {
        $lastAttempt = $this->session->fetch('lastAttempt');
        
        if ($lastAttempt !== 0 && $lastAttempt 
                + $this->opt['inactivTime'] < time()) {
            return false;
        }
        
        return true;
    }
    
    
    
    /**
     * Constructor
     * @param Array $options : security options
     */
    public function __construct(Array $options)
    {
        $this->opts = $options;
        
        $this->session = new \aetiom\Go4\Session('voight-kampff');
        $this->session->insert(array('attempts' => 0, 'lastAttempt' => 0, 'timeout' => 0));
    }
    
    
    /**
     * Reset session security data and unset actual collection
     */
    public function clear()
    {
        $this->session->update(
                array('attempts' => 0, 'lastAttempt' => 0, 'timeout' => 0));
    }
    
    
    
    /**
     * Reccord new attempt
     * @return boolean true if session is still active, false otherwise
     */
    public function add_attempt()
    {   
        if ($this->is_session_active()) {
            $this->session->select('attempts')->add(1);
            $session_is_active = true;
            
        } else {
            $this->session->select('attempts')->update(1);
            $session_is_active = false;
        }

        if ($this->session->fetch('attempts') >= $this->opts['maxAttempts']) {
            $this->session->select('timeout')
                    ->update(time() + intval($this->opts['timeoutTime']));
            $this->session->select('attempts')->update(0);
        }
        
        $this->session->select('lastAttempt')->update(time());
        return $session_is_active;
    }
}