<?php

namespace App\Exception;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FormValidationException extends HttpException implements AdditionalDetailsExceptionInterface
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * Constructor.
     */
    public function __construct(FormInterface $form)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, 'Validation error.');
        $this->form = $form;
    }

    /**
     * Get form.
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrors(): array
    {
        return $this->getFormErrors($this->form);
    }

    /**
     * Get form errors.
     */
    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            // if ($cause = $error->getCause()) {
            //     $message = $cause->getInvalidMessage();
            //     // TODO: Remove this.
            //     if (!$message) {
            //         $message = $cause->getMessage();
            //     }
            // } else {
            $message = $error->getMessage();
            // }
            $errors[] = ['message' => $message];
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getFormErrors($childForm)) {
                    foreach ($childErrors as $childError) {
                        $errors[] = [
                            'field' => $childForm->getName(),
                            'message' => $childError['message'],
                        ];
                    }
                }
            }
        }

        return $errors;
    }
}
