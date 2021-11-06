<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Controller\RequestValidator;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

final class CartRemoveItemsValidator
{
    public static function isValid(array $items): bool
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($items, [
            new Assert\Type('array'),
            new Assert\NotBlank(),
            new Assert\All([
                new Assert\Collection([
                    'fields' => [
                        'cartItemId' => new Assert\Required([new Assert\Uuid()]),
                        'decreaseQuantity' => new Assert\Required([new Assert\Positive()]),
                    ],
                ])
            ]),
        ]);

        return 0 === count($violations);
    }
}
