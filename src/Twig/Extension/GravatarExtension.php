<?php

namespace Undine\Twig\Extension;

use Symfony\Component\OptionsResolver\OptionsResolver;

class GravatarExtension extends \Twig_Extension
{
    const GRAVATAR_BASE_URL = 'https://gravatar.com/avatar/%s?%s';

    /** @var OptionsResolver */
    private $resolver;

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setDefaults([
                'size'    => 80,
                'default' => 'retro',
                'rating'  => 'g',
            ])
            ->setAllowedTypes('size', 'integer')
            ->setAllowedTypes('default', 'string')
            ->setAllowedTypes('rating', 'string')
            ->setAllowedValues('size', function ($value) {
                return $value >= 1 && $value <= 2048;
            })
            ->setAllowedValues('rating', ['g', 'pg', 'r', 'x']);
    }

    /**
     * Returns the gravatar image url for specified email.
     * Options is an optional array which accepts size, default and rating.
     *
     * @param string $email
     * @param array  $options
     *
     * @return string The url
     */
    public function getGravatarUrl($email, array $options = [])
    {
        $options = $this->resolver->resolve($options);
        $image   = sprintf(self::GRAVATAR_BASE_URL, md5($email), http_build_query($options));

        return $image;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('gravatar_url', [$this, 'getGravatarUrl']),
        ];
    }

    public function getName()
    {
        return 'gravatar';
    }
}