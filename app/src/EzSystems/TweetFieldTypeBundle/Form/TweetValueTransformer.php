<?php
namespace EzSystems\TweetFieldTypeBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\Core\FieldType\Value;

class TweetValueTransformer implements DataTransformerInterface
{
    /**
     * @var FieldType
     */
    private $fieldType;

    public function __construct(FieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        return (string) $value;
    }

    public function reverseTransform($value)
    {
        return $this->fieldType->fromHash($value);
    }
}