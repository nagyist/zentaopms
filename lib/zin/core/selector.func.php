<?php
declare(strict_types=1);
/**
 * The selector helpers file of zin of ZenTaoPMS.
 *
 * @copyright   Copyright 2023 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     zin
 * @version     $Id
 * @link        https://www.zentao.net
 */

namespace zin;

/**
 * Parse wg selector.
 *
 * @param string|object $selector
 * @param bool          $checkParents
 * @return object|null
 */
function parseSelector(string|object $selector, bool $checkParents = false): ?object
{
    if(is_object($selector)) return $selector;

    $selector = trim($selector);
    $len      = strlen($selector);

    if($len < 1) return null;

    $result = array(
        'class'    => array(),
        'id'       => null,
        'tag'      => null,
        'inner'    => false,
        'name'     => null,
        'first'    => false,
        'selector' => $selector,
        'options'  => array(),
        'parents'  => array()
    );
    if(str_contains($selector, '/'))
    {
        $parts          = explode('/', $selector, 2);
        $result['name'] = $parts[0];
        $selector       = $parts[1];
        $len            = strlen($selector);
    }
    $selector = str_replace('> *', '>*', $selector);
    if(substr($selector, strlen($selector) - 2) == '>*')
    {
        $result['inner'] = true;
        $selector = substr($selector, 0, strlen($selector) - 2);
        $len      = strlen($selector);
    }

    if($checkParents && str_contains($selector, ' '))
    {
        $parts    = explode(' ', $selector);
        $selector = array_pop($parts);
        $len      = strlen($selector);
        foreach($parts as $part) $result['parents'][] = parseSelector($part);
    }

    $type         = 'tag';
    $current      = '';
    $updateResult = function(&$result, $current, $type)
    {
        if(empty($current)) return;

        if($type === 'class')
        {
            $result[$type][]  = $current;
        }
        elseif($type === 'option')
        {
            $options = array();
            parse_str($current, $options);
            foreach($options as $key => $value) $result['options'][$key] = empty($value) ? true : $value;
        }
        else
        {
            $result[$type] = $current;
        }
    };

    for($i = 0; $i < $len; $i++)
    {
        $c = $selector[$i];
        $t = '';

        if($c === '#' & $type !== 'option')
        {
            $t = 'id';
        }
        elseif($c === '.' & $type !== 'option')
        {
            $t = 'class';
        }
        elseif($c === '(' && $type !== 'option' && str_ends_with($selector, ')'))
        {
            $command = substr($selector, $i + 1, -1);
            if(empty($command)) $command = $current;
            $result['command'] = $command;
            break;
        }
        elseif($c === ':')
        {
            $t = 'option';
        }

        if(empty($t))
        {
            $current .= $c;
        }
        else
        {
            $updateResult($result, $current, $type);
            $current = '';
            $type    = $t;
        }
    }
    $updateResult($result, $current, $type);

    if(empty($result['class'])) $result['class'] = null;
    if(empty($result['name']))
    {
        if(!empty($result['id']))      $result['name'] = $result['id'];
        elseif(!empty($result['tag'])) $result['name'] = $result['tag'];
        else                           $result['name'] = $selector;
    }

    return (object)$result;
}

/**
 * Parse wg selectors.
 *
 * @param object|string|object[]|string[] $selectors
 * @param bool                            $checkParents
 * @return object[]
 */
function parseSelectors(object|string|array $selectors, bool $checkParents = false): array
{
    if(is_object($selectors)) return array($selectors);
    if(is_string($selectors)) $selectors = explode(',', trim($selectors));
    $results = array();
    foreach($selectors as $selector)
    {
        $selector = parseSelector($selector, $checkParents);
        if(is_object($selector)) $results[] = $selector;
    }
    return $results;
}

/**
 * Stringify wg selectors.
 * @param object|object[] $selector
 * @return string
 */
function stringifySelectors(array|object|null $selector): string
{
    if(empty($selector)) return '';
    if(is_array($selector))
    {
        $result = array();
        foreach($selector as $s) $result[] = stringifySelectors($s);
        return implode(',', $result);
    }

    $result = '';
    if(!empty($selector->name) && $selector->name !== $selector->selector && $selector->name !== $selector->tag) $result .= $selector->name . '/';
    if(!empty($selector->tag))   $result .= $selector->tag;
    if(!empty($selector->id))    $result .= '#' . $selector->id;
    if(!empty($selector->class)) $result .= '.' . implode('.', $selector->class);
    if(!empty($selector->first)) $result .= ':first';
    if($selector->inner)         $result .= '>*';
    if(!empty($selector->options))
    {
        $options = array();
        foreach($selector->options as $key => $value) $options[] = $value === true ? $key : ($key . '=' . $value);
        $result .= ':' . implode('&', $options);
    }
    return $result;
}
