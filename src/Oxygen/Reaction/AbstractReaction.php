<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Undine\Oxygen\Reaction\Exception\ReactionException;
use Undine\Oxygen\Reaction\Exception\ReactionMalformedException;

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
        $this->data = $this->getResolver()->resolve($data);
    }

    /**
     * @return OptionsResolver
     *
     * @throws ReactionException
     */
    private function getResolver()
    {
        $class = get_called_class();

        if (!isset(self::$resolvers[$class])) {
            self::$resolvers[$class] = new OptionsResolver();
            try {
                $this->configureOptions(self::$resolvers[$class]);
            } catch (ExceptionInterface $e) {
                throw new ReactionMalformedException('The reaction data did not pass our expected format.', 0, $e);
            }
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
