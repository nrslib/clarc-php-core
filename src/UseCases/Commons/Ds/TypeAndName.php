<?php


namespace nrslib\Clarc\UseCases\Commons\Ds;


/**
 * Class TypeAndName
 * @package nrslib\Clarc\UseCases\Commons\Ds
 */
class TypeAndName
{
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $name;

    /**
     * TypeAndName constructor.
     * @param string $type
     * @param string $name
     */
    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }
}