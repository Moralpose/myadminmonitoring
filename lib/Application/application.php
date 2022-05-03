<?php

declare(strict_types=1);


namespace OCA\MyAdminMonitoring\AppInfo;

use OCA\MyAdminMonitoring\Listener\AppManagement;
use OCP\App\ManagerEvent;
use OCP\AppFramework\App;
use OCP\Log\Audit\CriticalActionPerformedEvent;
use OC\User\Session as UserSession;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use Psr\Log\LoggerInterface;
use OCP\Mail\IMailer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Application extends App implements IBootstrap {

	protected $logger;

	public function __construct() {
		parent::__construct('myadminmonitoring');
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(CriticalActionPerformedEvent::class, CriticalActionPerformedEventListener::class);
	}

	public function boot(IBootContext $context): void {
		$serverContainer = $context->getServerContainer();
		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher = $serverContainer->get(EventDispatcherInterface::class);
		$mailer = $serverContainer->get(IMailer::class);


	}
	private function appHooks(LoggerInterface $logger,
							  EventDispatcherInterface $eventDispatcher): void {
		$eventDispatcher->addListener(ManagerEvent::EVENT_APP_ENABLE, function (ManagerEvent $event) use ($logger) {
			$appActions = new AppManagement($logger);
			$appActions->enableApp($event->getAppID());
		});
		$eventDispatcher->addListener(ManagerEvent::EVENT_APP_ENABLE_FOR_GROUPS, function (ManagerEvent $event) use ($logger) {
			$appActions = new AppManagement($logger);
			$appActions->enableAppForGroups($event->getAppID(), $event->getGroups());
		});
		$eventDispatcher->addListener(ManagerEvent::EVENT_APP_DISABLE, function (ManagerEvent $event) use ($logger) {
			$appActions = new AppManagement($logger);
			$appActions->disableApp($event->getAppID());
		});
	}
}
