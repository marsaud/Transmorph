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
 * @package Transmorph
 * 
 * @subpackage Writer
 * 
 */

/**
 * Description of TransmorphWriter
 * 
 * This class handles Transmorph write rules.
 * 
 * @package Transmorph
 * 
 * @subpackage Writer
 * 
 * @property string $objectNodeType The type to instanciate when creating object nodes.
 */
class Transmorph_Writer
{

    /**
     * We can inject a Transmorph_Processor here for the writer to acces the plugin
     * stack.
     * 
     * @todo TASK I don't like this processor/writer coupling for plugin access.
     * Time will bring me an idea...-> IDEA -> Writer (& Reader) should have
     * its own Plugin Stack implemented its own plugin interface, so it will be
     * stand alone. Used by Processor, this one can feed the plugn stack of its
     * reader & writer.
     *
     * @var Transmorph_Processor
     */
    protected $_t;

    /**
     * A class name used to instanciate object nodes in the transformation output.
     *
     * @var string
     */
    protected $_objectNodeType;

    /**
     * The $transmorph parameter can provide a plugin stack fir the writer. Most
     * of the time this $transmorph will be a caller of the writer.
     *
     * @param mixed $transmorph 
     */
    public function __construct($transmorph = null)
    {
        if ($transmorph !== null)
        {
            $this->_setTransmorph($transmorph);
        }

        $this->_objectNodeType = 'stdClass';
    }

    /**
     * This method lately "type-hints" the {@link __construct()} $transmorph parameter.
     *
     * @param Transmorph_Processor $t A processor. See {@link __construct()}
     */
    protected function _setTransmorph(Transmorph_Processor $t)
    {
        $this->_t = $t;
    }

    /**
     * This method resolves a write rule to push data in a variable, creating
     * structure nodes in this variable if necessary.
     *
     * @param mixed $node The variable to write in.
     * @param string $path The write rule.
     * @param mixed $value The value to push in $node.
     * 
     * @return mixed The modified $node. 
     */
    public function feed(&$node, $path, $value)
    {

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
            $pathNode = $this->_fireProcessWriteRuleNode($pathNode);

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

                if (!is_object($node))
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
     * @codeCoverageIgnore Trivial.
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
     * @codeCoverageIgnore Trivial.
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
                $this->objectNodeType = $value;
                break;
            default:
                throw new OutOfRangeException(__CLASS__ . ' has no ' . $name . ' property-write.');
                break;
        }
    }

    /**
     * Plugin caller.
     * 
     * @see Transmorph_Plugin_Interface::processWriteRuleNode()
     *
     * @param string $ruleNode passed to plugin.
     * 
     * @return string back from plugin.
     */
    protected function _fireProcessWriteRuleNode($ruleNode)
    {
        if ($this->_t !== null)
        {
            $plugins = $this->_t->plugins;
            /* @var $plugin Transmorph_Plugin_Interface */
            foreach ($plugins as $plugin)
            {
                $ruleNode = $plugin->processWriteRuleNode($this->_t, $ruleNode);
            }
        }

        return $ruleNode;
    }

}