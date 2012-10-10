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
 * @author Julien Fontanet <julien.fontanet@isonoe.net>
 *
 * @package Plugin
 *
 */

/**
 * Provides support for class specification in write rules.
 *
 * Example  which will  create an  object of  type MyClass  with a  “prop”
 * property containing the value “1” :
 *
 * \1 >> .MyClass::prop
 *
 * or
 *
 * \1 >> .MyClass:prop
 *
 * @author Julien Fontanet <julien.fontanet@isonoe.net>
 *
 * @package Plugin
 */
class Transmorph_Plugin_Writer_ClassSpecifier implements
Transmorph_Plugin_Writer_Interface
{

    /**
     *
     * @var string
     */
    protected $_typeBuffer;

    /**
     * Retreives classnames in write-rule property-nodes.
     *
     * @param Transmorph_Writer $writer The calling writer.
     * @param string $ruleNode The rule to process.
     *
     * @return string The processed rule-node.
     */
    function processRuleNode(Transmorph_Writer $writer, $ruleNode)
    {
        if (!preg_match('#^(\.|/)([^\./])*#', $ruleNode))
        {
            throw new Transmorph_Exception('Incorrect rule-node');
        }

        if (!preg_match('#^(\.|/)([a-z_]+):{1,2}(.*)$#i', $ruleNode, $matches))
        {
            // No class specified.
            return $ruleNode;
        }

        $this->_typeBuffer = $writer->objectNodeType;
        $writer->objectNodeType = $matches[2];

        return $matches[1] . $matches[3];
    }

    /**
     * If a type has been buffered by processRuleNode, it will be given back to
     * the writer now.
     *
     * @param Transmorph_Writer $writer The calling writer.
     *
     * @return void
     */
    public function post(Transmorph_Writer $writer)
    {
        $this->_typeBuffer === NULL
          || $writer->objectNodeType = $this->_typeBuffer;

        $this->_typeBuffer = NULL;
    }

}
