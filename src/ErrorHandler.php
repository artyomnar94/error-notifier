<?php


namespace errorNotifier;


use Throwable;
use Yii;

/**
 * Class ErrorHandler
 *
 * @author artyomnar94@gmail.com
 * @version 1.0
 * @licence MIT
 */
class ErrorHandler
{
	/**
	 * Notifies maintainers via log & telegram message and user via flash message
	 *
	 * @param Throwable $error - error which has been thrown
	 * @param FlashConfigurator | null $flashMessageConfig - if provided then flash message will set for user
	 * @param string $category - the category of the log message
	 */
	public static function notify(Throwable $error,  $flashMessageConfig = null, string $category = 'application'): void
	{
		$message = $error->getMessage();
		Yii::error($message, $category);
		self::setFlashMessage($flashMessageConfig);
    if (!YII_ENV_DEV) {
    		self::sendNotification($error, $message);
    }
	}

	/**
	 * Sets user flash message for showing on client side
	 *
	 * @param FlashConfigurator | null $flashMessageConfig
	 */
	private static function setFlashMessage($flashMessageConfig): void
	{
		if ($flashMessageConfig) {
			Yii::$app->session->setFlash(
				$flashMessageConfig->getKey(),
				YII_ENV_PROD? $flashMessageConfig->getProductionMessage() : $flashMessageConfig->getTestMessage()
			);
		}
	}

	/**
	 * Sends message to telegram channel
	 *
	 * @param Throwable $error
	 * @param string $message
	 */
	private static function sendNotification(Throwable $error, string $message): void
	{
		$apiToken = Yii::$app->params['telegram_bot_token'];

		$data = [
			'chat_id' => Yii::$app->params['telegram_chat_id'],
			'text' => self::getMessageView($error, $message),
			'parse_mode' => 'HTML'
		];

		file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
	}

  /**
   * Generates html based message for telegram channel
   *
   * @param Throwable $error
	 * @param string $message
	 * @return string
  */
	private static function getMessageView(Throwable $error, string $message): string
	{
    $environment = YII_ENV;
		return "<b>ENV: #$environment</b>
      <b>Message: </b><i>$message</i>
      <b> File: </b><i>{$error->getFile()}</i>
      <b> Line: </b><i>{$error->getLine()}</i>
      <pre>{$error->getTraceAsString()}</pre>";
	}

}
