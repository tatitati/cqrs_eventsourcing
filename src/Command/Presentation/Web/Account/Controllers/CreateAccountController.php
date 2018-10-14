<?php
namespace App\Command\Presentation\Web\Account\Controllers;

use App\Command\ApplicationServices\CreateAccount\CreateAccountRequest;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateAccountController extends AbstractController
{
    public function create(CommandBus $commandBus, Request $request)
    {
        $initialBalance = $request->get('initialBalance');

        // build RequestDTO or Command for the command bus
        $command = new CreateAccountRequest($initialBalance);
        // drop command in command bus
        $commandBus->handle($command);

        return new Response(
            '<html><body>CREATE FINISHED</body></html>'
        );
    }
}

