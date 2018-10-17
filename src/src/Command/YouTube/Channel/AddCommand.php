<?php declare(strict_types=1);

namespace App\Command\YouTube\Channel;

use App\Service\YoutubeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends Command
{
    protected $youtubeService;

    public function __construct(YoutubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('youtube:channels:add')
            ->setDescription('Add new channel by ID')
            ->addArgument('id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Adding channel... ');
        $channel = $this->youtubeService->addChannelById($input->getArgument('id'));
        $output->writeln('Done!');

        $output->write('Adding videos for channel... ');
        $videos = $this->youtubeService->addVideosByChannel($channel);
        $output->writeln('Done! Total added: ' . \count($videos));
    }
}
