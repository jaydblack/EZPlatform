<?php

namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\FieldType\Nameable;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface;

class Type extends FieldType implements Nameable
{
    const TWITTER_URL_REGEX = '#^https?://twitter\.com/([^/]+)/status/([0-9]+)$#';

    protected $validatorConfigurationSchema = [
        'TweetValueValidator' => [
            'authorList' => [
                'type' => 'array',
                'default' => []
            ]
        ]
    ];

    /** @var TwitterClientInterface */
    private $twitterClient;

    public function __construct(TwitterClientInterface $twitterClient)
    {
        $this->twitterClient = $twitterClient;
    }

    public function toPersistenceValue(SPIValue $value)
    {
        if ($value === null) {
            return new PersistenceValue([
                'data' => null,
                'externalData' => null,
                'sortKey' => null,
            ]);
        }

        if ($value->contents === null) {
            $value->contents = $this->twitterClient->getEmbed($value->url);
        }

        return new PersistenceValue([
            'data' => $this->toHash($value),
            'sortKey' => $this->getSortInfo($value),
        ]);
    }

    public function fromPersistenceValue(PersistenceValue $fieldValue)
    {
        if ($fieldValue->data === null) {
            return $this->getEmptyValue();
        }

        return new Value($fieldValue->data);
    }

    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return [
            'url' => $value->url,
            'authorUrl' => $value->authorUrl,
            'contents' => $value->contents
        ];
    }

    public function getFieldName(SPIValue $value , FieldDefinition $fieldDefinition, $languageCode)
    {
        return preg_replace(
            self::TWITTER_URL_REGEX,
            '$1-$2',
            (string)$value->url
        );
    }

    public function getEmptyValue()
    {
        return new Value;
    }

    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $errors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $errors;
        }

        // Tweet URL validation
        if (!preg_match('#^https?://twitter.com/([^/]+)/status/[0-9]+$#', $fieldValue->url, $m)) {
            $errors[] = new ValidationError(
                'Invalid Twitter status URL %url%',
                null,
                ['%url%' => $fieldValue->url]
            );

            return $errors;
        }

        $author = $m[1];
        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        if (!$this->isAuthorApproved($author, $validatorConfiguration)) {
            $errors[] = new ValidationError(
                'Twitter user %user% is not in the approved author list',
                null,
                ['%user%' => $m[1]]
            );
        }

        return $errors;
    }

    public function validateValidatorConfiguration($validatorConfiguration)
    {
        $validationErrors = [];

        foreach ($validatorConfiguration as $validatorIdentifier => $constraints) {
            // Report unknown validators
            if ($validatorIdentifier !== 'TweetValueValidator') {
                $validationErrors[] = new ValidationError("Validator '$validatorIdentifier' is unknown");
                continue;
            }

            // Validate arguments from TweetValueValidator
            foreach ($constraints as $name => $value) {
                switch ($name) {
                    case 'authorList':
                        if (!is_array($value)) {
                            $validationErrors[] = new ValidationError('Invalid authorList argument');
                        }
                        foreach ($value as $authorName) {
                            if (!preg_match('/^[a-z0-9_]{1,15}$/i', $authorName)) {
                                $validationErrors[] = new ValidationError('Invalid twitter username');
                            }
                        }
                        break;
                    default:
                        $validationErrors[] = new ValidationError("Validator parameter '$name' is unknown");
                }
            }
        }

        return $validationErrors;
    }

    public function getFieldTypeIdentifier()
    {
        return 'eztweet';
    }

    public function getName(SPIValue $value)
    {
        return 'Tweet';
    }

    protected function createValueFromInput($inputValue)
    {
        if (is_string($inputValue)) {
            $inputValue = new Value(['url' => $inputValue]);
        }

        return $inputValue;
    }

    protected function checkValueStructure(CoreValue $value)
    {
        if (!is_string($value->url)) {
            throw new InvalidArgumentType(
                '$value->url',
                'string',
                $value->url
            );
        }
    }

    protected function getSortInfo(CoreValue $value)
    {
        return (string)$value->url;
    }

    private function isAuthorApproved($author, $validatorConfiguration)
    {
        return !isset($validatorConfiguration['TweetValueValidator'])
            || empty($validatorConfiguration['TweetValueValidator']['authorList'])
            || in_array($author, $validatorConfiguration['TweetValueValidator']['authorList']);
    }
}