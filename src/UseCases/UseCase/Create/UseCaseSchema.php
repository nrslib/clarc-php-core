<?php


namespace nrslib\Clarc\UseCases\UseCase\Create;


class UseCaseSchema
{
    public $categoryName;
    public $usecaseName;

    public function __construct(string $categoryName, string $usecaseName)
    {
        $this->categoryName = $categoryName;
        $this->usecaseName = $usecaseName;
    }

    public function fullName()
    {
        return $this->categoryName . $this->usecaseName;
    }
}