<?php

namespace App\Tests;

use App\Entity\Invoice;
use App\Exception\InvoiceException;
use App\Repository\InvoiceRepository;
use App\Repository\InvoiceRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\RegistryInterface;

class InvoiceRepositoryTest extends TestCase
{
    private $repository;

    public function setUp()
    {
        $registry = $this->prophesize(RegistryInterface::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $metadata = $this->prophesize(ClassMetadata::class);
        $em->getClassMetadata(Argument::type('string'))->willReturn($metadata->reveal());
        $registry->getManagerForClass(Argument::type('string'))
            ->willReturn($em->reveal());
        $this->repository = new InvoiceRepository($registry->reveal());
    }
    public function testInstance()
    {
        $this->assertInstanceOf(InvoiceRepositoryInterface::class, $this->repository);
    }

    public function testSave()
    {
        $registry = $this->prophesize(RegistryInterface::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $metadata = $this->prophesize(ClassMetadata::class);
        $em->getClassMetadata(Argument::type('string'))->willReturn($metadata->reveal());
        $em->persist(Argument::type(Invoice::class))->shouldBeCalled();
        $em->flush()->shouldBeCalled();
        $registry->getManagerForClass(Argument::type('string'))
            ->willReturn($em->reveal());
        $repository = new InvoiceRepository($registry->reveal());
        $invoice = $repository->save(new Invoice());
        $this->assertInstanceOf(Invoice::class, $invoice);
    }

    public function testDisableOk()
    {
        $registry = $this->prophesize(RegistryInterface::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $metadata = $this->prophesize(ClassMetadata::class);
        $em->getClassMetadata(Argument::type('string'))->willReturn($metadata->reveal());
        $em->flush()->shouldBeCalled();
        $registry->getManagerForClass(Argument::type('string'))
            ->willReturn($em->reveal());
        $repository = new InvoiceRepository($registry->reveal());
        $invoice = $repository->disable((new Invoice())->setStatus(Invoice::STATUS_DISABLED));
        $this->assertInstanceOf(Invoice::class, $invoice);
    }

    public function testDisableNotOk()
    {
        $this->expectException(InvoiceException::class);
        $this->repository->disable((new Invoice())->setStatus(Invoice::STATUS_ACTIVE));
    }
}
