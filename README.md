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
$count = $captcha->getOptions()->imageCount;
$prefix = $captcha->getOptions()->cbPrefix;

$answers = \VoightKampff\Captcha::obtainPostedImages($count, $prefix);
$captcha->verify($answers);
```

### Display captcha
```php
$images = $captcha->getImages();
$directive = $captcha->getDirective($lang);
$error = '';

if ($captcha->getError() !== null) {
    $error = $captcha->getError()->fetch($lang);
}

$render = new \VoightKampff\Render($captcha->getOptions(), $images);
$htmlCode = $render->createHtml($directive, $error);
```
