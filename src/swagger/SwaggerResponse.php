<?php
/**
 * Created by PhpStorm.
 * User: Dionisio Gabriel Rigo de Souza Machado - https://github.com/diomac
 * Date: 04/03/2019
 * Time: 14:17
 */

namespace Diomac\API\swagger;


use Diomac\API\Response;
use Exception;
use JsonSerializable;

class SwaggerResponse implements JsonSerializable
{
    /**
     * @var string $code
     */
    private $code;
    /**
     * @var string $description
     */
    private $description;
    /**
     * @var object $schema
     */
    private $schema;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return object
     */
    public function getSchema(): ?object
    {
        return $this->schema;
    }

    /**
     * @param object $schema
     */
    public function setSchema(object $schema = null): void
    {
        $this->schema = $schema;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     * @throws Exception
     */
    public function jsonSerialize(): array
    {
        Response::jsonField('description', $this->getDescription());
        Response::jsonField('schema', $this->getSchema());

        return Response::jsonSerialize($this);
    }
}
