<?php

namespace Undine\Api\Exception;

use Symfony\Component\Form\FormInterface;

class CommandInvalidException extends ApiException
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @param FormInterface $form Form object that holds validation data.
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
        parent::__construct('The command validation has failed.');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
