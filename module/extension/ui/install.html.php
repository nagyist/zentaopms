<?php
declare(strict_types=1);
/**
 * The install view file of extension module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     extension
 * @link        https://www.zentao.net
 */
namespace zin;

modalHeader
(
    set::title($title),
    set::titleClass('font-bold mr-2 '),
    isset($license) && $upgrade == 'yes' ? to::suffix(sprintf($lang->extension->upgradeVersion, $this->post->installedVersion, $this->post->upgradeVersion)) : null,
);

$fileItems = array();
foreach($files as $fileName => $md5)
{
    $fileItems[] = li
    (
        $fileName
    );
}

!empty($error) ? div
(
    div
    (
        set::class('mb-2'),
        sprintf($lang->extension->installFailed, $installType)
    ),
    p
    (
        set::class('text-danger mb-3'),
        $error,
    ),
    btn
    (
        set::type('primary'),
        set('data-load', 'modal'),
        $lang->extension->refreshPage,
    )
) : null;

empty($error) && isset($license) ? div
(
    div
    (
        set::class('font-bold mb-2'),
        $lang->extension->license
    ),
    p
    (
        set::class('mb-2'),
        textarea
        (
            set::rows(15),
            set::name('license'),
            set::value($license),
        )
    ),
    btn
    (
        set::type('primary'),
        set('data-load', 'modal'),
        set('url', $agreeLink),
        $lang->extension->agreeLicense,
    )
) : null;

empty($error) && !isset($license) ? div
(
    div
    (
        set::class('mb-2'),
        sprintf($lang->extension->installFinished, $installType)
    ),
    btn
    (
        set::type('success'),
        set::url(createLink('extension', 'browse')),
        $lang->extension->viewInstalled,
    ),
    div
    (
        set::class('alert success-outline mt-3'),
        div
        (
            set::class('alert-content'),
            p 
            (
                $lang->extension->successDownloadedPackage,
            ),
            p 
            (
                $lang->extension->successCopiedFiles,
            ),
            ul
            (
                $fileItems
            ),
            p 
            (
                $lang->extension->successInstallDB,
            )
        )
    )
) : null;

render();
