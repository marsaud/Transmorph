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
 * Provides support for variables in rules.
 *
 * A variable is  a described by a  “at sign” (“@”) followed by  a succession of
 * alphanumeric characters and/or underscores.
 *
 * Note 1: Variables are not parsed in constant read rules.
 *
 * Note 2: Variables  are directly replaced inside the rule  by their values, it
 *   is the user's job to make sure the resulting rule is valid.
 *
 * @author Julien Fontanet <julien.fontanet@isonoe.net>
 * @package Plugin
 */
class Transmorph_Plugin_Processor_VariableParser extends Transmorph_Plugin_Processor_Abstract
{
	/**
	 * @param string[] $vars Names to values associative array.
	 */
	function __construct(array $vars)
	{
		$this->_vars = $vars;
	}

	/**
	 * @return Transmorph_Rule
	 */
	function processRule(Transmorph_Processor $p, Transmorph_Rule $r)
	{
		if (!$p->isConst($r->readRule))
		{
			$r->readRule = $this->_parse($r->readRule);
		}
		$r->writeRule = $this->_parse($r->writeRule);

		return $r;
	}

	/**
	 * @var string[]
	 */
	protected $_vars;

	/**
	 * @param string $s
	 *
	 * @return string
	 */
	protected function _parse($s)
	{
		return preg_replace_callback(
			'/@([a-z0-9]+)/i',
			array($this, '_replaceCallback'),
			$s
		);
	}

	/**
	 * @param string[] $matches
	 *
	 * @return string
	 */
	protected function _replaceCallback(array $matches)
	{
		if (isset($this->_vars[$matches[1]]))
		{
			return $this->_vars[$matches[1]];
		}

		return $matches[0];
	}
}
