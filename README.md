# voight-kampff
PHP captcha generator

# Usage
__Create captcha__
$param = array(
    'imageCount'   => 7,
    'requestCount' => 2,
    'cbPrefix'     => 'html_checkbox_id_prefix',
    'defaultLang'  => 'default_lang_tag',
    'security'     => array(
        'maxAttempts' => 3,
        'timeoutTime' => 60,
        'inactivTime' => 600
    ),
    'frontend'      => array(),
    'directive_lib' => array(),
    'custom_errors' => array()
);

$captcha = new VoightKampff\Captcha($param);
$captcha->create('contact-form');



__Verify captcha__
for ($i = 0; $i < $symbolsCount; $i++) {
    $cb_id[] = $this->captchaOpts->getValue('cbPrefix').$i;
}

$answers = \VoightKampff\Captcha::obtainPostedImages($cb_id);
$captcha->verify('contact-form', $answers);


__Display captcha__
$symbols = $this->captcha->getImages();
$directive = $captcha->getDirective($lang);
$error = '';

if ($this->captcha->getError() !== null) {
    $error = $captcha->getError()->getMessage($lang);
}

$displayOptions = array(
    'frontend'  => array(
      'options' => array(),
      'colors'  => array(
          'background' => 'whitesmoke',
          'selection'  => 'cornflowerblue',
          'error'      => 'orangered'
      )
    ),
    'cbPrefix'  => 'html_checkbox_id_prefix',
    'cssEnable' => true,
    'jsEnable'  => true,
    'debug'     => false
)

$display = new \VoightKampff\Display($symbols, $displayOptions);
$htmlCode = $display->getHtmlCode($directive, $error);
