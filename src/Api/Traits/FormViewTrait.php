<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Api\Traits;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait FormViewTrait
{
    protected function createFormView(bool $isValidRequest, FormInterface $form, array $customData = []): Response
    {
        $errors = [];
        foreach ($form->getErrors(true, false) as $item) {
            foreach ($item as $error) {
                $errors[] = [
                    'name' => $error->getOrigin()->getName(),
                    'property_path' => (string)$error->getOrigin()->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }
        }

        return $this->handleView($this->view(
            array_merge([
                'isValidRequest' => $isValidRequest,
                'errors' => $errors,
                'success' => $isValidRequest && empty($errors),
            ], $customData),
            $isValidRequest ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        ));
    }

    protected function runNormalFormAction(
        Request $request,
        string $formClass,
        object $entity, bool
        $merge = false,
        callable $serializeFunc = null
    ): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];

        if ($merge && !is_null($serializeFunc)) {
            $data = array_merge($serializeFunc($entity), $data);
        }

        $form = $this->createForm($formClass, $entity, ['csrf_protection' => false]);
        $form->submit($data);
        $isValid = $form->isSubmitted() && $form->isValid();

        if ($isValid) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();
        }

        return $this->createFormView($isValid, $form);
    }
}
