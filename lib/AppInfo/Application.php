<?php

declare(strict_types=1);

namespace OCA\AeneasDispensary\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\INavigationManager;
use OCP\IURLGenerator;

class Application extends App implements IBootstrap {

    public const APP_ID = 'aeneas_dispensary';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        $context->registerNavigation(function(IURLGenerator $url, INavigationManager $nav) {
            $nav->add([
                'id' => self::APP_ID,
                'order' => 10,
                'href' => $url->linkToRoute(self::APP_ID . '.dispensary.index'),
                'icon' => $url->imagePath(self::APP_ID, 'app.svg'),
                'name' => 'Dispensary'
            ]);
        });
    }

    public function boot(IBootContext $context): void {
    }
}
