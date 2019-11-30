<?php


namespace nrslib\Clarc\UseCases\UseCase\Create;


use nrslib\Cfg\ClassRenderer;
use nrslib\Cfg\InterfaceRenderer;
use nrslib\Cfg\Meta\Classes\ClassMeta;
use nrslib\Cfg\Meta\Interfaces\InterfaceMeta;
use nrslib\Cfg\Meta\Words\AccessLevel;
use nrslib\Clarc\SourceFileBuilder\Controller\ControllerSourceFileBuilderInterface;
use nrslib\Clarc\SourceFileBuilder\Controller\DefaultControllerSourceFileBuilder;
use nrslib\Clarc\SourceFileBuilder\Presenter\PresenterSourceFileBuilderInterface;
use nrslib\Clarc\UseCases\Commons\Ds\NameRule;
use nrslib\Clarc\UseCases\Commons\Ds\SourceFileData;
use nrslib\Clarc\UseCases\Commons\Ds\TypeAndName;

/**
 * Class UseCaseCreateInteractor
 * @package nrslib\Clarc\UseCases\UseCase\Create
 */
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

    /**
     * UseCaseCreateInteractor constructor.
     * @param UseCaseCreatePresenterInterface $presenter
     * @param ClassRenderer $classRenderer
     * @param InterfaceRenderer $interfaceRenderer
     * @param ControllerSourceFileBuilderInterface|null $controllerSourceFileBuilder
     * @param PresenterSourceFileBuilderInterface|null $presenterSourceFileBuilder
     */
    public function __construct(UseCaseCreatePresenterInterface $presenter, ClassRenderer $classRenderer, InterfaceRenderer $interfaceRenderer, ControllerSourceFileBuilderInterface $controllerSourceFileBuilder = null, PresenterSourceFileBuilderInterface $presenterSourceFileBuilder = null)
    {
        $this->presenter = $presenter;
        $this->classRenderer = $classRenderer;
        $this->interfaceRenderer = $interfaceRenderer;
        $this->controllerSourceFileBuilder = !is_null($controllerSourceFileBuilder) ? $controllerSourceFileBuilder : new DefaultControllerSourceFileBuilder($classRenderer);
        $this->presenterSourceFileBuilder = !is_null($presenterSourceFileBuilder) ? $presenterSourceFileBuilder : new DefaultControllerSourceFileBuilder($classRenderer);
    }

    /**
     * @param UseCaseCreateInputData $inputData
     */
    public function handle(UseCaseCreateInputData $inputData)
    {
        $controllerSourceFile = $this->controllerSourceFileBuilder->build(
            $inputData->schema,
            $inputData->namespace->controllerNamespace,
            $this->getInputPortName($inputData),
            $inputData->namespace->inputPortNamespace);
        $interactorData = $this->createInteractorSourceFileData($inputData);
        $inputDataSourceFile = $this->createInputDataSourceFileData($inputData);
        $inputPortSourceFile = $this->createInputPortSourceFileData($inputData);
        $outputPortSourceFile = $this->createOutputPortSourceFileData($inputData);
        $outputDataSourceFile = $this->createOutputDataSourceFileData($inputData);
        $presenterSourceFile = $this->presenterSourceFileBuilder->build(
            $inputData->schema,
            $inputData->namespace->presenterNamespace,
            $this->getOutputDataName($inputData),
            $this->getOutputPortName($inputData),
            $inputData->namespace->outputPortNamespace);
        $viewModelSourceFile = $this->createViewModelSourceFile($inputData, $inputData->outputDataFields);

        $outputData = new UseCaseCreateOutputData(
            $controllerSourceFile,
            $inputPortSourceFile,
            $interactorData,
            $inputDataSourceFile,
            $outputPortSourceFile,
            $outputDataSourceFile,
            $presenterSourceFile,
            $viewModelSourceFile);

        $this->presenter->output($outputData);
    }

    /**
     * @param UseCaseCreateInputData $inputData
     * @return SourceFileData
     */
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

    /**
     * @param UseCaseCreateInputData $inputData
     * @return SourceFileData
     */
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

    /**
     * @param UseCaseCreateInputData $inputData
     * @return SourceFileData
     */
    private function createInputDataSourceFileData(UseCaseCreateInputData $inputData): SourceFileData
    {
        $inputDataName = $this->getInputDataName($inputData);

        return $this->createDataStructureSourceFileData($inputDataName, $inputData->namespace->inputPortNamespace, $inputData->inputDataFields);
    }

    /**
     * @param UseCaseCreateInputData $inputData
     * @return SourceFileData
     */
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

    /**
     * @param UseCaseCreateInputData $inputData
     * @return SourceFileData
     */
    public function createOutputDataSourceFileData(UseCaseCreateInputData $inputData): SourceFileData
    {
        $outputDataName = $this->getOutputDataName($inputData);

        return $this->createDataStructureSourceFileData($outputDataName, $inputData->namespace->outputPortNamespace, $inputData->outputDataFields);
    }

    /**
     * @param string $className
     * @param string $namespace
     * @param TypeAndName[] $fields
     * @param callable|null $setupConstructor
     * @return SourceFileData
     */
    private function createDataStructureSourceFileData(string $className, string $namespace, array $fields, callable $classPredicate = null): SourceFileData
    {
        $clazz = new ClassMeta($className, $namespace);

        $classSetup = $clazz->setupClass();

        if (is_null($classPredicate)) {
            $classSetup->setConstructor(function ($definition) use ($fields) {
                foreach ($fields as $field) {
                    $definition->addArgument($field->name, $field->type)
                        ->addBody($this->assignFieldCode($field->name));
                }
            });
        } else {
            $classPredicate($classSetup);
        }

        $fieldSetup = $clazz->setupFields();
        $methodSetup = $clazz->setupMethods();
        foreach ($fields as $field) {
            if ($field->hasNamespace()) {
                $classSetup->addUse($field->completeType());
            }

            $fieldName = $field->name;
            $fieldSetup->addField($field->name, $field->type);
            $getterName = 'get' . ucwords($fieldName);
            $methodSetup->addMethod($getterName, function ($methodDefinition) use ($field, $fieldName) {
                $methodDefinition->setReturnType($field->type)
                    ->addBody('return $this->' . $fieldName . ';')
                    ->setAccessLevel(AccessLevel::public());
            });
        }

        $contents = $this->classRenderer->render($clazz);

        return new SourceFileData($className, $contents);
    }

    /**
     * @param UseCaseCreateInputData $inputData
     * @param TypeAndName[] $fields
     * @return SourceFileData
     */
    private function createViewModelSourceFile(UseCaseCreateInputData $inputData, array $fields): SourceFileData
    {
        return $this->createDataStructureSourceFileData(
            $inputData->schema->fullName() . 'ViewModel',
            $inputData->namespace->viewModelNamespace,
            $fields,
            function ($classSetup) use ($inputData, $fields) {
                $classSetup->addUse($inputData->namespace->viewModelNamespace . '\\' . $this->getOutputDataName($inputData));
                $classSetup->setConstructor(function($constructorDefinition) use ($inputData, $fields) {
                    $constructorDefinition->addArgument('source', $this->getOutputDataName($inputData));
                    foreach ($fields as $field) {
                        $constructorDefinition->addBody($this->assignFieldCode($field->name, 'source->get' . ucfirst($field->name) . '()'));
                    }
                });
            });
    }

    /**
     * @param UseCaseCreateInputData $inputData
     * @param bool $appendNamespace
     * @return string
     */
    private function getInputPortName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $this->applyNameRule($inputData->schema->fullName() . 'InputPort', $inputData->codingRule->interfaceRule),
            $inputData->namespace->inputPortNamespace,
            $appendNamespace);
    }

    /**
     * @param UseCaseCreateInputData $inputData
     * @param bool $appendNamespace
     * @return string
     */
    private function getInteractorName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $this->applyNameRule($inputData->schema->fullName(), $inputData->interactorNameRule),
            $inputData->namespace->interactorNamespace,
            $appendNamespace);
    }

    /**
     * @param UseCaseCreateInputData $inputData
     * @param bool $appendNamespace
     * @return string
     */
    private function getInputDataName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $inputData->schema->fullName() . 'InputData',
            $inputData->namespace->inputPortNamespace,
            $appendNamespace);
    }

    /**
     * @param UseCaseCreateInputData $inputData
     * @param bool $appendNamespace
     * @return string
     */
    private function getOutputDataName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $inputData->schema->fullName() . 'OutputData',
            $inputData->namespace->outputPortNamespace,
            $appendNamespace);
    }

    /**
     * @param UseCaseCreateInputData $inputData
     * @param bool $appendNamespace
     * @return string
     */
    private function getOutputPortName(UseCaseCreateInputData $inputData, bool $appendNamespace = false): string
    {
        return $this->adjustObjectName(
            $this->applyNameRule($inputData->schema->fullName(). 'OutputPort', $inputData->codingRule->interfaceRule),
            $inputData->namespace->outputPortNamespace,
            $appendNamespace);
    }

    /**
     * @param string $name
     * @param string $prefix
     * @param bool $add
     * @return string
     */
    private function adjustObjectName(string $name, string $prefix, bool $add)
    {
        if ($add) {
            return $prefix . '\\' . $name;
        } else {
            return $name;
        }
    }

    /**
     * @param $name
     * @param NameRule $rule
     * @return string
     */
    private function applyNameRule($name, NameRule $rule): string
    {
        if ($rule->isPrefix) {
            return $rule->text . $name;
        } else {
            return $name . $rule->text;
        }
    }

    private function assignFieldCode(string $fieldName, string $argument = null): string
    {
        if (is_null($argument)) {
            return '$this->' . $fieldName . ' = $' . $fieldName . ';';
        } else {
            return '$this->' . $fieldName . ' = $' . $argument . ';';
        }
    }
}
