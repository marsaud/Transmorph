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
 * Provides support Class method callbacks in complex read-rules.
 *
 * @package Plugin
 *
 */
class Transmorph_Plugin_Processor_ClassCallback extends
Transmorph_Plugin_Processor_Abstract
{

    /**
     * Class Method callbacks support.
     *
     * This plugin method offers to parse a simple Callback expression in
     * read-rules, in PHP callback arrays.
     *
     * The expected format is "Class:method" or "Class::method"
     *
     * If method is static, the returned array will provide class name and
     * method name.
     *
     * If method is not static, the returned array will provide a class instance
     *  and the method name. For this to work, the constructor class must have
     * no required parameters, and it will be called with no parameters at all.
     *
     * @param Transmorph_Processor $transmorph The calling Transmorph_Processor.
     * @param string $callback A callback name.
     *
     * @return array An array representation of a class or object method for
     * callback.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) The injected caller is not
     * used to do the job.
     */
    public function processCallback(Transmorph_Processor $transmorph, $callback)
    {
        $processedCallback = preg_split('/:(:)?/', $callback);

        if (count($processedCallback) > 2)
        {
            throw new Transmorph_Exception(
                'Broken callback description : ' . $callback
            );
        }
        elseif (count($processedCallback) == 1)
        {
            // callback is a classic function
            $processedCallback = $callback;
        }
        elseif (count($processedCallback) == 2)
        {
            if (empty($processedCallback[0]) || empty($processedCallback[1]))
            {
                throw new Transmorph_Exception(
                    'Broken callback description : ' . $callback
                );
            }

            $reflection = new ReflectionClass($processedCallback[0]);
            /* @var $method ReflectionMethod */
            $method = $reflection->getMethod($processedCallback[1]);
            if (!$method->isStatic())
            {
                $constructor = $reflection->getConstructor();
                if ($constructor instanceof ReflectionMethod
                    && $constructor->getNumberOfRequiredParameters() > 0)
                {
                    throw new Transmorph_Exception(
                        __CLASS__
                        . ' does not support constructor parameters for callback'
                        . ' classes.'
                    );
                }

                $className = $reflection->name;
                $object = new $className();
                $processedCallback[0] = $object;
            }
        }

        return $processedCallback;
    }

}