# voight-kampff
PHP captcha generator

## Usage
### Create captcha
```php
$pool = array(
    array(
        'idStr' => '0120',
        'lang'   => array(
            'en' => 'table',
            'fr' => 'table'
        )
    ),
    array(
        'idStr' => '2501',
        'lang'   => array(
            'en' => 'glass',
            'fr' => 'verre'
        )
    ),
    
    ...
    
    array(
        'idStr' => '3241',
        'lang'   => array(
            'en' => 'book',
            'fr' => 'livre'
        )
    )
);

$captcha = new VoightKampff\Captcha('captcha_identifier_in_string', $pool);
```


### Verify captcha
```php
for ($i = 0; $i < $param['imageCount']; $i++) {
    $cbId[] = $param['cbPrefix'].$i;
}

$answers = \VoightKampff\Captcha::obtainPostedImages($cbId);
$captcha->verify($answers);
```

### Display captcha
```php
$images = $captcha->getImages();
$directive = $captcha->getDirective($lang);
$error = '';

if ($captcha->getError() !== null) {
    $error = $captcha->getError()->getMessage($lang);
}

$display = new \VoightKampff\Display($images);
$htmlCode = $display->getHtmlCode($directive, $error);
```
