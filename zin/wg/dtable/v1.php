<?php
namespace zin\wg;

require_once dirname(dirname(__DIR__)) . DS . 'core' . DS . 'wg.class.php';
require_once dirname(dirname(__DIR__)) . DS . 'core' . DS . 'h5.class.php';
require_once dirname(__DIR__) . DS . 'icon' . DS . 'v1.php';

use \zin\core\h5;

class dtable extends \zin\core\wg
{
    static $tag = 'div';

    static $defaultProps = array('id' => '$GID');

    static $customProps = 'data,height,plugins,responsive';

    /**
     * @return builder
     */
    protected function build($isPrint = false, $parent = NULL)
    {
        $builder = parent::build($isPrint, $parent);

        $jsRender = $this->prop('js-render');
        if ($jsRender)
        {
            $id = $this->prop('id');
            $this->props->remove('id');
            $builder->jsVar('options', $this->props->data);
            $builder->js("domReady(() => {const dtable = new zui.DTable('#$id', options);});");
        }
        return $builder;
    }
}
