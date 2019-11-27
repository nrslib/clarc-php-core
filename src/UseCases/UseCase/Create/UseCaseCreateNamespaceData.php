<?php


namespace nrslib\Clarc\UseCases\UseCase\Create;


/**
 * Class UseCaseCreateNameSpaceData
 * @package nrslib\Clarc\UseCases\UseCase\Create
 */
class UseCaseCreateNamespaceData
{
    /**
     * @var string
     */
    public $controllerNamespace;
    /**
     * @var string
     */
    public $inputPortNamespace;
    /**
     * @var string
     */
    public $interactorNamespace;
    /**
     * @var string
     */
    public $outputPortNamespace;
    /**
     * @var string
     */
    public $presenterNamespace;
    /**
     * @var string
     */
    public $viewModelNamespace;

    /**
     * UseCaseCreateNameSpaceData constructor.
     * @param string $controllerNamespace
     * @param string $inputPortNamespace
     * @param string $interactorNamespace
     * @param string $outputPortNamespace
     * @param string $presenterNamespace
     * @param string $viewModelNamespace
     */
    public function __construct(string $controllerNamespace, string $inputPortNamespace, string $interactorNamespace, string $outputPortNamespace, string $presenterNamespace, string $viewModelNamespace)
    {
        $this->controllerNamespace = $controllerNamespace;
        $this->inputPortNamespace = $inputPortNamespace;
        $this->interactorNamespace = $interactorNamespace;
        $this->outputPortNamespace = $outputPortNamespace;
        $this->presenterNamespace = $presenterNamespace;
        $this->viewModelNamespace = $viewModelNamespace;
    }
}