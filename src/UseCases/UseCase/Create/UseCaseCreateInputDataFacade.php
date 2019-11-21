<?php


namespace nrslib\Clarc\UseCases\UseCase\Create;


use nrslib\Clarc\UseCases\Commons\Ds\NameRule;

class UseCaseCreateInputDataFacade
{
    /**
     * @var UseCaseCreateInputData
     */
    public $original;

    public function getOriginal(): UseCaseCreateInputData
    {
        return $this->original;
    }

    public function getInputPortName(): string
    {
        return $this->applyNameRule($this->original->schema . 'InputPort', $this->original->codingRule->interfaceRule);
    }

    public function getInteractorName(): string
    {
        return $this->applyNameRule($this->original->schema, $this->original->interactorNameRule);
    }

    public function getInputDataName(): string
    {
        return $this->original->schema . 'InputData';
    }

    public function getOutputDataName(): string
    {
        return $this->original->schema . 'OutputData';
    }

    public function getOutputPortName(): string
    {
        return $this->applyNameRule($this->original->schema . 'OutputPort', $this->original->codingRule->interfaceRule);
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