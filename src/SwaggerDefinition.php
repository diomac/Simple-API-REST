<?php
/**
 * Created by PhpStorm.
 * User: Dionisio Gabriel Rigo de Souza Machado - https://github.com/diomac
 * Date: 04/03/2019
 * Time: 14:51
 */

namespace Diomac\API;


abstract class SwaggerDefinition implements \JsonSerializable
{
    /**
     * @var string $type
     */
    private $type;
    /**
     * @var string[] $required
     */
    private $required;
    /**
     * @var SwaggerDefinitionProperty[]
     */
    private $properties;
    /**
     * @var array $allOf
     */
    private $allOf;

    /**
     * SwaggerDefinition constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $child = get_called_class();
        try {
            $ann = new Annotation($child);
        } catch (\ReflectionException $ex) {
            throw new \Exception('Can\'t read ' . $child . ' information.');
        }

        preg_match('/@swaggerType ([A-Za-z]+)/', $ann->getDocComment(), $match);

        if ($match) {
            $this->type = $match[1];
        } else {
            throw new \Exception('@swaggerType bad configured in "' . $ann->getName() . '"');
        }

        $properties = $ann->getProperties();

        foreach ($properties as $p) {
            $this->properties[$p->getName()] = new SwaggerDefinitionProperty($p->getDocComment());
            $match = null;
            preg_match('/@swaggerRequired/', $p->getDocComment(), $match);
            if ($match) {
                $this->required[] = $p->getName();
            }
        }

        $match = null;

        preg_match_all('/@swaggerAllOf ([A-Za-z0-9\/]*)/', $ann->getDocComment(), $match);

        if ($match[0]) {
            $this->allOf = [];
            foreach ($match[1] as $m) {
                $this->allOf[] = ['$ref' => '#/definitions/' . $m];
            }
            $complement = [];
            if ($this->required) {
                $complement['required'] = $this->required;
            }
            if ($this->properties) {
                $complement['properties'] = $this->properties;
            }
            if ($complement) {
                $this->allOf[] = $complement;
            }
        }
    }


    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getRequired(): ?array
    {
        return $this->required;
    }

    /**
     * @return SwaggerDefinitionProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getAllOf(): array
    {
        return $this->allOf;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $p = [];
        $p['type'] = 'getType';

        if ($this->allOf) {
            $p['allOf'] = 'getAllOf';
        } else {
            if ($this->required) {
                $p['required'] = 'getRequired';
            }
            $p['properties'] = 'getProperties';
        }

        return Response::jsonSerialize($this, $p);
    }
}
