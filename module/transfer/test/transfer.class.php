<?php
declare(strict_types=1);
/**
 * The zen file of transfer module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tang Hucheng<tanghucheng@easycorp.ltd>
 * @package     transfer
 * @link        https://www.zentao.net
 */

class transferTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('transfer');
    }

    /**
     * 测试通过 id 获取用例库信息。
     * Get by ID test.
     *
     * @param  bool               $setImgSize
     * @access public
     * @return void
     */
    public function commonActionsTest(string $module)
    {
        $this->objectModel->commonActions($module);
    }
}
