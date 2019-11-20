<?php


namespace nrslib\Clarc\SourceFileBuilder\Presenter;


use nrslib\Clarc\UseCases\Commons\Ds\SourceFileData;

interface PresenterSourceFileBuilderInterface
{
    function build(string $aName, string $namespace, string $outputDataName, string $outputPortName, string $outputPortNamespace): SourceFileData;
}