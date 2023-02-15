<?php

namespace zin;

class column extends wg
{
    static $defineProps = 'justify,align';

    public function onAddChild($child)
    {
        if($child instanceof wg) {
            $this->addToBlock('inner', $child);
            return false;
        }
        return null;
    }

    protected function build()
    {
        $justify = empty($this->prop('justify')) ? 'start' : $this->prop('justify');
        $align = empty($this->prop('align')) ? 'start' : $this->prop('align');
        return div(
            setClass("col justify-$justify items-$align"),
            $this->block('inner'),
        );
    }
}
