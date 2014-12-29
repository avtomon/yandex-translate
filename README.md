## yandex_translate
================

https://tech.yandex.ru/translate/

Php class for using Yandex translate system

This system is like Bing Translator or Google translate, but api is free for use and without strict limits.

The API offers text translation features for over 30 languages. 

To begin using this class you need to get your own API key, you can do it just in pair of clicks.

Firstly create a Yandex account - https://passport.yandex.com/passport

Finally get your API key - http://api.yandex.ru/key/form.xml?service=trnsl

Official api description and documentation in English - http://api.yandex.com/translate/

in russian - http://api.yandex.ru/translate/

**Example:**

```
$key = 'YOUR_API_KEY';
$tr = new \Beeyev\YaTranslate\Trnsl($key);

$result = $tr->detect('Γεια');                                  //Detects language of the original text
var_dump($result);

$result = $tr->translate('Hello, how are you?','en-fr');        //Translate from French to English
var_dump($result);

$result = $tr->translate('Bonjour, comment allez-vous?','en');  //Detects language of the original text and translates in English
var_dump($result);
```
