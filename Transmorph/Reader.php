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
 * @subpackage Reader
 * 
 */

/**
 * Description of Transmorph_Reader
 * 
 * This class handles Transmorph read rules.
 *
 * @package Transmorph
 * 
 * @subpackage Reader
 */
class Transmorph_Reader
{

    /**
     * We can inject a Transmorph_Processor here for the reader to acces the plugin
     * stack.
     * 
     * @todo TASK I don't like this processor/reader coupling for plugin access.
     * Time will bring me an idea...-> IDEA -> Reader (& Writer) should have
     * its own Plugin Stack implemented its own plugin interface, so it will be
     * stand alone. Used by Processor, this one can feed the plugn stack of its
     * reader & writer.
     *
     * @var Transmorph_Processor
     */
    protected $_t;

    public function __construct($transmorph = null)
    {
        if ($transmorph !== null)
        {
            $this->_setTransmorph($transmorph);
        }
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
     * Follows recursively a 'simple' read-rule to pull out a value from an 
     * input variable.
     * 
     *
     * @todo TASK in the read-rules, we could support only '/' and let
     * the query handle the 'array key or object property ?' problem. Or 
     * support '.' as a strict query fr object property, and let the '/'
     * be general...
     *
     * @param mixed $input The variable to read in.
     * @param string $rule The read-rule.
     * 
     * @return mixed The pulled-out value.
     */
    public function query($input, $rule)
    {
        $result = null;

        if ($rule === '' || $rule == '/' || $rule == '.')
        {
            $result = $input;
        }
        else
        {
            $matches = array();
            $found = preg_match('#^((/[^\./\\\]+)|(\.[^\./\\\]+))((\.|/).*)*$#', $rule, $matches);

            if ($found !== 1)
            {
                throw new Transmorph_Reader_Exception('Illegal read-rule : ' . $rule);
            }

            $nextNode = $matches[1];

            $nextNode = $this->_fireProcessReadRuleNode($nextNode);

            $remainingPath = isset($matches[4]) ? $matches[4] : '';
            $key = substr($nextNode, 1);

            if ($nextNode[0] == '/') // tableau
            {
                if (!isset($input[$key]))
                {
                    throw new Transmorph_Reader_Exception('Read-rule leads to nothing');
                }
                $result = $this->query($input[$key], $remainingPath);
            }
            elseif ($nextNode[0] == '.') // objet
            {
                if (!isset($input->$key))
                {
                    throw new Transmorph_Reader_Exception('Read-rule leads to nothing');
                }
                $result = $this->query($input->$key, $remainingPath);
            }
        }

        return $result;
    }

    /**
     * @see Transmorph_Plugin_Interface::processReadRuleNode()
     *
     * @param string $ruleNode passed to plugin.
     * 
     * @return string back from plugin.
     */
    protected function _fireProcessReadRuleNode($ruleNode)
    {
        if ($this->_t !== null)
        {
            $plugins = $this->_t->plugins;
            foreach ($plugins as $plugin)
            {
                $ruleNode = $plugin->processReadRuleNode($this->_t, $ruleNode);
            }
        }

        return $ruleNode;
    }

}