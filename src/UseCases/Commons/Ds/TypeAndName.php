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
     * @var string|null
     */
    public $namespace;

    /**
     * TypeAndName constructor.
     * @param string $type
     * @param string $name
     * @param string|null $namespace
     */
    public function __construct(string $type, string $name, string $namespace = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->namespace = $namespace;
    }

    public function hasNamespace(): bool
    {
        return !is_null($this->namespace);
    }

    public function completeType()
    {
        return $this->namespace . '\\' . $this->type;
    }
}