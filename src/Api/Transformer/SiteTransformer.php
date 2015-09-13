<?php

namespace Undine\Api\Transformer;

use League\Fractal\ParamBag;
use Undine\Model\Site;
use Undine\Model\Site\SiteState;
use Undine\Model\SiteExtension;
use Undine\Model\SiteUpdate;
use Undine\Model\User;

class SiteTransformer extends AbstractTransformer
{
    protected $availableIncludes = [
        'user',
        'state',
        'modules',
        'themes',
        'coreUpdates',
        'moduleUpdates',
        'themeUpdates',
    ];

    public function transform(Site $site)
    {
        return [
            'uid' => $site->getUid(),
            'url' => (string)$site->getUrl(),
        ];
    }

    public function includeUser(Site $site)
    {
        return $this->item($site->getUser(), $this->transformers->get(User::class));
    }

    public function includeState(Site $site)
    {
        return $this->item($site->getSiteState(), $this->transformers->get(SiteState::class));
    }

    public function includeModules(Site $site)
    {
        return $this->collection($this->filterExtensions($site->getSiteExtensions(), SiteExtension::TYPE_MODULE), $this->transformers->get(SiteExtension::class));
    }

    public function includeThemes(Site $site)
    {
        return $this->collection($this->filterExtensions($site->getSiteExtensions(), SiteExtension::TYPE_THEME), $this->transformers->get(SiteExtension::class));
    }

    public function includeCoreUpdates(Site $site)
    {
        return $this->collection($this->filterUpdates($site->getSiteUpdates(), SiteUpdate::TYPE_CORE), $this->transformers->get(SiteUpdate::class));
    }

    public function includeModuleUpdates(Site $site, ParamBag $paramBag = null)
    {
        return $this->collection($this->filterUpdates($site->getSiteUpdates(), SiteUpdate::TYPE_MODULE), $this->transformers->get(SiteUpdate::class));
    }

    public function includeThemeUpdates(Site $site)
    {
        return $this->collection($this->filterUpdates($site->getSiteUpdates(), SiteUpdate::TYPE_THEME), $this->transformers->get(SiteUpdate::class));
    }

    /**
     * @param SiteExtension[] $extensions
     * @param string          $type
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
     * @param string       $type
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
