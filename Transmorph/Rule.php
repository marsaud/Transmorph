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
 * @subpackage Line
 * 
 */

/**
 * Description of Transmorph_Line
 *
 * Assure le parsing d'une ligne de mapping (LINE) et encapsule le résultat obtenu.
 * 
 * @package Transmorph
 * 
 * @subpackage Line
 * 
 * @property-read string $sourceRule ENTRY de lecture de la ligne.
 * @property-read string $targetRule PATH d'écriture de la ligne.
 */
class Transmorph_Rule
{
    /**
     * Le pattern d'expression réglière permettant de parser une ligne de mapping (LINE).
     */
    const LINE_REGEX = '#^(.*)( >> )(.*)$#';

    /**
     *
     * @var string
     */
    protected $_sourceRule;

    /**
     *
     * @var string
     */
    protected $_targetRule;

    /**
     * Construit une instance à partir de la ligne de mapping (LINE) fournie.
     *
     * @param string $line 
     */
    public function __construct($line)
    {
        $this->_parseRule($line);
    }

    /**
     * Interprète la LINE pour en extraire l'ENTRY source et le PATH cible.
     *
     * @param string $rule
     * 
     * throws TransmorphLineException
     */
    protected function _parseRule($rule)
    {
        $matches = array();
        $found = preg_match(self::LINE_REGEX, $rule, $matches);

        if ($found !== 1)
        {
            throw new Transmorph_Rule_Exception('STRING is not conform to Transmorph Rule format');
        }

        $this->_sourceRule = $matches[1];
        $this->_targetRule = $matches[3];
    }

    /**
     *
     * @param string $name
     * @return mixed 
     */
    public function __get($name)
    {
        switch ($name)
        {
            case 'sourceRule':
                return $this->_sourceRule;
                break;
            case 'targetRule':
                return $this->_targetRule;
                break;
            default:
                throw new OutOfRangeException(__CLASS__ . ' has no ' . $name . ' property');
                break;
        }
    }

}