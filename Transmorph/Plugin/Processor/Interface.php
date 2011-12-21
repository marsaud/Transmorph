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
 * An plugin interface used by {@link Transmorph_Processor}.
 * 
 * @package Plugin
 */
interface Transmorph_Plugin_Processor_Interface extends Transmorph_Plugin_Interface
{

    /**
     * Processes transformation rule-set.
     * 
     * Called by {@link Transmorph_Processor::handleFile()} after the rule file
     * has been read.
     * 
     * The point is to be free to put extended information in the transformation 
     * file, to feed extra features. This method should capture those extra 
     * informations before 'cleaning' the rule map before 'standard' 
     * transformation processing.
     * 
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are 
     * plugged in.
     * @param string[] $map The original rule-map.
     * 
     * @return string[] $map The processed rule-map.
     */
    public function processMap(Transmorph_Processor $transmorph, array $map);

    /**
     * Processes rules.
     * 
     * Called by {@link Transmorph_Processor::handleRule()} to process the rule
     * before passing it to the writer.
     * 
     * The point is to extend the informations carried by a rule, to feed extra 
     * features. This method should capture those extra informations before 
     * giving back a {@link Transmorph_Rule} that the 
     * {@link Transmorph_Processor} can process.
     * 
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are 
     * plugged in.
     * @param Transmorph_Rule $rule The original rule.
     * 
     * @return Transmorph_Rule The processed rule.
     */
    public function processRule(Transmorph_Processor $transmorph, Transmorph_Rule $rule);

    /**
     * Processes callbacks.
     * 
     * Called by {@link Transmorph_Processor::handleReadRule()} to process 
     * callback names before using them.
     * 
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are 
     * plugged in.
     * @param mixed $callback The original callback.
     * 
     * @return mixed The processed callback.
     */
    public function processCallback(Transmorph_Processor $transmorph, $callback);

    /**
     * Processes callback parameters.
     * 
     * Called by {@link Transmorph_Processor::handleReadRule()} to process 
     * callback parameters provided as an array of strings assumed to be
     * ordered raw read-rules.
     * 
     * @param Transmorph_Processor $transmorph The Transmorph_Processor we are 
     * plugged in.
     * @param string[] $callbackParams An array of read-rules purposed to be 
     * evaluated as callback parameters.
     * 
     * @return string[] The processed array.
     */
    public function processCallbackParams(Transmorph_Processor $transmorph, $callbackParams);
}