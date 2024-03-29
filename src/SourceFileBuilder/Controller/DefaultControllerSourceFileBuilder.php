<?php


namespace nrslib\Clarc\SourceFileBuilder\Controller;


use nrslib\Cfg\ClassRenderer;
use nrslib\Cfg\Meta\Classes\ClassMeta;
use nrslib\Cfg\Meta\Words\AccessLevel;
use nrslib\Clarc\UseCases\Commons\Ds\SourceFileData;
use nrslib\Clarc\UseCases\UseCase\Create\UseCaseSchema;

class DefaultControllerSourceFileBuilder implements ControllerSourceFileBuilderInterface
{
    /**
     * @var ClassRenderer
     */
    public $renderer;

    /**
     * DefaultControllerSourceFileBuilder constructor.
     * @param ClassRenderer $renderer
     */
    public function __construct(ClassRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function build(UseCaseSchema $schema, string $namespace, string $inputPortName, string $inputPortNamespace): SourceFileData
    {
        $name = $schema->fullName() . 'Controller';

        $clazz = new ClassMeta($name, $namespace);
        $clazz->setupClass()
            ->addUse($inputPortNamespace . '\\' . $inputPortName);
        $clazz->getFieldsSetting()
            ->addField('inputPort', $inputPortName);
        $clazz->getClassSetting()
            ->setConstructor(function($constructorDefinition) use ($inputPortName) {
                $constructorDefinition->addArgument('inputPort', $inputPortName)
                    ->addBody('$this->inputPort = $inputPort;');
            });

        $clazz->getMethodsSetting()
            ->addMethod('interact', function ($methodDefinition) {
                $methodDefinition->setAccessLevel(AccessLevel::public())
                    ->addBody('// TODO: Implement interact() method.');
            });

        $contents = $this->renderer->render($clazz);

        return new SourceFileData($name, $contents);
    }
}