<?php

class Item extends \Phalcon\Mvc\Model
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
    public $orden;

    /**
     *
     * @var string
     */
    public $menu;

    /**
     *
     * @var integer
     */
    public $cantidad;

    /**
     *
     * @var string
     */
    public $cambios;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('orden', 'Orden', 'id', array('alias' => 'Orden'));
        $this->belongsTo('menu', 'Menu', 'id', array('alias' => 'Menu'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'item';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Item[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Item
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
