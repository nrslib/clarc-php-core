<?php


namespace nrslib\ClarcTests\UseCase\Create;


use nrslib\Cfg\ClassRenderer;
use nrslib\Cfg\InterfaceRenderer;
use nrslib\Clarc\SourceFileBuilder\Controller\DefaultControllerSourceFileBuilder;
use nrslib\Clarc\SourceFileBuilder\Presenter\DefaultPresenterSourceFileBuilder;
use nrslib\Clarc\UseCases\Commons\Ds\TypeAndName;
use nrslib\Clarc\UseCases\UseCase\Create\UseCaseCreateInputData;
use nrslib\Clarc\UseCases\UseCase\Create\UseCaseCreateInteractor;
use nrslib\Clarc\UseCases\UseCase\Create\UseCaseCreateNamespaceData;

class UseCaseCreateTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {

    }

    public function testMyTest(): void
    {
        $classRenderer = new ClassRenderer();
        $interfaceRenderer = new InterfaceRenderer();
        $presenter = new UseCaseCreateTestPresenter();
        $controllerBuilder = new DefaultControllerSourceFileBuilder($classRenderer);
        $presenterBuilder = new DefaultPresenterSourceFileBuilder($classRenderer);
        $interactor = new UseCaseCreateInteractor($presenter, $classRenderer, $interfaceRenderer, $controllerBuilder, $presenterBuilder);

        $namespace = 'nrslib\\Test';

        $inputData = new UseCaseCreateInputData(
            new UseCaseCreateNamespaceData($namespace . '\\A', $namespace . '\\B', $namespace . '\\C', $namespace . '\\D', $namespace . '\\E'),
            "Test",
            [
                new TypeAndName('string', 'inputStringField'),
            ],
            [
                new TypeAndName('string', 'outputStringField'),
            ]
        );
        $interactor->handle($inputData);
        $outputData = $presenter->outputData;

        file_put_contents('c:\\test\\TestController.php', $outputData->getControllerSourceFile()->getContents());
        file_put_contents('c:\\test\\TestInputPortInterface.php', $outputData->getInputPortSourceFile()->getContents());
        file_put_contents('c:\\test\\TestInteractor.php', $outputData->getInteractorSourceFile()->getContents());
        file_put_contents('c:\\test\\TestInputData.php', $outputData->getInputDataSourceFile()->getContents());
        file_put_contents('c:\\test\\TestOutputPortInterface.php', $outputData->getOutputPortSourceFile()->getContents());
        file_put_contents('c:\\test\\TestOutputData.php', $outputData->getOutputDataSourceFile()->getContents());
        file_put_contents('c:\\test\\TestPresenter.php', $outputData->getPresenterSourceFile()->getContents());

        echo $outputData->getControllerSourceFile()->getContents();
        echo $outputData->getInputPortSourceFile()->getContents();
        echo $outputData->getInteractorSourceFile()->getContents();
        echo $outputData->getInputDataSourceFile()->getContents();
        echo $outputData->getOutputPortSourceFile()->getContents();
        echo $outputData->getOutputDataSourceFile()->getContents();
        echo $outputData->getPresenterSourceFile()->getContents();
    }
}