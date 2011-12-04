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
 * @subpackage Rule
 * 
 */

/**
 * Description of Transmorph_Line
 *
 * This class encapsulates the parsing of a transormation rule to obtain the 
 * read-rule and the write_rule.
 * 
 * @package Transmorph
 * 
 * @subpackage Rule
 * 
 * @property-read string $readRule The read-rule.
 * @property-read string $writeRule The write-rule.
 */
class Transmorph_Rule
{
    /**
     * The regex that does the job.
     */
    const LINE_REGEX = '#^(.*)( >> )(.*)$#';

    /**
     * The read-rule.
     *
     * @var string
     */
    protected $_readRule;

    /**
     * The write-rule.
     *
     * @var string
     */
    protected $_writeRule;

    /**
     * The constructor triggers the parsing of the rule.
     *
     * @param string $line 
     */
    public function __construct($line)
    {
        $this->_parseRule($line);
    }

    /**
     * Parsing of the rule.
     *
     * @param string $rule The rule to parse.
     * 
     * @throws Transmorph_Rule_Exception
     */
    protected function _parseRule($rule)
    {
        $matches = array();
        $found = preg_match(self::LINE_REGEX, $rule, $matches);

        if ($found !== 1)
        {
            throw new Transmorph_Rule_Exception('STRING is not conform to Transmorph Rule format');
        }

        $this->_readRule = $matches[1];
        $this->_writeRule = $matches[3];
    }

    /**
     * Property handling.
     *
     * @param string $name Property name.
     * 
     * @return mixed Property value. 
     */
    public function __get($name)
    {
        switch ($name)
        {
            case 'readRule':
                return $this->_readRule;
                break;
            case 'writeRule':
                return $this->_writeRule;
                break;
            default:
                throw new OutOfRangeException(__CLASS__ . ' has no ' . $name . ' property');
                break;
        }
    }

    /**
     * @codeCoverageIgnore Trivial
     *
     * @return string The tranformation rule.
     */
    public function __toString()
    {
        return $this->_readRule . ' >> ' . $this->_writeRule;
    }

}