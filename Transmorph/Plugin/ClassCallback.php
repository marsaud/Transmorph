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
 * @subpackage Plugin
 * 
 */

/**
 * Description of Transmorph_Plugin_ClassCallback
 * 
 * @package Transmorph
 * 
 * @subpackage Plugin
 * 
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