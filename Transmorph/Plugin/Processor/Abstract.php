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
 * Description of Transmorph_Plugin_Processor_Abstract
 * 
 * @package Plugin
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * 
 */
abstract class Transmorph_Plugin_Processor_Abstract implements Transmorph_Plugin_Processor_Interface
{

    /**
     * Indentity implementation to gain time for simple plugins.
     * 
     * @param Transmorph_Processor $transmorph A calling {@link Transmorph_Processor}.
     * @param mixed $callback A callback name or array representation.
     * 
     * @return mixed The processed callback.
     * 
     * @see Transmorph_Plugin_Interface::processCallback()
     */
    public function processCallback(Transmorph_Processor $transmorph, $callback)
    {
        return $callback;
    }

    /**
     * Indentity implementation to gain time for simple plugins.
     * 
     * @param Transmorph_Processor $transmorph A calling {@link Transmorph_Processor}.
     * @param string[] $callbackParams An array of read-rules. 
     * 
     * @return string[] The processed read-rules. 
     *
     * @see Transmorph_Plugin_Interface::processCallbackParams()
     */
    public function processCallbackParams(Transmorph_Processor $transmorph, $callbackParams)
    {
        return $callbackParams;
    }

    /**
     * Indentity implementation to gain time for simple plugins.
     * 
     * @param Transmorph_Processor $transmorph A calling {@link Transmorph_Processor}.
     * @param Transmorph_Rule $rule A transformation rule object.
     * 
     * @return Transmorph_Rule The processed rule.
     *
     * @see Transmorph_Plugin_Interface::processRule()
     */
    public function processRule(Transmorph_Processor $transmorph, Transmorph_Rule $rule)
    {
        return $rule;
    }

    /**
     * Indentity implementation to gain time for simple plugins.
     * 
     * @param Transmorph_Processor $transmorph A calling {@link Transmorph_Processor}.
     * @param string[] $map An array of ordered strings assumed to come from a rule file. 
     * 
     * @return array The array on its way (perhaps through several plugins) to become an array of transformation rules.
     *
     * @see Transmorph_Plugin_Interface::processMap()
     */
    public function processMap(Transmorph_Processor $transmorph, array $map)
    {
        return $map;
    }

}