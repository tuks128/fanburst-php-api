<?php

/**
 * @author: Martin Liprt
 * @email: tuxxx128@protonmail.com
 */

namespace WaProduction\Fanburst;

class FanburstResult implements \ArrayAccess
{

    public function __construct($arr)
    {
        foreach ($arr as $k => $v) {
            $this->$k = $v;
        }
    }

    public function toArray()
    {
        return (array) $this;
    }

    final public function offsetSet($nm, $val)
    {
        $this->$nm = $val;
    }

    final public function offsetGet($nm)
    {
        return $this->$nm;
    }

    final public function offsetExists($nm)
    {
        return isset($this->$nm);
    }

    final public function offsetUnset($nm)
    {
        unset($this->$nm);
    }
}
