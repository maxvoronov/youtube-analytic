<?php declare(strict_types=1);

namespace App\Command\YouTube\Channel;

use App\Service\YoutubeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddByUsernameCommand extends Command
{
    protected $youtubeService;

    public function __construct(YoutubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('youtube:channels:addbyusername')
            ->setDescription('Add new channels by username')
            ->addArgument('username', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Adding channels... ');
        $channels = $this->youtubeService->addChannelsByUsername($input->getArgument('username'));
        $output->writeln('Done! Total added: ' . \count($channels));

        $output->write('Adding videos... ');
        $videos = [];
        foreach ($channels as $channel) {
            $videos = array_merge($videos, $this->youtubeService->addVideosByChannel($channel));
        }
        $output->writeln('Done! Total added: ' . \count($videos));
    }
}
