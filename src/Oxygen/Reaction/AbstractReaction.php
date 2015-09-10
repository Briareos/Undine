<?php

namespace Undine\Oxygen\Reaction;

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
    public function setData(array $data)
    {
        $this->data = $this->resolve($data);
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    protected function resolve($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf('The resolving data is expected to be an array, "%s" given.', gettype($data)));
        }

        return $this->getResolver()->resolve($data);
    }

    /**
     * @return OptionsResolver
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
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
    }
}
