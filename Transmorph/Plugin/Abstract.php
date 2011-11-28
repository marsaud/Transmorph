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
 * @subpackage Plugin
 * 
 */

/**
 * Description of Transmorph_Plugin_Abstract
 * 
 * @package Transmorph
 * 
 * @subpackage Plugin
 * 
 */
abstract class Transmorph_Plugin_Abstract implements Transmorph_Plugin_Interface
{

    public function processCallback(Transmorph_Processor $transmorph, $callback)
    {
        return $callback;
    }

    public function processCallbackParams(Transmorph_Processor $transmorph, $callbackParams)
    {
        return $callbackParams;
    }

    public function processInputPathNode(Transmorph_Processor $transmorph, $pathNode)
    {
        return $pathNode;
    }

    public function processRule(Transmorph_Processor $transmorph, Transmorph_Rule $rule)
    {
        return $rule;
    }

    public function processMap(Transmorph_Processor $transmorph, array $map)
    {
        return $map;
    }

    public function processOutPutPathNode(Transmorph_Processor $transmorph, $pathNode)
    {
        return $pathNode;
    }

}