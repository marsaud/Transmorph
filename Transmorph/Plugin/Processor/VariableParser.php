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
 * Provides support for variables in rules.
 *
 * A variable is  a described by a  “at sign” (“@”) followed by  a succession of
 * alphanumeric characters and/or underscores.
 *
 * Note 1: Variables are not parsed in constant read rules.
 *
 * Note 2: Variables  are directly replaced inside the rule  by their values, it
 *   is the user's job to make sure the resulting rule is valid.
 *
 * @author Julien Fontanet <julien.fontanet@isonoe.net>
 * 
 * @package Plugin
 */
class Transmorph_Plugin_Processor_VariableParser extends
Transmorph_Plugin_Processor_Abstract
{

    /**
     * @var string[]
     */
    protected $_vars;

    /**
     * Trivial.
     * 
     * @param string[] $vars Names to values associative array.
     *
     *  @return void
     */
    public function __construct(array $vars)
    {
        $this->_vars = $vars;
    }

    /**
     * Replaces variables in the rule.
     *
     * @param Transmorph_Processor $transmorphProcessor The calling processor.
     * @param Transmorph_Rule $rule The rule to process.
     * 
     * @return Transmorph_Rule The processed rule.
     */
    public function processRule(
    Transmorph_Processor $transmorphProcessor, Transmorph_Rule $rule
    )
    {
        if (!$transmorphProcessor->isConst($rule->readRule))
        {
            $rule->readRule = $this->_parse($rule->readRule);
        }
        $rule->writeRule = $this->_parse($rule->writeRule);

        return $rule;
    }

    /**
     * The variable replacement.
     * 
     * @param string $ruleString A read-rule or write-rule.
     *
     * @return string The rule with variables replaced by there values.
     */
    protected function _parse($ruleString)
    {
        return preg_replace_callback(
            '/@([a-z0-9_]+)/i', array($this, '_replaceCallback'), $ruleString
        );
    }

    /**
     * The callback dedicated to variable replacement.
     * 
     * @param string[] $matches An array of substrings matching the variable
     * pattern.
     *
     * @return string the replacement for the matches.
     */
    protected function _replaceCallback(array $matches)
    {
        if (isset($this->_vars[$matches[1]]))
        {
            return $this->_vars[$matches[1]];
        }

        return $matches[0];
    }

}
