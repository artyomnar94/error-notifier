<?php


namespace ErrorNotifier;


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
	 * @var bool whether show $_GET parameters in telegram report message
	 */
	public $showGet = true;

	/**
	 * @var bool whether show $_POST parameters in telegram report message
	 */
	public $showPost = true;

	/**
	 * @var bool whether show $_SESSION parameters in telegram report message
	 */
	public $showSession = true;

	/**
	 * @var bool whether show $_SERVER parameters in telegram report message
	 */
	public $showServer = true;

	/**
	 * ErrorHandler constructor.
	 * @param array $config - use for customization default attributes ['showSession' => false].
	 */
	public function __construct(array $config = [])
	{
		foreach ($config as $attribute => $value) {
			$this->$attribute = $value;
		}
	}

	/**
	 * Notifies maintainers via log & telegram message and user via flash message
	 *
	 * @param Throwable $error - error which has been thrown
	 * @param FlashConfigurator | null $flashMessageConfig - if provided then flash message will set for user
	 * @param string $message - text message, which provide into log file and telegram alert. By default will use message
	 * from exception object.
	 * @param string $category - the category of the log message
	 */
	public function notify(Throwable $error,  $flashMessageConfig = null, string $message = '', string $category = 'application'): void
	{
		try {
			if (empty($message)) {
				$message = $error->getMessage();
			}
			self::setFlashMessage($flashMessageConfig);
			if (YII_ENV_PROD) {
				self::sendNotification($error, $message);
			}
		} catch (Throwable $exception) {
			Yii::error("Fail on sending message to telegram : " . $exception->getMessage());
		} finally {
			Yii::error($message, $category);
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
	private function sendNotification(Throwable $error, string $message): void
	{
		$apiToken = Yii::$app->params['telegram_bot_token'];

		$data = [
			'chat_id' => Yii::$app->params['telegram_chat_id'],
			'text' => $this->getMessageView($error, $message),
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
	private function getMessageView(Throwable $error, string $message): string
	{
    	$environment = YII_ENV;
		$reportMessage = "<b>ENV: #$environment</b>
      		<b>Message: </b><i>$message</i>
			<b> File: </b><i>{$error->getFile()}</i>
			<b> Line: </b><i>{$error->getLine()}</i>
			<pre>{$error->getTraceAsString()}</pre>";

		if ($this->showGet) {
			$reportMessage .= "<h3>GET:</h3><pre>" .print_r($_GET) ."</pre>";
		}
		if ($this->showPost) {
			$reportMessage .= "<h3>POST:</h3><pre>" .print_r($_POST) ."</pre>";
		}
		if ($this->showSession) {
			$reportMessage .= "<h3>SESSION:</h3><pre>" .print_r($_SESSION) ."</pre>";
		}
		if ($this->showServer) {
			$reportMessage .= "<h3>SERVER:</h3><pre>" .print_r($_SERVER) ."</pre>";
		}

		return $reportMessage;
	}

}
