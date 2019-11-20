<?php


namespace nrslib\Clarc\SourceFileBuilder\Controller;


use nrslib\Clarc\UseCases\Commons\Ds\SourceFileData;

interface ControllerSourceFileBuilderInterface
{
    function build(string $name, string $namespace, string $inputPortName, string $inputPortNamespace): SourceFileData;
}