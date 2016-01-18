<?php

namespace Undine\Api\Transformer;

use Undine\Model\SiteState;
use Undine\Model\SiteExtension;
use Undine\Model\SiteUpdate;

class SiteStateTransformer extends AbstractTransformer
{
    /**
     * Getter for availableIncludes.
     *
     * @return array
     */
    public function getAvailableIncludes()
    {
        return [
            'modules',
            'themes',
            'coreUpdates',
            'moduleUpdates',
            'themeUpdates',
        ];
    }

    public function transform(SiteState $siteState)
    {
        return [
            'connected' => $siteState->getStatus() === SiteState::STATUS_CONNECTED,
            'drupalVersion' => $siteState->getDrupalVersion(),
        ];
    }

    public function includeModules(SiteState $siteState)
    {
        return $this->collection($this->filterExtensions($siteState->getSiteExtensions(), SiteExtension::TYPE_MODULE), $this->transformers->get(SiteExtension::class));
    }

    public function includeThemes(SiteState $siteState)
    {
        return $this->collection($this->filterExtensions($siteState->getSiteExtensions(), SiteExtension::TYPE_THEME), $this->transformers->get(SiteExtension::class));
    }

    public function includeCoreUpdates(SiteState $siteState)
    {
        return $this->collection($this->filterUpdates($siteState->getSiteUpdates(), SiteUpdate::TYPE_CORE), $this->transformers->get(SiteUpdate::class));
    }

    public function includeModuleUpdates(SiteState $siteState)
    {
        return $this->collection($this->filterUpdates($siteState->getSiteUpdates(), SiteUpdate::TYPE_MODULE), $this->transformers->get(SiteUpdate::class));
    }

    public function includeThemeUpdates(SiteState $siteState)
    {
        return $this->collection($this->filterUpdates($siteState->getSiteUpdates(), SiteUpdate::TYPE_THEME), $this->transformers->get(SiteUpdate::class));
    }

    /**
     * @param SiteExtension[] $extensions
     * @param string $type
     *
     * @return SiteExtension[]
     */
    private function filterExtensions($extensions, $type)
    {
        return array_filter($extensions, function (SiteExtension $extension) use ($type) {
            return $extension->getType() === $type;
        });
    }

    /**
     * @param SiteUpdate[] $updates
     * @param string $type
     *
     * @return SiteUpdate[]
     */
    private function filterUpdates($updates, $type)
    {
        return array_filter($updates, function (SiteUpdate $update) use ($type) {
            return $update->getType() === $type;
        });
    }
}
