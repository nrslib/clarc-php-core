<?php


namespace nrslib\Clarc\UseCases\Commons\Ds;


class CodingRule
{
    /**
     * @var NameRule
     */
    public $interfaceRule;

    public function __construct(NameRule $interfaceRule)
    {
        $this->interfaceRule = $interfaceRule;
    }

    /**
     * @return CodingRule
     */
    public static function default(): CodingRule
    {
        $interfaceRule = new NameRule(false, 'Interface');
        return new CodingRule($interfaceRule);
    }
}