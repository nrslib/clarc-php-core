<?php


namespace nrslib\Clarc\UseCases\Commons\Ds;


/**
 * Class NameRule
 * @package nrslib\Clarc\UseCases\Commons\Ds
 */
class NameRule
{
    /**
     * @var bool
     */
    public $isPrefix;
    /**
     * @var string
     */
    public $text;

    /**
     * NameRule constructor.
     * @param bool $isPrefix
     * @param string $text
     */
    public function __construct($isPrefix, $text)
    {
        $this->isPrefix = $isPrefix;
        $this->text = $text;
    }
}