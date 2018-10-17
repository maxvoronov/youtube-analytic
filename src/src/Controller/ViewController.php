<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\YoutubeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ViewController extends AbstractController
{
    protected $youtubeService;

    public function __construct(YoutubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    public function execute(string $id)
    {
        $channel = $this->youtubeService->getChannelById($id);
        $channelVotes = $this->youtubeService->getChannelVotes($channel);

        return $this->render('channel_view.html.twig', [
            'channel' => $channel,
            'channelVotes' => $channelVotes,
        ]);
    }
}
