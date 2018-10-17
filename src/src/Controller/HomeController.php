<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\YoutubeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    protected $youtubeService;

    public function __construct(YoutubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    public function execute()
    {
        $topChannels = $this->youtubeService->getTopChannels(10);

        return $this->render('homepage.html.twig', [
            'topChannels' => $topChannels,
        ]);
    }
}
