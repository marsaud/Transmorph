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
     * A class name used to instanciate array nodes in the transformation
     * output.
     *
     * This attribute is automatically defined from $_objectNodeType if it
     * implements the ArrayAccess interface.
     *
     * @var string
     */
    protected $_arrayNodeType;

    /**
     * A class name used to instanciate object nodes in the transformation
     * output.
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
            throw new InvalidArgumentException(
                'resource type is not supported'
            );
        }

        if ($path === '')
        {
            // Root path.
            $node = $value;
        }
        else
        {
            $matches = array();
            $found = preg_match(
                '@
                ^
                (
                /[^./]*   # An array rule node.
                |
                \\.[^./]+ # An object rule node.
                )
                ((?1)*)   # Any number of following rule nodes.
                $
                @x', $path, $matches
            );

            if ($found !== 1)
            {
                throw new Transmorph_Writer_Exception(
                    'Illegal write-rule : ' . $path
                );
            }
            $pathNode = $matches[1];
            $pathNode = $this->_fireProcessRuleNode($pathNode);

            $remainingPath = isset($matches[2]) ? $matches[2] : '';

            if ($pathNode[0] == '/')
            {
                // Our node must be an array.
                if ($node === null)
                {
                    if (isset($this->_arrayNodeType))
                    {
                        $node = new $this->_arrayNodeType;
                    }
                    else
                    {
                        $node = array();
                    }
                }
                elseif (!is_array($node) && !($node instanceof ArrayAccess))
                {
                    throw new Transmorph_Writer_Exception(
                        'Incoherence: current node is neither an array nor an ArrayAccess object'
                    );
                }

                $key = substr($pathNode, 1);

                if ($key !== false)
                {
                    try
                    {
                        /*
                         * An exception may be raised in some conditions.  For
                         * instance, this might be a write-only array entry or it
                         * might not have a value yet.
                         */
                        $output = $node[$key];
                    }
                    catch (Exception $exc)
                    {
                        /**
                         * @todo A rejection log stream would be nice.
                         */
                    }
                }

                $this->feed($output, $remainingPath, $value);

                if ($key === false)
                {
                    // Incremental numeric array key
                    $node[] = $output;
                }
                else
                {
                    $node[$key] = $output;
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

                if (!is_object($node))
                {
                    throw new Transmorph_Writer_Exception(
                        'Incoherence: current node is not an object'
                    );
                }

                $property = substr($pathNode, 1);

                // Indirection to avoid problem with magic properties.
                try
                {
                    /*
                     * An  exception  may  be  raised in  some  conditions.  For
                     * instance, this might be a write-only property or it might
                     * not have a value yet.
                     *
                     * We do not provide error-handling further than the @.
                     * Users have to care about what they do.
                     */
                    @$propertyValue = $node->$property;
                }
                catch (Exception $exc)
                {
                    /**
                     * @todo A rejection log stream would be nice.
                     */
                    $propertyValue = null;
                }
                $this->feed($propertyValue, $remainingPath, $value);
                $node->$property = $propertyValue;
            }
        }

        $this->_firePost();

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
                throw new OutOfRangeException(
                    __CLASS__ . ' has no ' . $name . ' property-read.'
                );
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
                if (!isset($value))
                {
                    $this->_arrayNodeType = null;
                    $this->_objectNodeType = null;
                }
                else
                {
                    $class = new ReflectionClass($value);

                    $this->_arrayNodeType = $class->implementsInterface('ArrayAccess')
                        ? $value
                        : null;
                    $this->_objectNodeType = $value;
                }
                break;
            default:
                throw new OutOfRangeException(
                    __CLASS__ . ' has no ' . $name . ' property-write.'
                );
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
     * Plugin caller
     *
     * @return void
     */
    protected function _firePost()
    {
        foreach ($this->_pluginStack as $plugin)
        {
            /* @var $plugin Transmorph_Plugin_Writer_Interface */
            $plugin->post($this);
        }
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
            throw new Transmorph_Writer_Exception(
                'Unsupported plugin interface'
            );
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
            throw new Transmorph_Writer_Exception(
                'Unsupported plugin interface'
            );
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