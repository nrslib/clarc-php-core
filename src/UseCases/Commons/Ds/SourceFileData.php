<?php


namespace nrslib\Clarc\UseCases\Commons\Ds;


class SourceFileData
{
    /**
     * @var string
     */
    private $className;
    /**
     * @var string
     */
    private $contents;

    /**
     * OutputSourceFileData constructor.
     * @param string $className
     * @param string $contents
     */
    public function __construct(string $className, string $contents)
    {
        $this->className = $className;
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }
}