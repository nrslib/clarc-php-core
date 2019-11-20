<?php


namespace nrslib\Clarc\UseCases\UseCase\Create;


use nrslib\Cfg\ClassRenderer;
use nrslib\Cfg\InterfaceRenderer;
use nrslib\Cfg\Meta\Classes\ClassMeta;
use nrslib\Cfg\Meta\Interfaces\InterfaceMeta;
use nrslib\Cfg\Meta\Words\AccessLevel;
use nrslib\Clarc\SourceFileBuilder\Controller\ControllerSourceFileBuilderInterface;
use nrslib\Clarc\SourceFileBuilder\Presenter\PresenterSourceFileBuilderInterface;
use nrslib\Clarc\UseCases\Commons\Ds\NameRule;
use nrslib\Clarc\UseCases\Commons\Ds\SourceFileData;

class UseCaseCreateInteractor
{
    /**
     * @var UseCaseCreatePresenterInterface
     */
    private $presenter;

    /**
     * @var ClassRenderer
     */
    private $classRenderer;

    /**
     * @var InterfaceRenderer
     */
    private $interfaceRenderer;

    /**
     * @var ControllerSourceFileBuilderInterface;
     */
    private $controllerSourceFileBuilder;

    /**
     * @var PresenterSourceFileBuilderInterface
     */
    private $presenterSourceFileBuilder;

    public function __construct(UseCaseCreatePresenterInterface $presenter, ClassRenderer $classRenderer, InterfaceRenderer $interfaceRenderer, ControllerSourceFileBuilderInterface $controllerSourceFileBuilder, PresenterSourceFileBuilderInterface $presenterSourceFileBuilder)
    {
        $this->presenter = $presenter;
        $this->classRenderer = $classRenderer;
        $this->interfaceRenderer = $interfaceRenderer;
        $this->controllerSourceFileBuilder = $controllerSourceFileBuilder;
        $this->presenterSourceFileBuilder = $presenterSourceFileBuilder;
    }

    public function handle(UseCaseCreateInputData $inputData)
    {
        $controllerSourceFile = $this->controllerSourceFileBuilder->build(
            $inputData->name,
            $inputData->namespace->controllerNamespace,
            $this->getInputPortName($inputData),
            $inputData->namespace->inputPortNamespace);
        $interactorData = $this->createInteractorSourceFileData($inputData);
        $inputDataSourceFile = $this->createInputDataSourceFileData($inputData);
        $inputPortSourceFile = $this->createInputPortSourceFileData($inputData);
        $outputPortSourceFile = $this->createOutputPortSourceFileData($inputData);
        $outputDataSourceFile = $this->createOutputDataSourceFileData($inputData);
        $presenterSourceFile = $this->presenterSourceFileBuilder->build(
            $inputData->name,
            $inputData->namespace->presenterNamespace,
            $this->getOutputDataName($inputData),
            $this->getOutputPortName($inputData),
            $inputData->namespace->outputPortNamespace);

        $outputData = new UseCaseCreateOutputData($controllerSourceFile, $inputPortSourceFile, $interactorData, $inputDataSourceFile, $outputPortSourceFile, $outputDataSourceFile, $presenterSourceFile);

        $this->presenter->output($outputData);
    }

    private function createInputPortSourceFileData(UseCaseCreateInputData $inputData)
    {
        $name = $this->getInputPortName($inputData);

        $clazz = new InterfaceMeta($name, $inputData->namespace->inputPortNamespace);

        $clazz->getInterfaceSetting();

        $clazz->getMethodsSetting()
            ->addMethod('handle', function ($methodDefinition) use ($inputData) {
                $inputDataName = $this->getInputDataName($inputData);
                $methodDefinition->addArgument('inputData', $inputDataName);
            });

        $contents = $this->interfaceRenderer->render($clazz);

        return new SourceFileData($name, $contents);
    }

    private function createInteractorSourceFileData(UseCaseCreateInputData $inputData): SourceFileData
    {
        $interactorName = $this->getInteractorName($inputData);
        $inputDataClassName = $this->getInputDataName($inputData);
        $presenterInterfaceName = $this->getOutputPortName($inputData);

        $clazz = new ClassMeta($interactorName, $inputData->namespace->interactorNamespace);
        $clazz->setupClass()
            ->setConstructor(function ($constructorDefinition) use ($presenterInterfaceName) {
                $constructorDefinition->addArgument('outputPort', $presenterInterfaceName)
                    ->addBody('$this->outputPort = $outputPort;');
            })
            ->addUse($this->getInputPortName($inputData, true))
            ->addUse($this->getInputDataName($inputData, true))
            ->addUse($this->getOutputPortName($inputData, true))
            ->addImplement($this->getInputPortName($inputData));
        $clazz->setupFields()
            ->addField('outputPort', $presenterInterfaceName);
        $clazz->setupMethods()
            ->addMethod('handle', function ($methodDefinition) use ($inputDataClassName) {
                $methodDefinition->setAccessLevel(AccessLevel::public())
                    ->addArgument('inputData', $inputDataClassName)
                    ->addBody('// TODO: Implement handle() method.');
            });

        $contents = $this->classRenderer->render($clazz);

        return new SourceFileData($interactorName, $contents);
    }

    private function createInputDataSourceFileData(UseCaseCreateInputData $inputData): SourceFileData
    {
        $inputDataName = $this->getInputDataName($inputData);

        return $this->createDataStructureSourceFileData($inputDataName, $inputData->namespace->inputPortNamespace, $inputData->inputDataFields);
    }

    public function createOutputPortSourceFileData(UseCaseCreateInputData $inputData): SourceFileData
    {
        $name = $this->getOutputPortName($inputData);

        $clazz = new InterfaceMeta($name, $inputData->namespace->outputPortNamespace);
        $clazz->getMethodsSetting()
            ->addMethod('output', function ($methodDefinition) use ($inputData) {
                $outputDataName = $this->getOutputDataName($inputData);
                $methodDefinition->addArgument('outputData', $outputDataName);
            });

        $contents = $this->interfaceRenderer->render($clazz);

        return new SourceFileData($name, $contents);
    }

    public function createOutputDataSourceFileData(UseCaseCreateInputData $inputData): SourceFileData
    {
        $outputDataName = $this->getOutputDataName($inputData);

        return $this->createDataStructureSourceFileData($outputDataName, $inputData->namespace->outputPortNamespace, $inputData->outputDataFields);
    }

    public function createDataStructureSourceFileData(string $className, string $namespace, array $fields): SourceFileData
    {
        $clazz = new ClassMeta($className, $namespace);

        $fieldSetup = $clazz->setupFields();
        $methodSetup = $clazz->setupMethods();
        foreach ($fields as $field) {
            $fieldName = $field->name;
            $fieldSetup->addField($field->name, $field->type);
            $getterName = 'get' . ucwords($fieldName);
            $methodSetup->addMethod($getterName, function ($methodDefinition) use ($field, $fieldName) {
                $methodDefinition->setReturnType($field->type)
                    ->addBody('return $this->' . $fieldName . ';');
            });
        }

        $contents = $this->classRenderer->render($clazz);

        return new SourceFileData($className, $contents);
    }

    private function getInputPortName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $this->applyNameRule($inputData->name . 'InputPort', $inputData->codingRule->interfaceRule),
            $inputData->namespace->inputPortNamespace,
            $appendNamespace);
    }

    private function getInteractorName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $this->applyNameRule($inputData->name, $inputData->nameRule),
            $inputData->namespace->interactorNamespace,
            $appendNamespace);
    }

    private function getInputDataName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $inputData->name . 'InputData',
            $inputData->namespace->inputPortNamespace,
            $appendNamespace);
    }

    private function getOutputDataName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $inputData->name . 'OutputData',
            $inputData->namespace->outputPortNamespace,
            $appendNamespace);
    }

    private function getOutputPortName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $this->applyNameRule($inputData->name . 'OutputPort', $inputData->codingRule->interfaceRule),
            $inputData->namespace->outputPortNamespace,
            $appendNamespace);
    }

    private function adjustObjectName(string $name, string $prefix, bool $add)
    {
        if ($add) {
            return $prefix . '\\' . $name;
        } else {
            return $name;
        }
    }

    private function applyNameRule($name, NameRule $rule): string
    {
        if ($rule->isPrefix) {
            return $rule->text . $name;
        } else {
            return $name . $rule->text;
        }
    }
}
