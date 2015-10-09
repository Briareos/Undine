<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Command\ExtensionDownloadFromUrlCommand;
use Undine\Api\Command\ExtensionUpdateCommand;
use Undine\Api\Result\ExtensionDownloadFromUrlResult;
use Undine\Api\Result\ExtensionUpdateResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\Api;
use Undine\Model\Site;
use Undine\Oxygen\Action\DatabaseListMigrationsAction;
use Undine\Oxygen\Action\DatabaseRunMigrationAction;
use Undine\Oxygen\Action\ExtensionDownloadFromUrlAction;
use Undine\Oxygen\Action\ExtensionDownloadUpdateFromUrlAction;
use Undine\Oxygen\Action\ExtensionUpdateAction;
use Undine\Oxygen\Reaction\DatabaseListMigrationsReaction;
use Undine\Oxygen\Reaction\ExtensionDownloadFromUrlReaction;
use Undine\Oxygen\Reaction\ExtensionDownloadUpdateFromUrlReaction;
use Undine\Oxygen\Reaction\ExtensionUpdateReaction;

class ExtensionController extends AppController
{
    /**
     * @Route("extension.downloadFromUrl", name="api-extension.downloadFromUrl")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @Api("api__extension_download_from_url")
     */
    public function downloadFromUrlAction(Site $site, ExtensionDownloadFromUrlCommand $command)
    {
        /** @var ExtensionDownloadFromUrlReaction $reaction */
        $reaction = $this->oxygenClient->send($site, new ExtensionDownloadFromUrlAction($command->getUrl()));

        return new ExtensionDownloadFromUrlResult();
    }

    /**
     * @Route("extension.update", name="api-project.update")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @Api("api__extension_update")
     */
    public function downloadUpdateFromUrlAction(Site $site, ExtensionUpdateCommand $command)
    {
        $updates = $site->getSiteUpdates();
        if (!isset($updates[$command->getExtension()])) {
            throw $this->createNotFoundException('The extension could not be found.');
        }
        $extension = $command->getExtension();
        $url       = $updates[$command->getExtension()]->getRecommendedDownloadLink();

        $reaction = $this->oxygenClient
            ->sendAsync($site, new ExtensionDownloadUpdateFromUrlAction($extension, $url))
            ->then(function (ExtensionDownloadUpdateFromUrlReaction $reaction) use ($site, $extension) {
                return $this->oxygenClient->sendAsync($site, new ExtensionUpdateAction($extension));
            })
            ->then(function (ExtensionUpdateReaction $reaction) use ($site) {
                return $this->oxygenClient->sendAsync($site, new DatabaseListMigrationsAction());
            })
            ->then(function (DatabaseListMigrationsReaction $reaction) use ($site) {
                $migrationGenerator = function () use ($site, $reaction) {
                    foreach ($reaction->getMigrations() as $migration) {
                        yield $this->oxygenClient->sendAsync($site, new DatabaseRunMigrationAction($migration['module'], $migration['number'], $migration['dependencyMap']));
                    }
                };

                return \GuzzleHttp\Promise\each_limit_all($migrationGenerator(), 1);
            })
            ->wait();

        return new ExtensionUpdateResult();
    }
}
