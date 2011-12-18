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
 * Description of Transmorph_Plugin_Reader_Abstract
 * 
 * @package Plugin
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
abstract class Transmorph_Plugin_Reader_Abstract implements Transmorph_Plugin_Reader_Interface
{

    /**
     * Indentity implementation to gain time for simple plugins.
     * 
     * @param Transmorph_Reader $transmorphReader A calling 
     * {@link Transmorph_Reader}.
     * @param string $ruleNode A rule-node to process.
     * 
     * @return string The processed rule-node.
     * 
     * @see Transmorph_Plugin_Reader_Interface::processReadRuleNode()
     */
    public function processRuleNode(Transmorph_Reader $transmorphReader, $ruleNode)
    {
        return $ruleNode;
    }

}