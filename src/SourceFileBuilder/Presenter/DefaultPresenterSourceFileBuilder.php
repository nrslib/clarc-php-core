<?php


namespace nrslib\Clarc\SourceFileBuilder\Presenter;


use nrslib\Cfg\ClassRenderer;
use nrslib\Cfg\Meta\Classes\ClassMeta;
use nrslib\Cfg\Meta\Words\AccessLevel;
use nrslib\Clarc\UseCases\Commons\Ds\SourceFileData;

class DefaultPresenterSourceFileBuilder implements PresenterSourceFileBuilderInterface
{
    /**
     * @var ClassRenderer
     */
    private $renderer;

    /**
     * DefaultPresenterSourceFileBuilder constructor.
     * @param ClassRenderer $render
     */
    public function __construct(ClassRenderer $render)
    {
        $this->renderer = $render;
    }

    function build(string $aName, string $namespace, string $outputDataName, string $outputPortName, string $outputPortNamespace): SourceFileData
    {
        $name = $aName . 'Presenter';

        $clazz = new ClassMeta($name, $namespace);

        $clazz->setupClass()
            ->addUse($outputPortNamespace . '\\' . $outputDataName)
            ->addUse($outputPortNamespace . '\\' . $outputPortName)
            ->addImplement($outputPortName);

        $clazz->setupMethods()
            ->addMethod('output', function ($methodDefinition) use ($outputDataName) {
                $methodDefinition->setAccessLevel(AccessLevel::public())
                    ->addArgument('outputData', $outputDataName)
                    ->addBody('// TODO: Implement output() method.');
            });

        $contents = $this->renderer->render($clazz);

        return new SourceFileData($name, $contents);
    }
}