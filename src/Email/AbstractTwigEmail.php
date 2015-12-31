<?php

namespace Undine\Email;

/**
 * Abstract email generator that has access to Twig environment.
 */
abstract class AbstractTwigEmail implements EmailInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
}
