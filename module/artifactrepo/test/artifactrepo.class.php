<?php
class artifactrepoTest
{
    public function __construct(string $account = 'admin')
    {
        su($account);

        global $tester, $app;
        $this->objectModel = $tester->loadModel('artifactrepo');

        $app->rawModule = 'artifactrepo';
        $app->rawMethod = 'browse';
        $app->setModuleName('artifactrepo');
        $app->setMethodName('browse');
    }

    /**
     * 获取制品库列表。
     * Get artifactrepo repo list.
     *
     * @param  string $orderBy
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return array
     */
    public function getListTest(string $orderBy, int $recPerPage, int $pageID): array
    {
        $this->objectModel->app->loadClass('pager', true);
        $pager = new pager($recPerPage, $pageID);

        $artifactrepoList = $this->objectModel->getList($orderBy, $pager);

        if(dao::isError()) return dao::getError();
        return $artifactrepoList;
    }

    /**
     * 创建一个制品库。
     * Create a artifact repo.
     *
     * @param  array        $data
     * @access public
     * @return array|object
     */
    public function createTest(array $data): array|object
    {
        $artifactRepo = new stdClass();

        foreach($data as $key => $value) $artifactRepo->{$key} = $value;
        $artifactRepoID = $this->objectModel->create($artifactRepo);

        if(dao::isError()) return dao::getError();
        return $this->objectModel->dao->select('*')->from(TABLE_ARTIFACTREPO)->where('id')->eq($artifactRepoID)->fetch();
    }
}