<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transmorph_Processor
 *
 * @author fabrice
 * 
 * @property-read TransmorphPluginInterface[] $plugins
 * @property-read Transmorph_Reader $reader
 * @property-read Transmorph_Writer $writer
 * @property-read mixed $input Read-only. Always null except for plugins fired by {@link run()}
 * 
 * @todo plugin registering interface
 * 
 */
class Transmorph_Processor
{
    const REGEX_CONST = '#^\\\.+$#';

    /**
     * Les données d'entrée de la transformation.
     *
     * @var mixed
     */
    protected $_input;

    /**
     *
     * @var Transmorph_Reader
     */
    protected $_reader;

    /**
     *
     * @var Transmorph_Writer
     */
    protected $_writer;

    /**
     *
     * @var TransmorphPluginInterface[]
     */
    protected $_plugins;

    public function __construct()
    {
        $this->_reader = new Transmorph_Reader($this);
        $this->_writer = new Transmorph_Writer($this);

        $this->_plugins = array();
    }

    /**
     * @todo Etudier la possibilité de passer soit un fichier avec la $map,
     * soit la $map directement en string[].
     *
     * @param mixed $input
     * @param string $filePath
     * 
     * @return mixed 
     */
    public function run($input, $filePath)
    {
        $this->_input = $input;
        /**
         * @todo Vérifications sur la validité du filePath
         */
        $map = $this->handleFile($filePath);
        $map = $this->_fireProcessMap($map);

        $output = null;
        foreach ($map as $line)
        {
            $this->handleLine($output, $input, $line);
        }
        
        $this->_input = null;
        return $output;
    }

    /**
     * Ouvre $_filePath, et stock les lignes dans un tableau de string
     * 
     * @param string $filePath A path to a map ENTRY file.
     *
     * @return string[] le tableau avec les lignes du fichier.
     */
    public function handleFile($filePath)
    {
        return file($filePath, FILE_IGNORE_NEW_LINES);
    }

    /**
     * Traite une ligne de fichier d'entrée
     *
     * @param mixed $output
     * @param mixed $input
     * @param string $line 
     */
    public function handleLine(&$output, $input, $line)
    {
        $tLine = new Transmorph_Line($line);
        $tLine = $this->_fireProcessLine($tLine);
        $this->_writer->feed($output, $tLine->target, $this->handleMapEntry($input, $tLine->source));
    }

    /**
     * Recursif
     * 
     * Parse et évalue récursivement un ENTRY pour en dégager d'autres ENTRY, 
     * ou des PATTERN ou CONST à évaluer.
     * 
     * La récursivité s'arrête aux PATTERN ou CONST.
     *
     * @param mixed $input
     * @param string $mapEntry ENTRY
     * 
     * @return mixed 
     */
    public function handleMapEntry($input, $mapEntry)
    {
        if ($this->isConst($mapEntry))
        {
            /**
             * @todo dedicated function
             */
            return substr($mapEntry, 1);
        }
        elseif ($this->isPath($mapEntry))
        {
            return $this->_reader->query($input, $mapEntry);
        }
        else
        {
            $callback = $this->findCallback($mapEntry);
            $callback = $this->_fireProcessCallback($callback);
            if ($callback === '')
            {
                throw new Transmorph_Exception('Illegal Entry');
            }
            else
            {
                $paramEntries = $this->findCallbackParams($mapEntry);
                $paramEntries = $this->_fireProcessCallbackParams($paramEntries);
                
                $inputArray = array();
                for ($i = 0; $i < count($paramEntries); $i++)
                {
                    $inputArray[] = $input;
                }
                return call_user_func_array($callback, array_map(array($this, __FUNCTION__), $inputArray, $paramEntries));
            }
        }
    }

    /**
     * Vérifie si un ENTRY est une CONST
     *
     * @param string $mapEntry
     * @return boolean
     */
    public function isConst($mapEntry)
    {
        return preg_match(self::REGEX_CONST, $mapEntry) == 1;
    }

    /**
     * Vérifie si un ENTRY est un PATH
     *
     * @param string $mapEntry
     * @return boolean
     */
    public function isPath($mapEntry)
    {
        $pathRegex = '#^((/[^\./\\\]+)|(\.[^\./\\\]+))*(/|\.)?$#';
        return preg_match($pathRegex, $mapEntry) == 1;
    }

    /**
     * Récupère le nom du Callback dans le $mapEntry
     * Renvoie une chaine vide si il n'y pas de callback
     *
     * @param string $mapEntry un ENTRY
     * 
     * @return string FUNCTION
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
     * Récupère si elles existent les expressions formatées des paramètres du 
     * callback dans le $mapEntry.
     * Renvoie un tableau numérique avec les expressions dans l'ordre où elles
     * étaient écrites.
     *
     * @param string $mapEntry ENTRY
     * 
     * @return string[] des ENTRY|PATTERN
     */
    public function findCallbackParams($mapEntry)
    {
        $parameters = array();

        /*
         * Avec cette regex, on capture le bloc (*,*,*) du callback
         */
        $entryRegex = '#^[^/\.\(]+\((.*)\)$#';

        $matches = array();
        preg_match($entryRegex, $mapEntry, $matches);
        if (isset($matches[1]))
        {
            $paramString = $matches[1];
            //Index du tableau de chaînes renvoyé
            $index = 0;
            /*
             * Profondeur de parenthèses pendant le parsing.
             * Permet de bypasser les ',' encapsulés dans des formes de ce
             * type :
             * (*,(a,b),*)
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
     * Property handling.
     * 
     * @codeCoverageIgnore Trivial
     *
     * @param string $name
     * 
     * @return mixed 
     */
    public function __get($name)
    {
        switch ($name)
        {
            case 'plugins':
                return $this->_plugins;
                break;
            case 'reader':
                return $this->_reader;
                break;
            case 'writer':
                return $this->_writer;
                break;
            case 'input':
                /**
                 *  @todo this should return a recursive copy/clone to avoid breaking encapsulation.
                 */
                return $this->_input;
                break;
            default:
                throw new OutOfRangeException(__CLASS__ . ' has no ' . $name . ' property-read.');
                break;
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string[] $map An ENTRY array to process.
     * 
     * @return string[] The processed array of ENTRYs.
     */
    protected function _fireProcessMap(array $map)
    {
        foreach ($this->_plugins as $plugin)
        {
            /* @var $plugin TransmorphPluginInterface */
            $map = $plugin->processMap($this, $map);
        }

        return $map;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param Transmorph_Line $line A LINE to process.
     * 
     * @return Transmorph_Line The processed LINE.
     */
    protected function _fireProcessLine(Transmorph_Line $line)
    {
        foreach ($this->_plugins as $plugin)
        {
            /* @var $plugin TransmorphPluginInterface */
            $line = $plugin->processLine($this, $line);
        }

        return $line;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $callback A callback name to process.
     * 
     * @return string The processes callback name.
     */
    protected function _fireProcessCallback($callback)
    {
        foreach ($this->_plugins as $plugin)
        {
            /* @var $plugin TransmorphPluginInterface */
            $callback = $plugin->processCallback($this, $callback);
        }

        return $callback;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string[] $callbackParams An ENTRY array to process.
     * 
     * @return string[] The processed ENTRY array.
     */
    protected function _fireProcessCallbackParams(array $callbackParams)
    {
        foreach ($this->_plugins as $plugin)
        {
            /* @var $plugin TransmorphPluginInterface */
            $callbackParams = $plugin->processCallbackParams($this, $callbackParams);
        }

        return $callbackParams;
    }

}