<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DatabaseListMigrationsReaction extends AbstractReaction
{
    /**
     * @var array
     */
    private $migrations = [];

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        foreach ($data as $row) {
            $this->migrations[] = $this->resolve($row);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['module', 'number', 'dependencyMap']);
        $resolver->setAllowedTypes('module', 'string');
        $resolver->setAllowedTypes('number', 'int');
        $resolver->setAllowedTypes('dependencyMap', 'array');
    }

    /**
     * Each migration has 3 elements - module:string, number:int, dependencyMap:array.
     *
     * @return array
     */
    public function getMigrations()
    {
        return $this->migrations;
    }
}
