<?php

declare(strict_types=1);


namespace OCA\MyAdminMonitoring\Listener;

use OCP\Mail\IMailer;

class AppManagement {
	private IMailer $mailer;

	public function __construct(IMailer $mailer) {
		$this->mailer = $mailer;
	}

	/**
	 * @param string $appName
	 */
	public function enableApp(string $appName): void {
		$this->mail('App "%s" enabled',
			['app' => $appName]

		);
	}

	/**
	 * @param string $appName
	 * @param string[] $groups
	 */
	public function enableAppForGroups(string $appName, array $groups): void {
		$this->mail('App "%1$s" enabled for groups: %2$s',
			['app' => $appName, 'groups' => implode(', ', $groups)]

		);
	}

	/**
	 * @param string $appName
	 */
	public function disableApp(string $appName): void {
		$this->mail('App "%s" disabled',
			['app' => $appName]
			
		);
	}

	private function mail(string $string, array $params): void {
		$text =
			vsprintf(
				$string,
				$params
			);

		$mailTemplate = $this->mailer->createEMailTemplate('app_mail_notifications.mail');
		$mailTemplate->setSubject($text);
		$mailTemplate->addBodyText($text);

		$mailer = \OC::$server->getMailer();
		$message = $this->mailer->createMessage();
		$message->setFrom(['wnd@xiller.com' => 'Nextcloud Notifier']);
		$message->setTo(['njiandzebewilfriedjunior.com' => 'Recipient']);
		$message->useTemplate($mailTemplate);

		$this->mailer->send($message);
	}
}
