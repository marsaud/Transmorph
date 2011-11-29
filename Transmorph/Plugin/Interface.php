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
 * An plugin interface used by {@link Transmorph_Processor}.
 * 
 * @package Transmorph
 * 
 * @subpackage Plugin
 */
interface Transmorph_Plugin_Interface
{
    /**
     * Called by {@link Transmorph_Processor::run()} to process the lines
     * returned by {@link Transmorph_Processor::handleFile()}.
     * 
     * The point is to be free to put extended information in the transformation 
     * file, to feed extra features. This method should capture those extra 
     * informations before 'cleaning' the file before 'standard' transformation 
     * processing.
     * 
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are plugged in.
     * @param string[] $map The original rule-map.
     * 
     * @return string[] $map The processed rule-map.
     */
    public function processMap(Transmorph_Processor $transmorph, array $map);
    
    /**
     * Called by {@link Transmorph_Processor::handleRule()} to process the rule
     * before passing it to the writer.
     * 
     * The point is to extend the informations carried by a rule, to feed extra 
     * features. This method should capture those extra informations before giving
     * back a {@link Transmorph_Rule} that the {@link Transmorph_Processor} can
     * process.
     * 
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are plugged in.
     * @param Transmorph_Rule $rule The original rule.
     * 
     * @return Transmorph_Rule The processed rule.
     */
    public function processRule(Transmorph_Processor $transmorph, Transmorph_Rule $rule);
    
    /**
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are plugged in.
     * @param string $callback The original callback name.
     * 
     * @return string The processed callback name.
     */
    public function processCallback(Transmorph_Processor $transmorph, $callback);
    
    /**
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are plugged in.
     * @param string[] $callbackParams An array of ENTRYs to be callback parameters
     * 
     * @return string[] The processed callback array
     */
    public function processCallbackParams(Transmorph_Processor $transmorph, $callbackParams);
    
    /**
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are plugged in.
     * @param string $ruleNode The original rule node.
     * 
     * @return string The processed rule node.
     */
    public function processReadRuleNode(Transmorph_Processor $transmorph, $ruleNode);
    
    /**
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are plugged in.
     * @param string $ruleNode The original rule node.
     * 
     * @return string The processed rule node.
     */
    public function processWriteRuleNode(Transmorph_Processor $transmorph, $ruleNode);
}