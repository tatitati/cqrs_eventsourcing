<?php
namespace App\Tests\Command\ApplicationServices\CreateAccount;

use App\Command\ApplicationServices\Account\CreateAccount;
use App\Command\ApplicationServices\Account\CreateAccountRequest;
use App\Command\Infrastructure\Projections\Projector;
use App\Command\Infrastructure\Repository\AccountRepository;
use PHPUnit\Framework\TestCase;

/**
 * @group application
 */
class CreateAccountTest extends TestCase
{
    public function testCheckIfAlreadyExists()
    {
        $mockRepository = $this->createMock(AccountRepository::class);
        $mockRepository->expects($this->once())
            ->method('findByEmail');

        (new CreateAccount($mockRepository, $this->createMock(Projector::class)))
            ->handle($this->request());
    }

    public function testSavesNewAccount()
    {
        $mockRepository = $this->createMock(AccountRepository::class);
        $mockRepository->expects($this->once())
            ->method('save');

        (new CreateAccount($mockRepository, $this->createMock(Projector::class)))
            ->handle($this->request());
    }

    private function request()
    {
        return  new CreateAccountRequest(122, 'email@asfa.com');
    }
}
