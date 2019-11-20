<?php


namespace nrslib\Clarc\UseCases\UseCase\Create;


class UseCaseFileWritePresenter implements UseCaseCreatePresenterInterface
{

    /**
     * @var string
     */
    private $rootDirectoryFullPath;

    public function __construct(string $rootDirectoryFullPath)
    {
        $this->rootDirectoryFullPath = $rootDirectoryFullPath;
    }

    function output(UseCaseCreateOutputData $outputData)
    {

    }
}