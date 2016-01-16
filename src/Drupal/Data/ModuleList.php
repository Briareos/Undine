<?php

namespace Undine\Drupal\Data;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

class ModuleList
{
    /**
     * @var ModuleListItem[]
     */
    private $modules;

    /**
     * @param ModuleListItem[] $modules
     */
    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @return ModuleListItem[]
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @param string      $slug
     * @param string|null $package Not mandatory, but it's highly recommended to rely on package names.
     *
     * @return ModuleListItem|null
     */
    public function find($slug, $package = null)
    {
        foreach ($this->modules as $module) {
            if ($package === null || $package === $module->getPackage()) {
                if ($module->getSlug() === $slug) {
                    return $module;
                }
            }
        }

        return null;
    }

    /**
     * @param Form $form
     *
     * @return ModuleList
     */
    public static function createFromForm(Form $form)
    {
        $node = new Crawler($form->getFormNode());
        // Example: <input type="checkbox" id="edit-modules-other-oxygen-enable" name="modules[Other][oxygen][enable]" value="1" class="form-checkbox" />
        $inputs = $node->filter('input[type="checkbox"][id^="edit-modules-"]');
        $modules = [];
        foreach ($inputs as $input) {
            /** @var \DOMElement $input */
            if (!$input->hasAttribute('name')) {
                continue;
            }
            $enabled = $input->hasAttribute('checked');
            $match = preg_match('{^modules\[([^\]]+)\]\[([^\]]+)\]\[enable\]$}', $input->getAttribute('name'), $matches);
            if (!$match) {
                continue;
            }
            list(, $package, $slug) = $matches;
            $modules[] = new ModuleListItem($package, $slug, $enabled);
        }

        return new self($modules);
    }
}
