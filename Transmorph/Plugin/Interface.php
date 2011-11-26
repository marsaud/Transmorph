<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author fabrice
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
     * @param Transmorph_Line $line The original line.
     * 
     * @return Transmorph_Line The processed line.
     */
    public function processLine(Transmorph_Processor $transmorph, Transmorph_Line $line);
    
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
