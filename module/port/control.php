<?php
class port extends control
{
    /**
     * Export datas.
     *
     * @param  string $model
     * @param  string $params
     * @param  array  $rows
     * @access public
     * @return void
     */
    public function export($model = '')
    {
        if($_POST)
        {
            $this->port->export($model);
            $this->fetch('file', 'export2' . $_POST['fileType'], $_POST);
        }
    }

    /**
     * Export Template.
     *
     * @access public
     * @return void
     */
    public function exportTemplate($model, $params = '')
    {
        if($_POST)
        {
            $this->loadModel($model);
            if($this->config->edition != 'open')
            {
                $appendFields = $this->dao->select('t2.*')->from(TABLE_WORKFLOWLAYOUT)->alias('t1')
                    ->leftJoin(TABLE_WORKFLOWFIELD)->alias('t2')->on('t1.field=t2.field && t1.module=t2.module')
                    ->where('t1.module')->eq($model)
                    ->andWhere('t1.action')->eq('exporttemplate')
                    ->andWhere('t2.buildin')->eq(0)
                    ->orderBy('t1.order')
                    ->fetchAll();

                foreach($appendFields as $appendField)
                {
                    $this->lang->$model->{$appendField->field} = $appendField->name;
                    $this->config->$model->templateFields .= ',' . $appendField->field;
                }
            }

            if($params)
            {
                /* Split parameters into variables (executionID=1,status=open).*/
                $params = explode(',', $params);
                foreach($params as $key => $param)
                {
                    $param = explode('=', $param);
                    $params[$param[0]] = $param[1];
                    unset($params[$key]);
                }
                extract($params);

                /* save params to session. */
                $this->session->set(($model.'PortParams'), $params);
            }

            $this->loadModel($model);
            $this->config->port->sysDataList = $this->port->initSysDataFields();

            $fields = $this->config->$model->templateFields;

            /* Init config fieldList */
            $fieldList = $this->port->initFieldList($model, $fields);

            $list = $this->port->setListValue($model, $fieldList);
            if($list) foreach($list as $listName => $listValue) $this->post->set($listName, $listValue);

            $fields = $this->port->getExportDatas($fieldList);

            $this->post->set('fields', $fields['fields']);
            $this->post->set('kind', isset($_POST['kind']) ? $_POST['kind'] : $model);
            $this->post->set('rows', array());
            $this->post->set('extraNum', $this->post->num);
            $this->post->set('fileName', isset($_POST['fileName']) ? $_POST['fileName'] : $model . 'Template');

            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }

    /**
     * Import.
     *
     * @access public
     * @return void
     */
    public function import($model, $locate = '')
    {
        $locate = $locate ? $locate : $this->session->showImportURL;
        if($_FILES)
        {
            $file      = $this->loadModel('file')->getUpload('file');
            $file      = $file[0];
            $shortName = $this->file->getSaveName($file['pathname']);
            if(empty($shortName))
            {
                die(js::alert($this->lang->excel->emptyFileName));
            }
            $extension = $file['extension'];

            $fileName = $this->file->savePath . $shortName;

            move_uploaded_file($file['tmpname'], $fileName);

            if($extension == 'xlsx' or $extension == 'xls') $this->port->cutFile($fileName);

            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007();
            if(!$phpReader->canRead($fileName))
            {
                $phpReader = new PHPExcel_Reader_Excel5();
                if(!$phpReader->canRead($fileName))die(js::alert($this->lang->excel->canNotRead));
            }
            $this->session->set('fileImportFileName', $fileName);
            $this->session->set('fileImportExtension', $extension);

            die(js::locate($locate, 'parent.parent'));
        }

        $this->view->title = $this->lang->port->importCase;
        $this->display();
    }

    /**
     * Ajax get Tbody .
     *
     * @param  string $model
     * @param  int    $pagerID
     * @access public
     * @return void
     */
    public function ajaxGetTbody($model = '', $lastID = 0, $pagerID = 1)
    {
        $this->loadModel($model);
        $importFields = $this->config->$model->templateFields;
        $fields       = $this->port->initFieldList($model, $importFields, false);
        $formatDatas  = $this->port->format($model);
        $datas        = $this->port->getPageDatas($formatDatas, $pagerID);

        $html = $this->port->buildNextList($datas->datas, $lastID, $fields, $pagerID);
        die($html);
    }

    /**
     * Ajax Get Options.
     *
     * @param  string $model
     * @param  string $field
     * @param  string $value
     * @param  string $id
     * @access public
     * @return void
     */
    public function ajaxGetOptions($model = '', $field = '', $value = '', $index = '')
    {
        $this->loadModel($model);
        $fields = $this->config->$model->templateFields;

        if($this->config->edition != 'open')
        {
            $appendFields = $this->loadModel('workflowaction')->getFields($model, 'showimport', false);

            foreach($appendFields as $appendField) $fields .= ',' . $appendField->field;
        }

        $fieldList = $this->port->initFieldList($model, $fields, false);

        if(empty($fieldList[$field]['values'])) $fieldList[$field]['values'] = array();
        if(!isset($fieldList[$field]['values'][''])) $fieldList[$field]['values'][''] = '';
        $multiple = $fieldList[$field]['control'] == 'multiple' ? 'multiple' : '';

        return print(html::select($field . "[$index]", $fieldList[$field]['values'], $value, "class='form-control picker-select' $multiple"));
    }
}
