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
 * An plugin interface used by {@link Transmorph_Reader}.
 * 
 * @package Plugin
 * 
 * @todo
 */
interface Transmorph_Plugin_Reader_Interface extends Transmorph_Plugin_Interface
{

    /**
     * Called by {@link Transmorph_Reader::query()} to process a read-rule node 
     * before the Transmorph_Reader will exploit it.
     * 
     * @param Transmorph_Reader $transmorphReader The Transmorph_Reader we are 
     * plugged in.
     * @param string $ruleNode The original rule node.
     * 
     * @return string The processed rule node.
     */
    public function processRuleNode(Transmorph_Reader $transmorphReader, $ruleNode);
}
