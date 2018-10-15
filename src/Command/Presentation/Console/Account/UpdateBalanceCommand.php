<?php
namespace App\Command\Presentation\Console\Account;

use League\Tactician\CommandBus;
use App\Command\ApplicationServices\Account\UpdateBalanceRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBalanceCommand extends ContainerAwareCommand
{
    /** @var CommandBus  */
    private $commandbus;

    public function __construct(CommandBus $commandBus)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->commandbus = $commandBus;

        parent::__construct();
    }

    protected function configure()
    {
        // @example:
        // php bin/console account:update --email='whatever' --amount=10

        $this
            ->setName('account:update')
            ->setDescription('Update an account with some topup of money')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'email?')
            ->addOption('amount', null, InputOption::VALUE_REQUIRED, 'topup amount?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getOption('email');
        $amount = $input->getOption('amount');


        $this->commandbus->handle(
            new UpdateBalanceRequest($amount, $email)
        );
    }
}

