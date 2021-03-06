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
 */

/**
 * An plugin interface used by {@link Transmorph_Writer}.
 *
 * @package Plugin
 */
interface Transmorph_Plugin_Writer_Interface extends Transmorph_Plugin_Interface
{

    /**
     * Processes write-rule nodes.
     *
     * Called by {@link Transmorph_Writer::feed()} to process a write-rule node
     * before the Transmorph_Writer will exploit it.
     *
     * @param Transmorph_Writer $transmorphWriter The Transmorph_Writer we
     * are plugged in.
     * @param string $ruleNode The original rule node.
     *
     * @return string The processed rule node.
     */
    public function processRuleNode(
    Transmorph_Writer $transmorphWriter, $ruleNode
    );

    /**
     * An opportunity to do post-work before the writer ends up a job.
     *
     * @param Transmorph_Writer $transmorphWriter The Transmorph_Writer we
     * are plugged in.
     *
     * @return void
     */
    public function post(Transmorph_Writer $transmorphWriter);
}
