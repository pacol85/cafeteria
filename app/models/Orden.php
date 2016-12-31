<?php

class Orden extends \Phalcon\Mvc\Model
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
    public $numero;

    /**
     *
     * @var string
     */
    public $cliente;

    /**
     *
     * @var string
     */
    public $identificacion;

    /**
     *
     * @var string
     */
    public $otros;

    /**
     *
     * @var string
     */
    public $estado;

    /**
     *
     * @var string
     */
    public $hinicio;

    /**
     *
     * @var string
     */
    public $hfinal;
    
    /**
     *
     * @var integer
     */
    public $prioridad;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Item', 'orden', array('alias' => 'Item'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'orden';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Orden[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Orden
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
