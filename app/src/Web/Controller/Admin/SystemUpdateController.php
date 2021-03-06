<?php
declare(strict_types=1);

namespace Fc2blog\Web\Controller\Admin;

use Fc2blog\Model\SystemUpdateModel;
use Fc2blog\Web\Html;
use Fc2blog\Web\Request;
use LogicException;

class SystemUpdateController extends AdminController
{
    public function index(Request $request): string
    {
        // TODO unit test
        if (!$request->isGet()) return $this->error400();

        $release_list = SystemUpdateModel::getReleaseInfo();
        $this->set('release_list', $release_list);
        $this->set('repo_site_url', SystemUpdateModel::getReleasesUrl());
        $this->set('now_version', SystemUpdateModel::getVersion(true));
        return 'admin/system_update/index.twig';
    }

    public function update(Request $request): string
    {
        // TODO unit test
        if (!$request->isPost()) return $this->error400();

        if (!$request->isValidSig()) {
            $this->setWarnMessage(__("Request failed: invalid sig, please retry."));
            $this->redirect($request, Html::url($request, ['controller' => 'system_update', 'action' => 'index']));
        }

        // check request
        $request_version = $request->get('version');
        if (is_null($request_version)) {
            $this->setWarnMessage(__("Request failed: notfound request version."));
            $this->redirect($request, Html::url($request, ['controller' => 'system_update', 'action' => 'index']));
        }

        $release_list = SystemUpdateModel::getReleaseInfo();
        // get request version
        $release = SystemUpdateModel::findByVersionFromReleaseList($release_list, $request_version);
        $zip_url = SystemUpdateModel::getZipDownloadUrl($release);

        // do update system.
        SystemUpdateModel::updateSystemByUrl($zip_url);

        // set flash message
        $this->setInfoMessage(__("Update success."));

        // redirect to index
        $this->redirect($request, Html::url($request, ['controller' => 'system_update', 'action' => 'index']));

        throw new LogicException("must be redirect");
    }
}
