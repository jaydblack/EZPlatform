<?php


namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;


use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

class LegacyConverter implements Converter
{
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        $storageFieldValue->dataText = json_encode($value->data);
        $storageFieldValue->sortKeyString = $value->sortKey;
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $fieldValue->data = json_decode($value->dataText, true);
        $fieldValue->sortKey = $value->sortKeyString;
    }

    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        $storageDef->dataText1 = json_encode(
            $fieldDef->fieldTypeConstraints->validators['TweetValueValidator']['authorList']
        );
    }

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        $authorList = json_decode($storageDef->dataText1);
        if (!empty($authorList)) {
            $fieldDef->fieldTypeConstraints->validators = [
                'TweetValueValidator' => [
                    'authorList' => $authorList
                ],
            ];
        }
    }
    public function getIndexColumn()
    {
        return 'sort_key_string';
    }
}