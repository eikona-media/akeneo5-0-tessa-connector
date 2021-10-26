<?php

namespace Eikona\Tessa\SharedCatalogsBundle\EventSubscriber;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\WorkOrganization\Workflow\Component\Model\PublishedProductInterface;
use Akeneo\Tool\Component\FileStorage\Exception\FileRemovalException;
use Akeneo\Tool\Component\FileStorage\Exception\FileTransferException;
use Akeneo\Tool\Component\StorageUtils\StorageEvents;
use Eikona\Tessa\SharedCatalogsBundle\Exceptions\LinkedAttributeDifferentConfigException;
use Eikona\Tessa\SharedCatalogsBundle\Exceptions\LinkedAttributeOnWrongLevelException;
use Eikona\Tessa\SharedCatalogsBundle\Services\AssetManager;
use Eikona\Tessa\SharedCatalogsBundle\Services\ProductAndProductModelProcessor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class SharedCatalogsSubscriber implements EventSubscriberInterface
{
    protected ProductAndProductModelProcessor $processor;
    protected AssetManager $assetManager;

    /**
     * @param AssetManager                    $assetManager
     * @param ProductAndProductModelProcessor $processor
     */
    public function __construct(
        AssetManager $assetManager,
        ProductAndProductModelProcessor $processor
    ) {
        $this->processor = $processor;
        $this->assetManager = $assetManager;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            StorageEvents::PRE_SAVE => [
                ['onPreSave', 91]
            ]
        ];
    }

    /**
     * @param GenericEvent $event
     *
     * @throws FileRemovalException
     * @throws FileTransferException
     * @throws LinkedAttributeDifferentConfigException
     * @throws LinkedAttributeOnWrongLevelException
     */
    public function onPreSave(GenericEvent $event): void
    {
        if (!$this->assetManager->isSyncEnabled()) {
            return;
        }

        if ($event->hasArgument('skip_tessa_assets_sync')
            && $event->getArgument('skip_tessa_assets_sync') === true) {
            return;
        }

        $subject = $event->getSubject();

        if ($subject instanceof ProductInterface && !$subject instanceof PublishedProductInterface) {
            $this->processor->processProduct($subject);
        }

        if ($subject instanceof ProductModelInterface) {
            $this->processor->processProductModel($subject);
        }
    }
}
