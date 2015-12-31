<?php

namespace Undine\Twig\Node;

use Twig_Compiler;

class GlobNode extends \Twig_Node
{
    private $files = [];

    /**
     * Constructor.
     *
     * The nodes are automatically made available as properties ($this->node).
     * The attributes are automatically made available as array items ($this['name']).
     *
     * @param array  $files
     * @param array  $nodes      An array of named nodes
     * @param array  $attributes An array of attributes (should not be nodes)
     * @param int    $lineno     The line number
     * @param string $tag        The tag name associated with the Node
     */
    public function __construct(array $files, array $nodes = [], array $attributes = [], $lineno = 0, $tag = null)
    {
        $this->files = $files;

        parent::__construct($nodes, $attributes, $lineno, $tag);
    }


    public function compile(Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        foreach ($this->files as $file) {
            $compiler
                ->write("\$context['glob_path'] = ")
                ->repr($file)
                ->write(";\n")
                ->subcompile($this->getNode('body'));
        }
    }
}
