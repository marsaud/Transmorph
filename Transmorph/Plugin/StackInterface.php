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
 * A plugin stack interface to implement by classes which will encapsulate the
 * conrete stack.
 *
 * @package Plugin
 */
interface Transmorph_Plugin_StackInterface
{
    /**
     * Adds a plugin in the last position of the plugin stack. The plugin order 
     * is important when several plugin on the stack concretely implement the 
     * same method(s) of the {@link Transmorph_Plugin_Interface}.
     *
     * @param Transmorph_Plugin_Interface $plugin An instance of a plugin.
     * 
     * @return void
     * 
     * @throws Transmorph_Exception If an instance of the same plugin class is 
     * already in the stack.
     */
    public function appendPlugin(Transmorph_Plugin_Interface $plugin);
    
    /**
     * Adds a plugin in first position on the plugin stack. The plugin order is
     * important when several plugin on the stack concretely implement the same
     * method(s) of the {@link Transmorph_Plugin_Interface}.
     *
     * @param Transmorph_Plugin_Interface $plugin An instance of a plugin.
     * 
     * @return void
     * 
     * @throws Transmorph_Exception If an instance of the same plugin class is 
     * already in the stack.
     */
    public function prependPlugin(Transmorph_Plugin_Interface $plugin);
    
    /**
     * Removes a plugin identified by its class name from the plugin stack. 
     *
     * @param string $pluginClassName The class name of the plugin to remove.
     * 
     * @return void
     * 
     * @throws Transmorph_Exception if the plugin to remove is not found in the
     * stack.
     */
    public function removePlugin($pluginClassName);
}
