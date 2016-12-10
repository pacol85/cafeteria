<?php

class Seccion extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var string
     */
    public $nombre;

    /**
     *
     * @var string
     */
    public $desc;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Menu', 'seccion', array('alias' => 'Menu'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'seccion';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Seccion[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Seccion
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
