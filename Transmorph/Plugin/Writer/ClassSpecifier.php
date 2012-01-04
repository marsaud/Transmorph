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
 * Provides support for class specification in write rules.
 *
 * Example  which will  create an  object of  type MyClass  with a  “p” property
 * containing the value “1”:
 *
 *     \1 >> .MyClass::p
 *
 * @author Julien Fontanet <julien.fontanet@isonoe.net>
 * @package Plugin
 */
class Transmorph_Plugin_Writer_ClassSpecifier extends Transmorph_Plugin_Writer_Abstract
{
	/**
	 * @param string $ruleNode
	 */
	function processRuleNode(Transmorph_Writer $w, $ruleNode)
	{
		if ($ruleNode[0] !== '.')
		{
			// Not a property rule.

			return $ruleNode;
		}

		if (!preg_match('/^\.([a-z_]+):{1,2}(.+)$/i', $ruleNode, $matches))
		{
			// No class specified.

			/*
			 * Manually resets the original object node type.
			 *
			 * This is not perfect because the assumption that this was the original
			 * value is arbitrary and might  be wrong. The correct solution would be
			 * to restore  the previous value in  a “tear down” hook  which does not
			 * exist yet.
			 *
			 * TODO: Implements the “tear down” hook.
			 */
			$w->objectNodeType = 'stdClass';

			return $ruleNode;
		}

		$w->objectNodeType = $matches[1];
		return '.'.$matches[2];
	}
}
