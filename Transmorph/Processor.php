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
 * @package Core
 * 
 */

/**
 * Processing transformation rules.
 * 
 * Transmorph's purpose is to provide data stucture transformation driven by a
 * file giving tranformation rules.
 * 
 * Transmorph_Processor is the "front" component for every day use.
 * 
 * A full file-driven transformation can be done with the {@link run()} method,
 * more particular operations are available, like a transformation from a
 * rule string with {@link handleRule()} or simply use "read-rules" to
 * explore data structures with {@link handleReadRule}.
 * 
 * See also {@link Transmorph_Reader} and {@link Transmorph_Writer} doc for
 * other particular operations.
 * 
 * Transmorph_Processor functionalities can be extended by providing plugins
 * implementing {@link Transmorph_Plugin_Interface} and its various extensions.
 * 
 * @package Core
 * 
 * @property-read Transmorph_Reader $reader The input reader component.
 * @property-read Transmorph_Writer $writer The output writer component.
 * @property-read mixed $input The input submitted to {@link run()}. Always null
 *  except for plugins fired by {@link run()}.
 */
class Transmorph_Processor implements Transmorph_Plugin_StackInterface
{
    /**
     * A regex for constant rules.
     */
    const REGEX_CONST = '#^\\\.+$#';

    /**
     * The input submitted to {@link run()}. Encapsulated to be read by plugins.
     *
     * @var mixed
     */
    protected $_input;

    /**
     * The input reader component.
     *
     * @var Transmorph_Reader
     */
    protected $_reader;

    /**
     * The output writer component.
     *
     * @var Transmorph_Writer
     */
    protected $_writer;

    /**
     *
     * @var Transmorph_Plugin_Stack
     */
    protected $_pluginStack;

    /**
     * Initializes encapsulated readers and writers.
     * 
     * @todo FEATURE. The constructor could call a kind of _init protected 
     * overridable method(s) to instanciate reader & writer, so extending 
     * Transmorph_Process would be an opportunity to use extended 
     * Transmorph_Reader and Transmorph_Writer subclasses. 
     */
    public final function __construct()
    {
        $this->_reader = new Transmorph_Reader($this);
        $this->_writer = new Transmorph_Writer($this);
        $this->_pluginStack = new Transmorph_Plugin_Stack();
    }

    /**
     * Main run.
     * 
     * Call this method for the main use of Transmorph : transforming a data 
     * structure to another one following a set of rules written in a file.
     *
     * @param mixed $input A variable of any type. As the purpose is to 
     * tranform structures, most of the time the input will be a structure of 
     * array and/or objects.
     * @param string $ruleFilePath The path to the file defining tranformation 
     * rules.
     * 
     * @return mixed The output structure resulting from the transformation.
     * 
     * @todo FEATURE. This method could take a path/to/file, or the 
     * transformation rules in a string, as it could be written in the file.
     */
    public function run($input, $ruleFilePath)
    {
        $this->_input = $input;

        if (!file_exists($ruleFilePath))
        {
            throw new Transmorph_Exception('Rule file does not exist');
        }

        $ruleMap = $this->handleFile($ruleFilePath);

        $output = null;
        foreach ($ruleMap as $rule)
        {
            $this->handleRule($output, $input, $rule);
        }

        $this->_input = null;
        return $output;
    }

    /**
     * Rule-file handling.
     * 
     * Reads a file to give back the file lines in an array of strings.
     * Of course the expected file is a tranformation rule file.
     * 
     * @param string $filePath A /path/to/a_file.
     *
     * @return string[] The lines found in the file.
     * 
     * @todo FEATURE. As {@link run()} is implemented, it expects an array of 
     * transformation rules given "ready to use" by handleFile; and it passes
     * this array to {@link _fireProcessMap()}. It would be interesting to
     * introduce a _fireProcessFileLines in handleFile to take some work to be
     * done to go from "file" to "map".
     */
    public function handleFile($filePath)
    {
        $ruleMap = file($filePath, FILE_IGNORE_NEW_LINES);
        $ruleMap = $this->_fireProcessMap($ruleMap);
        return $ruleMap;
    }

    /**
     * Executes one transformation rule.
     *
     * @param mixed &$output A variable where the data pulled from $input will 
     * be written. 
     * @param mixed $input The data structure to transmorph to $ouput.
     * @param string $rule A single complete transformation rule.
     * 
     * @return void
     */
    public function handleRule(&$output, $input, $rule)
    {
        $tRule = new Transmorph_Rule($rule);
        $tRule = $this->_fireProcessRule($tRule);
        $this->_writer->feed($output, $tRule->writeRule, $this->handleReadRule($input, $tRule->readRule));
    }

    /**
     * Pulls data from an $input following a read-rule.
     * 
     * This function can recursively follow imbricated callbacks declared in the
     * read-rule. Recursivity stops when a constant rule or a simple read-rule 
     * is found. 
     *
     * @param mixed $input The data read followinf the rule.
     * @param string $readRule The read-rule.
     * 
     * @return mixed The data read and/or processed(when callbacks are used).
     */
    public function handleReadRule($input, $readRule)
    {
        if ($this->isConst($readRule))
        {
            return $this->_evalConstRule($readRule);
        }
        elseif ($this->isPath($readRule))
        {
            return $this->_reader->query($input, $readRule);
        }
        else
        {
            $callback = $this->findCallback($readRule);
            $callback = $this->_fireProcessCallback($callback);
            if ($callback === '')
            {
                throw new Transmorph_Exception('Illegal Entry');
            }
            else
            {
                $paramExps = $this->findCallbackParams($readRule);
                $paramExps = $this->_fireProcessCallbackParams($paramExps);

                $inputArray = array();
                for ($i = 0; $i < count($paramExps); $i++)
                {
                    $inputArray[] = $input;
                }
                return call_user_func_array($callback, array_map(array($this, __FUNCTION__), $inputArray, $paramExps));
            }
        }
    }

    /**
     * Evaluates a constant read-rule.
     *
     * @param string $constRule A constant read-rule
     * 
     * @return string The constant value
     * 
     * @todo FEATURE. This method could apply a casting strategy to handle
     * PHP simple internal type (int, float, string, boolean).
     */
    protected function _evalConstRule($constRule)
    {
        return substr($constRule, 1);
    }

    /**
     * Checks if a read-rule is a constant rule.
     *
     * @param string $mapEntry The read-rule to check.
     * 
     * @return boolean True if the rule is constant rule.
     */
    public function isConst($mapEntry)
    {
        return preg_match(self::REGEX_CONST, $mapEntry) == 1;
    }

    /**
     * Checks if a read-rule is a simple read-rule.
     *
     * @param string $mapEntry The rule to check.
     * 
     * @return boolean True if the rule is simple read-rule.
     */
    public function isPath($mapEntry)
    {
        $pathRegex = '#^((/[^\./\\\]+)|(\.[^\./\\\]+))*(/|\.)?$#';
        return preg_match($pathRegex, $mapEntry) == 1;
    }

    /**
     * Assuming a read-rule to be a complex read-rule, attempts to parse out a 
     * callback name.
     *
     * @param string $mapEntry The rule to analyse.
     * 
     * @return string The callback name if found, null otherwise.
     */
    public function findCallback($mapEntry)
    {
        $callbackName = '';
        $entryRegex = '#^([^/\.\(]+)\(.*\)$#';
        $matches = array();

        preg_match($entryRegex, $mapEntry, $matches);
        if (isset($matches[1]))
        {
            $callbackName = $matches[1];
        }

        return $callbackName;
    }

    /**
     * Assuming a read-rule to be a complex read-rule, attempts to parse out 
     * parameters for a callback.
     *
     * @param string $mapEntry The rule to analyse.
     * 
     * @return string[] An array of strings expected to be read-rules to provide
     * parameters for a callback.
     */
    public function findCallbackParams($mapEntry)
    {
        $parameters = array();
        /*
         * This regex captures a () block following what can be a callback name.
         */
        $entryRegex = '#^[^/\.\(]+\((.*)\)$#';

        $matches = array();
        preg_match($entryRegex, $mapEntry, $matches);
        if (isset($matches[1]))
        {
            $paramString = $matches[1];
            $index = 0;
            /*
             * We watch the parenthesis depth to keep parameters for imbricated 
             * callbacks uses for later.
             */
            $parenthesisDepth = 0;

            for ($i = 0; $i < strlen($paramString); $i++)
            {
                if ($paramString[$i] == '(')
                {
                    $parenthesisDepth++;
                }
                elseif ($paramString[$i] == ')')
                {
                    $parenthesisDepth--;
                }

                if ($paramString[$i] == ',' && $parenthesisDepth == 0)
                {
                    $index++;
                }
                else
                {
                    if (!isset($parameters[$index]))
                    {
                        $parameters[$index] = '';
                    }
                    $parameters[$index] .= $paramString[$i];
                }
            }
        }

        return $parameters;
    }

    /**
     * Adds a plugin in first position on the plugin stack. The plugin order is
     * important when several plugin on the stack concretely implement the same
     * method(s) of the {@link Transmorph_Plugin_Interface}.
     *
     * @param Transmorph_Plugin_Processor_Interface $plugin An instance of a 
     * plugin.
     * 
     * @return void
     * 
     * @throws Transmorph_Exception If an instance of the same plugin class is 
     * already in the stack.
     */
    public function prependPlugin(Transmorph_Plugin_Interface $plugin)
    {
        if ($plugin instanceof Transmorph_Plugin_Processor_Interface)
        {
            $this->_pluginStack->prependPlugin($plugin);
        }
        elseif ($plugin instanceof Transmorph_Plugin_Reader_Interface)
        {
            $this->reader->prependPlugin($plugin);
        }
        elseif ($plugin instanceof Transmorph_Plugin_Writer_Interface)
        {
            $this->writer->prependPlugin($plugin);
        }
        else
        {
            throw new Transmorph_Exception('Unsupported plugin interface');
        }
    }

    /**
     * Adds a plugin in the last position of the plugin stack. The plugin order 
     * is important when several plugin on the stack concretely implement the 
     * same method(s) of the {@link Transmorph_Plugin_Interface}.
     *
     * @param Transmorph_Plugin_Processor_Interface $plugin An instance of a 
     * plugin.
     * 
     * @return void
     * 
     * @throws Transmorph_Exception If an instance of the same plugin class is 
     * already in the stack.
     */
    public function appendPlugin(Transmorph_Plugin_Interface $plugin)
    {
        if ($plugin instanceof Transmorph_Plugin_Processor_Interface)
        {
            $this->_pluginStack->appendPlugin($plugin);
        }
        elseif ($plugin instanceof Transmorph_Plugin_Reader_Interface)
        {
            $this->reader->appendPlugin($plugin);
        }
        elseif ($plugin instanceof Transmorph_Plugin_Writer_Interface)
        {
            $this->writer->appendPlugin($plugin);
        }
        else
        {
            throw new Transmorph_Exception('Unsupported plugin interface');
        }
    }

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
    public function removePlugin($pluginClassName)
    {
        try
        {
            $this->_pluginStack->removePlugin($pluginClassName);
        }
        catch (Transmorph_Exception $exc)
        {
            try
            {
                $this->reader->removePlugin($pluginClassName);
            }
            catch (Transmorph_Exception $exc)
            {
                $this->writer->removePlugin($pluginClassName);
            }
        }
    }

    /**
     * Property handling.
     *
     * @param string $name Property name.
     * 
     * @return mixed Property value.
     * 
     * @codeCoverageIgnore Trivial code.
     */
    public function __get($name)
    {
        switch ($name)
        {
            case 'reader':
                return $this->_reader;
                break;
            case 'writer':
                return $this->_writer;
                break;
            case 'input':
                return $this->_copyInput($this->_input);
                break;
            default:
                throw new OutOfRangeException(__CLASS__ . ' has no ' . $name . ' property-read.');
                break;
        }
    }

    /**
     * This has 2 interests : making a deep copy of the input so plugins cannot 
     * violate its encapsulation, and inhibiting savage propagation of 
     * 'resources' variables.
     *
     * @param mixed $input An input structure implied in a transfromation.
     * 
     * @return mixed 
     */
    protected function _copyInput($input)
    {
        return unserialize(serialize($input));
    }

    /**
     * Firing plugins.
     *
     * @param string[] $map passed to plugin.
     * 
     * @return string[] back from plugin.
     * 
     * @see Transmorph_Plugin_Processor_Interface::processMap()
     */
    protected function _fireProcessMap(array $map)
    {
        foreach ($this->_pluginStack as $plugin)
        {
            /* @var $plugin Transmorph_Plugin_Processor_Interface */
            $map = $plugin->processMap($this, $map);
        }

        return $map;
    }

    /**
     * Firing plugins.
     * 
     * @param Transmorph_Rule $rule passed to plugin.
     * 
     * @return Transmorph_Rule back from plugin.
     * 
     * @see Transmorph_Plugin_Processor_Interface::processRule()
     */
    protected function _fireProcessRule(Transmorph_Rule $rule)
    {
        foreach ($this->_pluginStack as $plugin)
        {
            /* @var $plugin Transmorph_Plugin_Processor_Interface */
            $rule = $plugin->processRule($this, $rule);
        }

        return $rule;
    }

    /**
     * Firing plugins.
     *
     * @param mixed $callback passed to plugin.
     * 
     * @return mixed back from plugin.
     * 
     * @see Transmorph_Plugin_Processor_Interface::processCallback()
     */
    protected function _fireProcessCallback($callback)
    {
        foreach ($this->_pluginStack as $plugin)
        {
            /* @var $plugin Transmorph_Plugin_Processor_Interface */
            $callback = $plugin->processCallback($this, $callback);
        }

        return $callback;
    }

    /**
     * Firing plugins.
     *
     * @param string[] $callbackParams passed to plugin.
     * 
     * @return string[] back for plugin.
     * 
     * @see Transmorph_Plugin_Processor_Interface::processCallbackParams()
     */
    protected function _fireProcessCallbackParams(array $callbackParams)
    {
        foreach ($this->_pluginStack as $plugin)
        {
            /* @var $plugin Transmorph_Plugin_Processor_Interface */
            $callbackParams = $plugin->processCallbackParams($this, $callbackParams);
        }

        return $callbackParams;
    }

}