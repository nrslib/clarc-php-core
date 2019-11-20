<?php


namespace nrslib\ClarcTests\UseCase\Create;


use nrslib\Clarc\UseCases\UseCase\Create\UseCaseCreateOutputData;
use nrslib\Clarc\UseCases\UseCase\Create\UseCaseCreatePresenterInterface;

class UseCaseCreateTestPresenter implements UseCaseCreatePresenterInterface
{
    /**
     * @var UseCaseCreateOutputData
     */
    public $outputData;

    function output(UseCaseCreateOutputData $outputData)
    {
        $this->outputData = $outputData;
    }
}