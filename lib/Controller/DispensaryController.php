<?php

declare(strict_types=1);

namespace OCA\AeneasDispensary\Controller;

use OCA\AeneasDispensary\AppInfo\Application;
use OCA\AeneasDispensary\Service\DispensaryService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\ApiRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\AdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\IGroupManager;

class DispensaryController extends Controller {

    public function __construct(
        IRequest $request,
        private IUserSession $userSession,
        private IGroupManager $groupManager,
        private DispensaryService $service
    ) {
        parent::__construct(Application::APP_ID, $request);
    }

    private function getUserId(): string {
        return $this->userSession->getUser()?->getUID() ?? '';
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    #[FrontpageRoute(verb: 'GET', url: '/')]
    public function index(): TemplateResponse {
        $userId = $this->getUserId();
        $stats = $this->service->getUserStats($userId);
        $isAdmin = $this->groupManager->isAdmin($userId);

        return new TemplateResponse(
            Application::APP_ID,
            'dispensary',
            [
                'userId' => $userId,
                'stats' => $stats,
                'isAdmin' => $isAdmin,
            ]
        );
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    #[ApiRoute(verb: 'POST', url: '/dispensary/add')]
    public function addAmount(int $amount): DataResponse {
        $userId = $this->getUserId();
        $result = $this->service->addAbgabe($userId, $amount);
        return new DataResponse($result, $result['status']);
    }

    #[AdminRequired]
    #[NoCSRFRequired]
    #[FrontpageRoute(verb: 'GET', url: '/admin')]
    public function adminIndex(): TemplateResponse {
        $list = $this->service->getAdminList();
        return new TemplateResponse(
            Application::APP_ID,
            'admin',
            ['entries' => $list]
        );
    }

    #[AdminRequired]
    #[NoCSRFRequired]
    #[ApiRoute(verb: 'POST', url: '/admin/update')]
    public function updateAbgabe(int $id, int $amount): DataResponse {
        $adminId = $this->getUserId();
        $result = $this->service->adminUpdateAbgabe($id, $amount, $adminId);
        return new DataResponse($result);
    }
}
