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
 * Basic implementation of {@link Transmorph_Plugin_Reader_Interface} for
 * further extensions.
 * 
 * All methods are pre-implemented to do no side-effects, so we just have to 
 * override what we need.
 * 
 * Ok, there is only one method fr the moment, but this is here as a standard
 * habit, in case the interface grows over time.
 * 
 * @package Plugin
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter) As methods do no side effects,
 * parameters are oftenly not used.
 */
abstract class Transmorph_Plugin_Reader_Abstract implements Transmorph_Plugin_Reader_Interface
{

    /**
     * Indentity implementation to save time for simple plugins.
     * 
     * @param Transmorph_Reader $transmorphReader A calling 
     * {@link Transmorph_Reader}.
     * @param string $ruleNode A rule-node to process.
     * 
     * @return string The processed rule-node.
     * 
     * @see Transmorph_Plugin_Reader_Interface::processRuleNode()
     */
    public function processRuleNode(Transmorph_Reader $transmorphReader, $ruleNode)
    {
        return $ruleNode;
    }

}