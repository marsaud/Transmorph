<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transmorph_Plugin_IteratorNode
 *
 * @author root
 */
class Transmorph_Plugin_IteratorNode extends Transmorph_Plugin_Abstract
{
    // const ITER_NODE_REGEX = '%^(((/[^#\./\\\]+)|(\.[^#\./\\\]+))*)((\.|/)#)((/[^#\./\\\]+)|(\.[^#\./\\\]+))*)((\.|/).*)*$%';
    const ITER_NODE_REGEX = '%((\.|/)#)%';

    public function processMap(Transmorph_Processor $transmorph, array $map)
    {

        $processedMap = array();

        foreach ($map as $key => $mapLine)
        {
            $lineNumber = $key + 1;
            $tLine = new Transmorph_Line($mapLine);

            $matchesInput = array();
            $foundInput = preg_match_all(self::ITER_NODE_REGEX, $tLine->source, $matchesInput, PREG_OFFSET_CAPTURE);
            if ($foundInput > 1)
            {
                throw new Transmorph_Exception(
                    __CLASS__ . ' supports only one iteration node per map line. Found '
                    . $foundInput
                    . ' in input in line '
                    . $lineNumber . '.'
                );
            }

            $matchesOutput = array();
            $foundOutput = preg_match_all(self::ITER_NODE_REGEX, $tLine->target, $matchesOutput);
            if ($foundOutput > 1)
            {
                throw new Transmorph_Exception(
                    __CLASS__ . ' supports only one iteration node per map line. Found '
                    . $foundOutput
                    . ' in output in line '
                    . $lineNumber . '.'
                );
            }

            if ($foundOutput === 1 && $foundInput === 0)
            {
                throw new Transmorph_Exception('Line ' . $lineNumber . ' : Iteration must root on input');
            }

            if ($foundInput === 0 && $foundOutput === 0)
            {
                $processedMap[] = $mapLine;
            }
            else
            {
                $path = substr($tLine->source, 0, $matchesInput[0][0][1]);
                
                $iterableNode = null;
                try
                {
                    $iterableNode = $this->getIterableNode($transmorph->reader, $transmorph->input, $path);
                }
                catch (Transmorph_Exception $exc)
                {
                    throw new Transmorph_Exception('Processing Map Line ' . $lineNumber . ' throws : ' . $exc->getMessage());
                }

                $mapExtension = $this->extendMapLine($iterableNode, $mapLine);
                $processedMap = array_merge($processedMap, $mapExtension);
            }
        }

        return $processedMap;
    }

    /**
     *
     * @param Transmorph_Reader $reader
     * @param string $input
     * @param string $path 
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
     *
     * @param mixed $iterableNode
     * @param string $mapLine 
     * 
     * @return string[]
     */
    public function extendMapLine($iterableNode, $mapLine)
    {
        $this->_checkIterableNode($iterableNode);
        $mapLineExtension = array();
        
        $count = 0;
        foreach ($iterableNode as $key => $value)
        {
            $newMapLine = preg_replace('/#/', $key, $mapLine, -1, $count);
            $mapLineExtension[] = $newMapLine;
            if ($count === 0)
            {
                break;
            }
        }
        return $mapLineExtension;
    }
    
    /**
     *
     * @param mixed $node
     * 
     * @throws Transmorph_Exception
     * 
     * @return boolean
     */
    protected function _checkIterableNode($node)
    {
        if (!is_array($node) && !is_object($node))
        {
            throw new Transmorph_Exception('Input value node is not iterable.');
        }
        return true;
    }

}