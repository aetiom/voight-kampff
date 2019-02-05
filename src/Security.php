<?php

namespace VoightKampff;

/**
 * VoightKampff captcha security
 *
 * @author Aetiom <aetiom@protonmail.com>
 * @package VoightKampff
 * @version 1.0
 */
class Security {
    
    /**
     * @var \VoightKampff\Opions $options : config options
     */
    protected $options;
    
    /**
     * @var \aetiom\PhpExt\Session $session : captcha session
     */
    protected $session;
    
    /**
     * Get timeout status
     * @return boolean ! true if user get timeouted, false otherwise
     */
    
    public function getTimeoutStatus()
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
    public function getTimeoutRemaining()
    {
        if ($this->session->fetch('timeout') !== 0) {
            return $this->session->fetch('timeout') - time();
        }
        
        return 0;
    }
    
    /**
     * Get session activity status
     * @return boolean : true if session is active, false otherwise
     */
    public function isSessionActive()
    {
        $lastAttempt = $this->session->fetch('lastAttempt');
        
        if ($lastAttempt !== 0 && $lastAttempt 
                + $this->options->security['inactivTime'] < time()) {
            return false;
        }
        
        return true;
    }
    
    
    
    public function resetInactivity()
    {
        $this->session->select('lastAttempt')->update(0);
    }
    
    
    public function resetTimout()
    {
        $this->session->select('timeout')->update(0);
    }
    
    
    /**
     * Constructor
     * @param \VoightKampff\Options $options : options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
        
        $this->session = new \aetiom\PhpExt\Session('voight-kampff');
        $this->session->insert(
                array('attempts' => 0, 'lastAttempt' => 0, 'timeout' => 0)
        );
    }
    
    
    
    /**
     * Reset session security data
     * @param string $param : security parameter to clear
     */
    public function clear($param = null)
    {
        if ($param === null) {
            $this->session->update(
                array('attempts' => 0, 'lastAttempt' => 0, 'timeout' => 0));
        }
        
        $this->session->select($param)->update(0);
    }
    
    /**
     * Reccord new attempt
     * @return boolean true if session is still active, false otherwise
     */
    public function addAttempt()
    {   
        if ($this->isSessionActive()) {
            $this->session->select('attempts')->add(1);
            $session_is_active = true;
            
        } else {
            $this->session->select('attempts')->update(1);
            $session_is_active = false;
        }

        if ($this->session->fetch('attempts') >= $this->options->security['maxAttempts']) {
            $this->session->select('timeout')
                    ->update(time() + intval($this->options->security['timeoutTime']));
            $this->session->select('attempts')->update(0);
        }
        
        $this->session->select('lastAttempt')->update(time());
        return $session_is_active;
    }
}