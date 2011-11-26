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
 * @package Transmorph
 * 
 * @subpackage Writer
 * 
 * @property string $objectNodeType The type to instanciate when creating object nodes.
 */
class Transmorph_Writer
{

    /**
     *
     * @var Transmorph_Processor
     */
    protected $_t;

    /**
     *
     * @var string
     */
    protected $_objectNodeType;

    public function __construct($transmorph = null)
    {
        if ($transmorph !== null)
        {
            $this->_setTransmorph($transmorph);
        }

        $this->_objectNodeType = 'stdClass';
    }

    protected function _setTransmorph(Transmorph_Processor $t)
    {
        $this->_t = $t;
    }

    public function feed(&$node, $path, $value)
    {
        if ($path === '' || $path == '/' || $path == '.')
        {
            $node = $value;
        }
        else
        {
            $matches = array();
            $found = preg_match('#^((/[^\./\\\]+)|(\.[^\./\\\]+))((\.|/).*)*$#', $path, $matches);
            if ($found !== 1)
            {
                /**
                 * @todo complete
                 */
                throw new Transmorph_Writer_Exception();
            }
            $pathNode = $matches[1];
            $pathNode = $this->_fireProcessOuputPathNode($pathNode);

            $remainingPath = isset($matches[4]) ? $matches[4] : '';

            if ($pathNode[0] == '/')
            {
                if ($node === null)
                {
                    $node = array();
                }

                if (!is_array($node))
                {
                    /**
                     * @todo complete
                     */
                    throw new Transmorph_Writer_Exception();
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
                if ($node === null)
                {
                    $class = $this->_objectNodeType;
                    $node = new $class();
                }

                if (!is_object($node))
                {
                    /**
                     * @todo complete
                     */
                    throw new Transmorph_Writer_Exception();
                }

                // objet
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
     * @codeCoverageIgnore Trivial
     *
     * @param string $name
     * @return mixed 
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
     * @codeCoverageIgnore Trivial
     *
     * @param string $name
     * @param mixed $value 
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
     * @codeCoverageIgnore
     *
     * @param string $pathNode
     * @return string 
     */
    protected function _fireProcessOuputPathNode($pathNode)
    {
        if ($this->_t !== null)
        {
            $plugins = $this->_t->plugins;
            foreach ($plugins as $plugin)
            {
                $pathNode = $plugin->processOutPutPathNode($this->_t, $pathNode);
            }
        }

        return $pathNode;
    }

}