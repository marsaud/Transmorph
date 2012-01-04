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
 * @package Core
 */

/**
 * Handling of Transmorph write rules.
 * 
 * @package Core
 * 
 * @property string $objectNodeType The type to instanciate when creating object
 *  nodes.
 */
class Transmorph_Writer implements Transmorph_Plugin_StackInterface
{

    /**
     * A plugin stack.
     *
     * @var Transmorph_Plugin_StackInterface
     */
    protected $_pluginStack;

    /**
     * A class name used to instanciate object nodes in the transformation 
     * output. "stdClass" by default.
     *
     * @var string
     */
    protected $_objectNodeType;

    /**
     * Initializations.
     */
    public function __construct()
    {
        $this->_objectNodeType = 'stdClass';
        $this->_pluginStack = new Transmorph_Plugin_Stack();
    }

    /**
     * Output building.
     * 
     * This method resolves a write rule to push data in a variable, creating
     * structure nodes in this variable if necessary.
     *
     * @param mixed &$node The variable to write in.
     * @param string $path The write rule.
     * @param mixed $value The value to push in $node.
     * 
     * @return mixed The modified $node.
     * 
     * @throws InvalidArgumentException If $value is a resource.
     */
    public function feed(&$node, $path, $value)
    {
        if (gettype($value) === 'resource')
        {
            throw new InvalidArgumentException('resource type is not supported');
        }

        if ($path === '' || $path == '/' || $path == '.')
        {
            // Simple type. No structure.
            $node = $value;
        }
        else
        {
            $matches = array();
            $found = preg_match('#^((/[^\./\\\]+)|(\.[^\./\\\]+))((\.|/).*)*$#', $path, $matches);
            if ($found !== 1)
            {
                throw new Transmorph_Writer_Exception('Illegal write-rule : ' . $path);
            }
            $pathNode = $matches[1];
            $pathNode = $this->_fireProcessRuleNode($pathNode);

            $remainingPath = isset($matches[4]) ? $matches[4] : '';

            if ($pathNode[0] == '/')
            {
                // Our node must be an array.
                if ($node === null)
                {
                    $node = array();
                }

                if (!is_array($node))
                {
                    throw new Transmorph_Writer_Exception('Incoherence beetween write-rule and output node type');
                }

                $key = substr($pathNode, 1);
                if (!isset($node[$key]))
                {
                    if (!isset($remainingPath[0]))
                    {
                        $node[$key] = $value;
                    }
                    else
                    {
                        $nextNode = null;
                        $node[$key] = $this->feed($nextNode, $remainingPath, $value);
                    }
                }
                else
                {
                    $nextNode = $node[$key];
                    $node[$key] = $this->feed($nextNode, $remainingPath, $value);
                }
            }
            elseif ($pathNode[0] == '.')
            {
                // Our node must be an object.
                if ($node === null)
                {
                    $class = $this->_objectNodeType;
                    $node = new $class();
                }

                if (!(is_object($node) && ($node instanceof $this->_objectNodeType)))
                {
                    throw new Transmorph_Writer_Exception('Incoherence beetween write-rule and output node type');
                }

                $key = substr($pathNode, 1);
                if (!isset($node->$key))
                {
                    if (!isset($remainingPath[0]))
                    {
                        $node->$key = $value;
                    }
                    else
                    {
                        $nextNode = null;
                        $node->$key = $this->feed($nextNode, $remainingPath, $value);
                    }
                }
                else
                {
                    $nextNode = $node->$key;
                    $node->$key = $this->feed($nextNode, $remainingPath, $value);
                }
            }
        }

        return $node;
    }

    /**
     * Property handling.
     *
     * @param string $name Property name.
     * 
     * @return mixed Property value.
     */
    public function __get($name)
    {
        switch ($name)
        {
            case 'objectNodeType':
                return $this->_objectNodeType;
                break;
            default:
                throw new OutOfRangeException(__CLASS__ . ' has no ' . $name . ' property-read.');
                break;
        }
    }

    /**
     * Property handling.
     * 
     * @param string $name Property name.
     * @param mixed $value Property value.
     * 
     * @return void
     */
    public function __set($name, $value)
    {
        switch ($name)
        {
            case 'objectNodeType':
                $this->_objectNodeType = $value;
                break;
            default:
                throw new OutOfRangeException(__CLASS__ . ' has no ' . $name . ' property-write.');
                break;
        }
    }

    /**
     * Plugin caller.
     * 
     * @param string $ruleNode passed to plugin.
     * 
     * @return string back from plugin.
     *
     * @see Transmorph_Plugin_Writer_Interface::processRuleNode()
     */
    protected function _fireProcessRuleNode($ruleNode)
    {
        foreach ($this->_pluginStack as $plugin)
        {
            /* @var $plugin Transmorph_Plugin_Writer_Interface */
            $ruleNode = $plugin->processRuleNode($this, $ruleNode);
        }

        return $ruleNode;
    }

    /**
     * Append a plugin to the stack.
     *
     * @param Transmorph_Plugin_Interface $plugin A plugin to append.
     * 
     * @return void
     * 
     * @see Transmorph_Plugin_StackInterface::appendPlugin()
     */
    public function appendPlugin(Transmorph_Plugin_Interface $plugin)
    {
        if ($plugin instanceof Transmorph_Plugin_Writer_Interface)
        {
            $this->_pluginStack->appendPlugin($plugin);
        }
        else
        {
            throw new Transmorph_Writer_Exception('Unsupported plugin interface');
        }
    }

    /**
     * Prepend a plugin to the stack.
     *
     * @param Transmorph_Plugin_Interface $plugin A plugin to prepend.
     * 
     * @return void
     * 
     * @see Transmorph_Plugin_StackInterface::prependPlugin()
     */
    public function prependPlugin(Transmorph_Plugin_Interface $plugin)
    {
        if ($plugin instanceof Transmorph_Plugin_Writer_Interface)
        {
            $this->_pluginStack->prependPlugin($plugin);
        }
        else
        {
            throw new Transmorph_Writer_Exception('Unsupported plugin interface');
        }
    }

    /**
     * Remove a plugin from the stack.
     *
     * @param string $pluginClassName The class name of the plugin to remove.
     * 
     * @return void
     * 
     * @see Transmorph_Plugin_StackInterface::removePlugin()
     */
    public function removePlugin($pluginClassName)
    {
        $this->_pluginStack->removePlugin($pluginClassName);
    }

}