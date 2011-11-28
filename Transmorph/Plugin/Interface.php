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
 * @package Transmorph
 * 
 * @subpackage Plugin
 */
interface Transmorph_Plugin_Interface
{
    /**
     * @param Transmorph_Processor $transmorph The working Transmorph Instance.
     * @param string[] $map The original map.
     * 
     * @return string[] $map The processed map.
     */
    public function processMap(Transmorph_Processor $transmorph, array $map);
    
    /**
     * @param Transmorph_Processor $transmorph The working Transmorph Instance.
     * @param Transmorph_Rule $rule The original rule.
     * 
     * @return Transmorph_Rule The processed rule.
     */
    public function processRule(Transmorph_Processor $transmorph, Transmorph_Rule $rule);
    
    /**
     * @param Transmorph_Processor $transmorph The working Transmorph Instance.
     * @param string $callback The original callback name.
     * 
     * @return string The processed callback name.
     */
    public function processCallback(Transmorph_Processor $transmorph, $callback);
    
    /**
     * @param Transmorph_Processor $transmorph The working Transmorph Instance.
     * @param string[] $callbackParams An array of ENTRYs to be callback parameters
     * 
     * @return string[] The processed callback array
     */
    public function processCallbackParams(Transmorph_Processor $transmorph, $callbackParams);
    
    /**
     * @param Transmorph_Processor $transmorph The working Transmorph Instance.
     * @param string $pathNode The original pathNode.
     * 
     * @return string The processed pathNode.
     */
    public function processInputPathNode(Transmorph_Processor $transmorph, $pathNode);
    
    /**
     * @param Transmorph_Processor $transmorph The working Transmorph Instance.
     * @param string $pathNode The original pathNode.
     * 
     * @return string The processed pathNode.
     */
    public function processOutPutPathNode(Transmorph_Processor $transmorph, $pathNode);
}

?>
