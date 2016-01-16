<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractReaction implements ReactionInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var OptionsResolver[]
     */
    private static $resolvers = [];

    /**
     * {@inheritdoc}
     */
    final public function setData(array $data)
    {
        $this->data = $this->getResolver()->resolve($data);
    }

    /**
     * @return OptionsResolver
     *
     * @throws ExceptionInterface
     */
    private function getResolver()
    {
        $class = get_called_class();

        if (!isset(self::$resolvers[$class])) {
            self::$resolvers[$class] = new OptionsResolver();
            $this->configureOptions(self::$resolvers[$class]);
        }

        return self::$resolvers[$class];
    }

    /**
     * Implement this method to define response format.
     * By default, all response values are rejected.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
    }
}
