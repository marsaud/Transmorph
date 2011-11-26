<?php

/**
 * @package Transmorph
 */

/**
 * Description of Transmorph_Reader
 *
 * @package Transmorph
 * @subpackage Reader
 * 
 * @author fabrice
 */
class Transmorph_Reader
{

    /**
     *
     * @var Transmorph_Processor
     */
    protected $_t;

    public function __construct($transmorph = null)
    {
        if ($transmorph !== null)
        {
            $this->_setTransmorph($transmorph);
        }
    }

    protected function _setTransmorph(Transmorph_Processor $t)
    {
        $this->_t = $t;
    }

    /**
     * Explore récursivement une structure de données d'entrée composées
     * indifférement d'objets, de tableaux numériques ou associatifs et de
     * scalaires, selon un chemin de recherche formaté(PATH).
     *
     * @param mixed $input Une structure de données
     * @param string $path Un PATH
     * 
     * @return mixed la valeur ou sous-structure trouvée en résolvant $path
     */
    public function query($input, $path)
    {
        $result = null;

        if ($path === '' || $path == '/' || $path == '.')
        {
            /**
             * Si on est arrivés au bout d'un PATH, on peut renvoyer la valeur courante.
             * 
             * Je laisse un / ou . optionel à la manière du / optionel à la fin
             * d'un nom de dossier sous UNIX pour une commande comme 'cd' par exemple.
             * 
             * @todo Dans les ENRTY, je songe à tout exprimer en / et à laisser le .
             * dans les 'target PATH' seulement pour forcer l'usage de stdClass.
             */
            $result = $input;
        }
        else
        {
            $matches = array();
            $found = preg_match('#^((/[^\./\\\]+)|(\.[^\./\\\]+))((\.|/).*)*$#', $path, $matches);

            if ($found !== 1)
            {
                throw new Transmorph_Reader_Exception('Unresolvable Transmorph PATH');
            }

            $nextNode = $matches[1];

            $nextNode = $this->_fireProcessInputPathNode($nextNode);

            $remainingPath = isset($matches[4]) ? $matches[4] : '';
            $key = substr($nextNode, 1);

            if ($nextNode[0] == '/') // tableau
            {
                if (!isset($input[$key]))
                {
                    throw new Transmorph_Reader_Exception('PATH references no value');
                }
                $result = $this->query($input[$key], $remainingPath);
            }
            elseif ($nextNode[0] == '.') // objet
            {
                if (!isset($input->$key))
                {
                    throw new Transmorph_Reader_Exception('PATH references no value');
                }
                $result = $this->query($input->$key, $remainingPath);
            }
        }

        return $result;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $pathNode
     * @return string 
     */
    protected function _fireProcessInputPathNode($pathNode)
    {
        if ($this->_t !== null)
        {
            $plugins = $this->_t->plugins;
            foreach ($plugins as $plugin)
            {
                $pathNode = $plugin->processInputPathNode($this->_t, $pathNode);
            }
        }

        return $pathNode;
    }

}