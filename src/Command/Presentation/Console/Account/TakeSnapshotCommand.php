<?php
namespace App\Command\Presentation\Console\Account;

use DateTimeImmutable;
use League\Tactician\CommandBus;
use App\Command\ApplicationServices\Account\TakeSnapshotRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TakeSnapshotCommand extends ContainerAwareCommand
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

        $this
            ->setName('account:snapshot')
            ->setDescription('Create an snapshot')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'email?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getOption('email');

        $this->commandbus->handle(
            new TakeSnapshotRequest($email, new DateTimeImmutable())
        );
    }
}
