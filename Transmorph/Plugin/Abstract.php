<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transmorph_Plugin_Abstract
 *
 * @author root
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

    public function processLine(Transmorph_Processor $transmorph, Transmorph_Line $line)
    {
        return $line;
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