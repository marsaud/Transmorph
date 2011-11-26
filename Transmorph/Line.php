<?php

/**
 * @package Transmorph
 */

/**
 * Description of Transmorph_Line
 *
 * Assure le parsing d'une ligne de mapping (LINE) et encapsule le résultat obtenu.
 * 
 * @author fabrice
 * 
 * @package Transmorph
 * @subpackage Line
 * 
 * @property-read string $source ENTRY de lecture de la ligne.
 * @property-read string $target PATH d'écriture de la ligne.
 */
class Transmorph_Line
{
    /**
     * Le pattern d'expression réglière permettant de parser une ligne de mapping (LINE).
     */
    const LINE_REGEX = '#^(.*)( >> )(.*)$#';

    /**
     *
     * @var string
     */
    protected $_source;

    /**
     *
     * @var string
     */
    protected $_target;

    /**
     * Construit une instance à partir de la ligne de mapping (LINE) fournie.
     *
     * @param string $line 
     */
    public function __construct($line)
    {
        $this->_parseLine($line);
    }

    /**
     * Interprète la LINE pour en extraire l'ENTRY source et le PATH cible.
     *
     * @param string $line
     * 
     * throws TransmorphLineException
     */
    protected function _parseLine($line)
    {
        $matches = array();
        $found = preg_match(self::LINE_REGEX, $line, $matches);

        if ($found !== 1)
        {
            throw new Transmorph_Line_Exception('STRING is not conform to TransmorphLine format');
        }

        $this->_source = $matches[1];
        $this->_target = $matches[3];
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
            case 'source':
                return $this->_source;
                break;
            case 'target':
                return $this->_target;
                break;
            default:
                throw new OutOfRangeException(__CLASS__ . ' has no ' . $name . ' property');
                break;
        }
    }

}