# error-notifier
Component writes message into log-file, notifies via telegram and optionally set flash message.
Yii2 framework friendly.

#Start
 - Set in params-local.php file elements:
   - *'telegram_bot_token'* => *'your_bot_token'*
   - *'telegram_chat_id'* => *'your_chat'* Unique identifier for the target chat or username of the target channel (in the format @channelusername)

#Usage
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
