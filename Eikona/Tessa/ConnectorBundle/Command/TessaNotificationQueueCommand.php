<?php
/**
 * TessaNotificationQueueCommand.php
 *
 * @author      Felix Hack <f.hack@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Command;

use Eikona\Tessa\ConnectorBundle\Services\TessaNotificationQueueService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TessaNotificationQueueCommand extends ContainerAwareCommand
{
    use LockableTrait;

    /** @var TessaNotificationQueueService */
    protected $tessaNotificationQueueService;

    /**
     * @param TessaNotificationQueueService $tessaNotificationQueueService
     */
    public function __construct(
        TessaNotificationQueueService $tessaNotificationQueueService
    )
    {
        $this->tessaNotificationQueueService = $tessaNotificationQueueService;
        parent::__construct('eikona_media:tessa:notification_queue:execute');
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('eikona_media:tessa:notification_queue:execute');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->lock(base64_encode(__FILE__))) {
            $io->note('Command is already running');
            return 0;
        }

        $this->tessaNotificationQueueService->syncQueue();

        $io->note("Queue synced");
        return 0;
    }
}
