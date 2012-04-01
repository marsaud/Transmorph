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
 * 
 */

/**
 * Handling Transmorph read rules.
 *
 * @package Core
 * 
 */
class Transmorph_Reader implements Transmorph_Plugin_StackInterface
{

    /**
     * A plugin stack.
     *
     * @var Transmorph_Plugin_StackInterface
     */
    protected $_pluginStack;

    /**
     * Initializations.
     */
    public function __construct()
    {
        $this->_pluginStack = new Transmorph_Plugin_Stack();
    }

    /**
     * Follows recursively a 'simple' read-rule to pull out a value from an 
     * input variable.
     * 
     * @param mixed $input The variable to read in.
     * @param string $rule The read-rule.
     * 
     * @return mixed The pulled-out value.
     * 
     * @todo FEATURE. In the read-rules, we could support only '/' and let
     * the query handle the 'array key or object property ?' problem. Or 
     * support '.' as a strict query requiring object property, and let the '/'
     * be general...
     * 
     * @throws InvalidArgumentException If the query finds a resource to return.
     */
    public function query($input, $rule)
    {
        $result = null;

        if ($rule === '')
        {
            $result = $input;
        }
        else
        {
            $matches = array();
            $found = preg_match(
                '@
                ^
                (
                /[^\./\\\]+#array-node
                |
                \.[^\./\\\]+#object-node
                )
                ((\.|/).*)*#other-nodes
                $
                @x', $rule, $matches
            );

            if ($found !== 1)
            {
                throw new Transmorph_Reader_Exception(
                    'Illegal read-rule : ' . $rule
                );
            }

            $nextNode = $matches[1];

            $nextNode = $this->_fireProcessReadRuleNode($nextNode);

            $remainingPath = isset($matches[2]) ? $matches[2] : '';
            $key = substr($nextNode, 1);

            if ($nextNode[0] == '/') // array
            {
                if (!isset($input[$key]))
                {
                    throw new Transmorph_Reader_Exception(
                        'Read-rule leads to nothing'
                    );
                }
                $result = $this->query($input[$key], $remainingPath);
            }
            elseif ($nextNode[0] == '.') // object
            {
                if (!isset($input->$key))
                {
                    throw new Transmorph_Reader_Exception(
                        'Read-rule leads to nothing'
                    );
                }
                $result = $this->query($input->$key, $remainingPath);
            }
        }

        if (gettype($result) === 'resource')
        {
            throw new InvalidArgumentException(
                'resource type is not supported'
            );
        }

        return $result;
    }

    /**
     * Firing plugins.
     *
     * @param string $ruleNode passed to plugin.
     * 
     * @return string back from plugin.
     * 
     * @see Transmorph_Plugin_Reader_Interface::processRuleNode()
     */
    protected function _fireProcessReadRuleNode($ruleNode)
    {
        foreach ($this->_pluginStack as $plugin)
        {
            /* @var $plugin Transmorph_Plugin_Reader_Interface */
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
        if ($plugin instanceof Transmorph_Plugin_Reader_Interface)
        {
            $this->_pluginStack->appendPlugin($plugin);
        }
        else
        {
            throw new Transmorph_Reader_Exception(
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
        if ($plugin instanceof Transmorph_Plugin_Reader_Interface)
        {
            $this->_pluginStack->prependPlugin($plugin);
        }
        else
        {
            throw new Transmorph_Reader_Exception(
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