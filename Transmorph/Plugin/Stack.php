<?php

/**
 * This file is part of Transmorph.
 *
 * Transmorph is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Transmorph is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Transmorph. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @author Fabrice Marsaud <marsaud.fabrice@neuf.fr>
 * 
 * @package Plugin
 */

/**
 * Description of Transmorph_Plugin_Stack
 *
 * @package Plugin
 */
class Transmorph_Plugin_Stack implements Transmorph_Plugin_StackInterface, Iterator
{

    /**
     *
     * @var Transmorph_Plugin_Interface[] 
     */
    protected $_plugins;

    /**
     * Initializations.
     */
    public function __construct()
    {
        $this->_plugins = array();
    }

    /**
     * Appending a plugin to the stack.
     *
     * @param Transmorph_Plugin_Interface $newPlugin A plugin.
     * 
     * @return void
     * 
     * @see Transmorph_Plugin_StackInterface::appendPlugin()
     */
    public function appendPlugin(Transmorph_Plugin_Interface $newPlugin)
    {
        foreach ($this->_plugins as $plugin)
        {
            if (get_class($plugin) == get_class($newPlugin))
            {
                throw new Transmorph_Exception('Plugin ' . get_class($newPlugin) . ' already registered');
            }
        }
        $this->_plugins[] = $newPlugin;
    }

    /**
     * Prepending a plugin to the stack. 
     *
     * @param Transmorph_Plugin_Interface $newPlugin A plugin.
     * 
     * @return void
     * 
     * @see Transmorph_Plugin_StackInterface::prependPlugin()
     */
    public function prependPlugin(Transmorph_Plugin_Interface $newPlugin)
    {
        foreach ($this->_plugins as $p)
        {
            if (get_class($p) == get_class($newPlugin))
            {
                throw new Transmorph_Exception('Plugin ' . get_class($newPlugin) . ' already registered');
            }
        }
        array_unshift($this->_plugins, $newPlugin);
    }

    /**
     * Removing a plugin from the stack.
     *
     * @param type $pluginClassName A plgin class name.
     * 
     * @return void
     * 
     * @see Transmorph_Plugin_StackInterface::removePlugin()
     */
    public function removePlugin($pluginClassName)
    {
        $removeKey = null;
        foreach ($this->_plugins as $key => $value)
        {
            if (get_class($value) === $pluginClassName)
            {
                $removeKey = $key;
                break;
            }
        }

        if ($removeKey === null)
        {
            throw new Transmorph_Exception('Plugin ' . $pluginClassName . ' not found for removal.');
        }

        unset($this->_plugins[$removeKey]);
    }

    /**
     * Trivial.
     *
     * @return Transmorph_Plugin_Interface Current element from the stack.
     */
    public function current()
    {
        return current($this->_plugins);
    }

    /**
     * Trivial.
     *
     * @return integer Key of the current stack element.
     */
    public function key()
    {
        return key($this->_plugins);
    }

    /**
     * Trivial.
     *
     * @return Transmorph_Plugin_Interface The next element from the stack.
     */
    public function next()
    {
        return next($this->_plugins);
    }

    /**
     * Trivial.
     * 
     * @return void
     */
    public function rewind()
    {
        reset($this->_plugins);
    }

    /**
     * Trivial.
     *
     * @return boolean 
     */
    public function valid()
    {
        /**
         * This is OK as encapsulation prevents the array of containing 
         * boolean values.
         */
        return current($this->_plugins) !== false;
    }

}
