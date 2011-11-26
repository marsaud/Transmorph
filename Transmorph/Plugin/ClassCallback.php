<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transmorph_Plugin_ClassCallback
 * 
 * TEST
 *
 * @author fabrice
 */
class Transmorph_Plugin_ClassCallback extends Transmorph_Plugin_Abstract
{

    public function processCallback(Transmorph_Processor $transmorph, $callback)
    {
        $processedCallback = preg_split('/:(:)?/', $callback);

        if (count($processedCallback) > 2)
        {
            throw new Transmorph_Exception('Broken callback description : ' . $callback);
        }
        elseif (count($processedCallback) == 1)
        {
            $processedCallback = $callback;
        }
        elseif (count($processedCallback) == 2)
        {
            if (empty($processedCallback[0]) || empty($processedCallback[1]))
            {
                throw new Transmorph_Exception('Broken callback description : ' . $callback);
            }
            $r = new ReflectionClass($processedCallback[0]);
            /* @var $m ReflectionMethod */
            $m = $r->getMethod($processedCallback[1]);
            if (!$m->isStatic())
            {
                $c = $r->getConstructor();
                if ($c instanceof ReflectionMethod && $c->getNumberOfRequiredParameters() > 0)
                {
                    throw new Transmorph_Exception(__CLASS__ . ' does not support constructor parameters for callback classes.');
                }

                $className = $r->name;
                $object = new $className();
                $processedCallback[0] = $object;
            }
        }

        return $processedCallback;
    }

}