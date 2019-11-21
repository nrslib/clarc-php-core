<?php


namespace nrslib\Clarc\SourceFileBuilder\Controller;


use nrslib\Clarc\UseCases\Commons\Ds\SourceFileData;
use nrslib\Clarc\UseCases\UseCase\Create\UseCaseSchema;

interface ControllerSourceFileBuilderInterface
{
    function build(UseCaseSchema $schema, string $namespace, string $inputPortName, string $inputPortNamespace): SourceFileData;
}