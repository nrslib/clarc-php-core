<?php


namespace nrslib\Clarc\SourceFileBuilder\Presenter;


use nrslib\Clarc\UseCases\Commons\Ds\SourceFileData;
use nrslib\Clarc\UseCases\UseCase\Create\UseCaseSchema;

interface PresenterSourceFileBuilderInterface
{
    function build(UseCaseSchema $schema, string $namespace, string $outputDataName, string $outputPortName, string $outputPortNamespace): SourceFileData;
}