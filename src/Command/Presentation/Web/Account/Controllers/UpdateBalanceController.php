<?php
namespace App\Command\Presentation\Web\Account\Controllers;

use App\Command\ApplicationServices\UpdateBalance\UpdateBalanceRequest;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateBalanceController extends AbstractController
{
    public function update(CommandBus $commandBus, Request $request)
    {
        $command = new UpdateBalanceRequest();
        $commandBus->handle($command);

        return new Response(
            '<html><body>UPDATE FINISHED</body></html>'
        );
    }
}

