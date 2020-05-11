<?php

namespace EzSystems\TweetFieldTypeBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer that transforms array into comma-separated string and vice versa
 */
class StringToArrayTransformer implements DataTransformerInterface
{
    public function transform($array)
    {
        if ($array === null) {
            return '';
        }

        return implode(',', $array);
    }

    public function reverseTransform($string)
    {
        if (empty($string)) {
            return [];
        }

        return explode(',', $string);
    }
}