<?php

declare(strict_types=1);

namespace OCA\AeneasDispensary\Controller;

use OCA\AeneasDispensary\AppInfo\Application;
use OCA\AeneasDispensary\Service\DispensaryService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\IGroupManager;

class DispensaryController extends Controller {

    public function __construct(
        string $appName,
        IRequest $request,
        private IUserSession $userSession,
        private IGroupManager $groupManager,
        private DispensaryService $service
    ) {
        parent::__construct($appName, $request);
    }

    private function getUserId(): string {
        $user = $this->userSession->getUser();
        return $user ? $user->getUID() : '';
    }

    /**
     * User view
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): TemplateResponse {
        $userId = $this->getUserId();
        $stats = $this->service->getUserStats($userId);
        $isAdmin = $userId !== '' && $this->groupManager->isAdmin($userId);

        return new TemplateResponse(
            Application::APP_ID,
            'dispensary',
            [
                'userId'  => $userId,
                'stats'   => $stats,
                'isAdmin' => $isAdmin,
            ]
        );
    }

    /**
     * Add Abgabe for current user
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function addAmount(int $amount): DataResponse {
        $userId = $this->getUserId();
        $result = $this->service->addAbgabe($userId, $amount);
        return new DataResponse($result, $result['status']);
    }

    /**
     * Admin overview
     * @AdminRequired
     * @NoCSRFRequired
     */
    public function adminIndex(): TemplateResponse {
        $list = $this->service->getAdminList();
        return new TemplateResponse(
            Application::APP_ID,
            'admin',
            [
                'entries' => $list,
            ]
        );
    }

    /**
     * Admin update
     * @AdminRequired
     * @NoCSRFRequired
     */
    public function updateAbgabe(int $id, int $amount): DataResponse {
        $adminId = $this->getUserId();
        $result = $this->service->adminUpdateAbgabe($id, $amount, $adminId);
        return new DataResponse($result);
    }
}