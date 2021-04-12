# error-notifier
Component writes message into log-file, notifies via telegram and optionally set flash message.
Yii2 framework friendly.

[![Latest Stable Version](https://poser.pugx.org/artyomnar/error-notifier/v/stable.png)](https://packagist.org/packages/artyomnar/error-notifier)
[![Total Downloads](https://poser.pugx.org/artyomnar/error-notifier/downloads.png)](https://packagist.org/packages/artyomnar/error-notifier)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require artyomnar/error-notifier
```

or add

```
"artyomnar/error-notifier": "1.1"
```

to the require section of your composer.json.

Settings
------------

 - Set in params-local.php file elements:
   - *'telegram_bot_token'* => *'your_bot_token'*
   - *'telegram_chat_id'* => *'your_chat'* Unique identifier for the target chat or username of the target channel (in the format @channelusername)

Usage
------------

```
try {
    //your code which can throw an exception
} catch (Throwable $exception) {
    ErrorNotifier\ErrorHandler::notify($exception);
}
```
```
try {
    //your code which can throw an exception
} catch (Throwable $exception) {
    ErrorNotifier\ErrorHandler::notify(
        $exception,
        new FlashConfigurator(
            $exception->getMessage(),
            'Service unavailable, try later!'
        )
    );
}
```
