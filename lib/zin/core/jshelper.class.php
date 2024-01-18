<?php
declare(strict_types=1);
/**
 * The js helper class file of zin lib.
 *
 * @copyright   Copyright 2024 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     zin
 * @version     $Id
 * @link        https://www.zentao.net
 */

namespace zin;

require_once __DIR__ . DS . 'js.class.php';

/**
 * Class for generating js code.
 * 用于生成 js 代码的助手类，可以方便的操作 DOM 元素。
 */
class jsHelper extends js
{
    /**
     * Hide the specified elements.
     * 隐藏指定的元素。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @return self
     */
    public function toggleHide(string $selector): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').hide();");
    }

    /**
     * Show the specified elements.
     * 显示指定的元素。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @return self
     */
    public function toggleShow(string $selector): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').show();");
    }

    /**
     * Toggle the specified elements.
     * 切换显示或隐藏指定的元素。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $toggle   Whether to show or hide the elements.
     * @return self
     */
    public function toggle(string $selector, string $toggle): self
    {
        $selector = str_replace('\'', "\'", $selector);
        if(!is_string($toggle)) $toggle = json_encode($toggle);
        return $this->appendLine("$('{$selector}').toggle($toggle);");
    }

    /**
     * Add class to the specified elements.
     * 给指定的元素添加 class。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $class    The class to add.
     * @return self
     */
    public function addClass(string $selector, string $class): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').addClass(\"{$class}\");");
    }

    /**
     * Remove class from the specified elements.
     * 从指定的元素中移除 class。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $class    The class to remove.
     * @return self
     */
    public function removeClass(string $selector, string $class): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').removeClass(\"{$class}\");");
    }

    /**
     * Toggle class of the specified elements.
     * 切换指定的元素的 class。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $class    The class to toggle.
     * @param string $toggle   Whether to add or remove the class.
     * @return self
     */
    public function toggleClass(string $selector, string $class, string $toggle): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').removeClass(\"{$class}\", $toggle);");
    }

    /**
     * Set the html of the specified elements.
     * 设置指定元素的 html。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $html     The html to set.
     * @return self
     */
    public function setHtml(string $selector, string $html): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').html(", json_encode($html), ");");
    }

    /**
     * Set the text of the specified elements.
     * 设置指定元素的 text。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $text     The text to set.
     * @return self
     */
    public function setText(string $selector, string $text): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').text(", json_encode($text), ");");
    }

    /**
     * Set the value of the specified elements.
     * 设置指定元素的 value。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $value    The value to set.
     * @return self
     */
    public function setVal(string $selector, string $value): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').val(", json_encode($value), ");");
    }

    /**
     * Set the css of the specified elements.
     * 设置指定元素的 css。
     *
     * @access public
     * @param string $selector        The selector of the elements.
     * @param string $nameOrStyle     The name of the css or the css style object.
     * @param string $value           The value of the css.
     * @return self
     */
    public function setCss(string $selector, string|array $nameOrStyle, ?string $value = null): self
    {
        $selector = str_replace('\'', "\'", $selector);
        if(is_array($nameOrStyle)) return $this->appendLine("$('{$selector}').css(", json_encode($nameOrStyle), ");");
        return $this->appendLine("$('{$selector}').css(\"{$nameOrStyle}\", ", json_encode($value), ");");
    }

    /**
     * Set the attr of the specified elements.
     * 设置指定元素的 attr。
     *
     * @access public
     * @param string $selector        The selector of the elements.
     * @param string $nameOrAttrs     The name of the attr or the attr object.
     * @param string $value           The value of the attr.
     * @return self
     */
    public function setAttr(string $selector, string|array $nameOrAttrs, ?string $value = null): self
    {
        $selector = str_replace('\'', "\'", $selector);
        if(is_array($nameOrAttrs)) return $this->appendLine("$('{$selector}').attr(", json_encode($nameOrAttrs), ");");
        return $this->appendLine("$('{$selector}').attr(\"{$nameOrAttrs}\", ", json_encode($value), ");");
    }

    /**
     * Remove the attr of the specified elements.
     * 移除指定元素的 attr。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $name     The name of the attr.
     * @return self
     */
    public function removeAttr(string $selector, string $name): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').removeAttr(\"{$name}\");");
    }

    /**
     * Set the prop of the specified elements.
     * 设置指定元素的 prop。
     *
     * @access public
     * @param string $selector        The selector of the elements.
     * @param string $nameOrProps     The name of the prop or the prop object.
     * @param string $value           The value of the prop.
     * @return self
     */
    public function setProp(string $selector, string|array $nameOrProps, ?string $value = null): self
    {
        $selector = str_replace('\'', "\'", $selector);
        if(is_array($nameOrProps)) return $this->appendLine("$('{$selector}').prop(", json_encode($nameOrProps), ");");
        return $this->appendLine("$('{$selector}').prop(\"{$nameOrProps}\", ", json_encode($value), ");");
    }

    /**
     * Remove the prop of the specified elements.
     * 移除指定元素的 prop。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $name     The name of the prop.
     * @return self
     */
    public function removeProp(string $selector, string $name): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').removeProp(\"{$name}\");");
    }

    /**
     * Trigger the specified event of the specified elements.
     * 触发指定元素的指定事件。
     *
     * @access public
     * @param string $selector The selector of the elements.
     * @param string $event    The event to trigger.
     * @param string $data     The data to pass to the event handler.
     * @return self
     */
    public function triggerEvent(string $selector, string $event, string $data = ''): self
    {
        $selector = str_replace('\'', "\'", $selector);
        return $this->appendLine("$('{$selector}').trigger(\"{$event}\", ", json_encode($data), ");");
    }

    /**
     * Bind the specified event to the specified elements.
     * 给指定元素绑定指定事件。
     *
     * @access public
     * @param string $selector          The selector of the elements.
     * @param string $event             The event to bind.
     * @param string $targetsOrHandler  The targets or handler to bind.
     * @param string $handler           The handler to bind.
     * @return self
     */
    public function onEvent(string $selector, string $event, string $targetsOrHandler, ?string $handler = null): self
    {
        if(is_null($handler))
        {
            $targets = null;
            $handler = $targetsOrHandler;
        }
        else
        {
            $targets = $targetsOrHandler;
        }
        $selector = str_replace('\'', "\'", $selector);

        if(is_null($targets))
        {
            return $this->appendLine("$('{$selector}').on(\"{$event}\", {$handler});");
        }
        $targets = str_replace('"', '\\"', $targets);
        return $this->appendLine("$('{$selector}').on(\"{$event}\", \"{$targets}\", {$handler});");
    }

    /**
     * Unbind the specified event from the specified elements.
     * 给指定元素解绑指定事件。
     *
     * @access public
     * @param string $selector          The selector of the elements.
     * @param string $event             The event to unbind.
     * @param string $targetsOrHandler  The targets or handler to unbind.
     * @param string $handler           The handler to unbind.
     * @return self
     */
    public function offEvent(string $selector, string $event, string $targetsOrHandler, ?string $handler = null): self
    {
        if(is_null($handler))
        {
            $targets = null;
            $handler = $targetsOrHandler;
        }
        else
        {
            $targets = $targetsOrHandler;
        }
        $selector = str_replace('\'', "\'", $selector);

        if(is_null($targets))
        {
            return $this->appendLine("$('{$selector}').off(\"{$event}\", {$handler});");
        }
        $targets = str_replace('"', '\\"', $targets);
        return $this->appendLine("$('{$selector}').off(\"{$event}\", \"{$targets}\", {$handler});");
    }
}

function jsHelper(string|array|js|null ...$codes): jsHelper
{
    return new jsHelper(...$codes);
}