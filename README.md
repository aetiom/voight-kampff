# voight-kampff
PHP captcha generator

## Usage
### Create captcha
```php
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
```


### Verify captcha
```php
for ($i = 0; $i < $param['imageCount']; $i++) {
    $cbId[] = $param['cbPrefix'].$i;
}

$answers = \VoightKampff\Captcha::obtainPostedImages($cbId);
$captcha->verify('contact-form', $answers);
```

### Display captcha
```php
$images = $captcha->getImages();
$directive = $captcha->getDirective($lang);
$error = '';

if ($captcha->getError() !== null) {
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

$display = new \VoightKampff\Display($images, $displayOptions);
$htmlCode = $display->getHtmlCode($directive, $error);
```
