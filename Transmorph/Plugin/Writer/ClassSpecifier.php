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
 *
 */

/**
 * Provides support for class specification in write rules.
 *
 * Example  which will  create an  object of  type MyClass  with a  “prop” property
 * containing the value “1”:
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
class Transmorph_Plugin_Writer_ClassSpecifier extends Transmorph_Plugin_Writer_Abstract
{

    /**
     * Retreives classnames in write-rule properyt-nodes.
     *
     * @param Transmorph_Writer $writer Teh calling writer.
     * @param string $ruleNode The rule to process.
     * 
     * @return string The processed rule-node.
     */
    function processRuleNode(Transmorph_Writer $writer, $ruleNode)
    {
        if ($ruleNode[0] !== '.')
        {
            // Not a property rule.
            return $ruleNode;
        }

        $matches = array();
        if (!preg_match('/^\.([a-z_]+):{1,2}(.+)$/i', $ruleNode, $matches))
        {
            // No class specified.

            /*
             * Manually resets the original object node type.
             *
             * This is not perfect because the assumption that this was the original
             * value is arbitrary and might  be wrong. The correct solution would be
             * to restore  the previous value in  a “tear down” hook  which does not
             * exist yet.
             *
             * @todo Implements the “tear down” hook.
             */
            $writer->objectNodeType = 'stdClass';

            return $ruleNode;
        }

        $writer->objectNodeType = $matches[1];
        return '.' . $matches[2];
    }

}
