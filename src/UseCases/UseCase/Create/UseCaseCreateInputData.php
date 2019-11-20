<?php


namespace nrslib\Clarc\UseCases\UseCase\Create;


use nrslib\Clarc\UseCases\Commons\Ds\CodingRule;
use nrslib\Clarc\UseCases\Commons\Ds\NameRule;
use nrslib\Clarc\UseCases\Commons\Ds\TypeAndName;

class UseCaseCreateInputData
{
    /**
     * @var UseCaseCreateNamespaceData
     */
    public $namespace;

    /**
     * @var string
     */
    public $name;

    /**
     * @var TypeAndName[]
     */
    public $inputDataFields;

    /**
     * @var TypeAndName[]
     */
    public $outputDataFields;

    /**
     * @var NameRule
     */
    public $nameRule;

    /**
     * @var CodingRule
     */
    public $codingRule;

    /**
     * UseCaseCreateInputData constructor.
     * @param UseCaseCreateNamespaceData $namespace
     * @param string $name
     * @param TypeAndName[] $inputDataFields
     * @param TypeAndName[] $outputDataFields
     * @param NameRule|null $nameRule
     * @param CodingRule|null $codingRule
     */
    public function __construct(
        UseCaseCreateNamespaceData $namespace,
        string $name,
        array $inputDataFields,
        array $outputDataFields,
        NameRule $nameRule = null,
        CodingRule $codingRule = null)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->inputDataFields = $inputDataFields;
        $this->outputDataFields = $outputDataFields;
        $this->nameRule = !is_null($nameRule) ? $nameRule : new NameRule(false, 'Interactor');
        $this->codingRule = !is_null($codingRule) ? $codingRule : CodingRule::default();
    }
}