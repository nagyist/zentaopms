<?php
declare(strict_types=1);
class zahostTest
{
    public function __construct()
    {
        global $tester;
        $this->objectModel = $tester->loadModel('zahost');
    }

    /**
     * 测试根据编号获取主机信息。
     * Get by id test.
     *
     * @param  int          $zahostID
     * @access public
     * @return array|object
     */
    public function getByIDTest(int $zahostID): array|object
    {
        $zahost = $this->objectModel->getByID($zahostID);
        if(dao::isError()) return dao::getError();
        return $zahost;
    }
}