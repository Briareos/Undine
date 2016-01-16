<?php

namespace Undine\Api\Exception;

use Symfony\Component\Form\FormInterface;

/**
 * The form validation of a command has failed.
 */
class CommandInvalidException extends ApiException
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @param string        $type Form type name (fully qualified class name).
     * @param FormInterface $form Form object that holds validation data.
     */
    public function __construct($type, FormInterface $form)
    {
        $this->type = $type;
        $this->form = $form;
        parent::__construct('The command validation has failed.');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
