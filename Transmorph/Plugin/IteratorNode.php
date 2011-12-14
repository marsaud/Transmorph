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
 * Description of Transmorph_Plugin_IteratorNode
 * 
 * @package Plugin
 * 
 */
class Transmorph_Plugin_IteratorNode extends Transmorph_Plugin_Abstract
{
    const ITER_NODE_REGEX = '%((\.|/)#)%';

    /**
     * This plugin method searchs for the # symbol as an array key or object
     * attribute in a read rule-node. 
     * 
     * When found, it iterates on the targeted node in the input of the injected
     *  {@link Transmorph_Processor} object to create the subsequent standard 
     * read-rules based on the node keys.
     *
     * @param Transmorph_Processor $transmorph A calling 
     * {@link Transmorph_Processor}.
     * @param string[] $map An array of transformation rule strings asssumed to 
     * come from a transformation file.
     * 
     * @return string[] The processed tranformation rules.
     */
    public function processMap(Transmorph_Processor $transmorph, array $map)
    {

        $processedMap = array();

        foreach ($map as $mapRule)
        {
            $tRule = new Transmorph_Rule($mapRule);

            $matchesInput = array();
            $foundInput = preg_match_all(self::ITER_NODE_REGEX, $tRule->readRule, $matchesInput, PREG_OFFSET_CAPTURE);
            if ($foundInput > 1)
            {
                throw new Transmorph_Exception(
                    __CLASS__ . ' supports only one iteration node per read-rule. Found '
                    . $foundInput
                    . ' in read-rule : '
                    . $tRule->readRule
                );
            }

            $matchesOutput = array();
            $foundOutput = preg_match_all(self::ITER_NODE_REGEX, $tRule->writeRule, $matchesOutput);
            if ($foundOutput > 1)
            {
                throw new Transmorph_Exception(
                    __CLASS__ . ' supports only one iteration node per write-rule. Found '
                    . $foundOutput
                    . ' in write-rule : '
                    . $tRule->writeRule
                );
            }

            if ($foundOutput === 1 && $foundInput === 0)
            {
                throw new Transmorph_Exception('Iteration must root on read-rule.');
            }

            if ($foundInput === 0 && $foundOutput === 0)
            {
                $processedMap[] = $mapRule;
            }
            else
            {
                $path = substr($tRule->readRule, 0, $matchesInput[0][0][1]);

                $iterableNode = null;
                try
                {
                    $iterableNode = $this->getIterableNode($transmorph->reader, $transmorph->input, $path);
                }
                catch (Transmorph_Exception $exc)
                {
                    throw new Transmorph_Exception(
                        __CLASS__ . ' throws : ' . $exc->getMessage() . ' on rule : ' . $tRule
                    );
                }

                $mapExtension = $this->extendRule($iterableNode, $mapRule);
                $processedMap = array_merge($processedMap, $mapExtension);
            }
        }

        return $processedMap;
    }

    /**
     * Retrieves a node in an input data structure, checking it is iterable, so
     * the plugin can do the job.
     *
     * @param Transmorph_Reader $reader A reader to perform a query.
     * @param mixed $input The input structure to query in.
     * @param string $path The path for the query
     * 
     * @return mixed The iterable node
     */
    public function getIterableNode(Transmorph_Reader $reader, $input, $path)
    {
        $toIterate = $reader->query($input, $path);
        $this->_checkIterableNode($toIterate);
        return $toIterate;
    }

    /**
     * The core method of the plugin. Create a read-rule for every key in the
     * iterable stuff trageted by the # symbol in the original read-rule.
     *
     * @param mixed $iterableNode An iterable variable.
     * @param string $mapRule A read-rule with the # generic key.
     * 
     * @return string[] The deterministic read-rules based on the real keys of
     * the $iterableNode argument.
     * 
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function extendRule($iterableNode, $mapRule)
    {
        $this->_checkIterableNode($iterableNode);
        $mapRuleExtension = array();
        
        $count = 0;
        foreach ($iterableNode as $key => $value)
        {
            $newMapRule = preg_replace('/#/', $key, $mapRule, -1, $count);
            $mapRuleExtension[] = $newMapRule;
            if ($count === 0)
            {
                break;
            }
        }
        return $mapRuleExtension;
    }

    /**
     * Checks if a variable is iterable.
     *
     * @param mixed $node The variable to check.
     * 
     * @throws Transmorph_Exception
     * 
     * @return void
     */
    protected function _checkIterableNode($node)
    {
        if (!is_array($node) && !is_object($node))
        {
            throw new Transmorph_Exception('Input value node is not iterable.');
        }
    }

}