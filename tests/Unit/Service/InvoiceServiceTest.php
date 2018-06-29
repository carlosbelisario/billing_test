<?php


namespace Tests\Unit\Service;

use App\Entity\Company;
use App\Exception\InvoiceException;
use App\Repository\InvoiceRepositoryInterface;
use App\Service\InvoiceService;
use App\Entity\Client;
use App\Entity\Item;
use App\Entity\Invoice;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;


class InvoiceServiceTest extends TestCase
{
    private $billRepository;

    public function setUp()
    {
        $this->billRepository = $this->prophesize(InvoiceRepositoryInterface::class);
    }

    public function testInstance()
    {
        $service = new InvoiceService($this->billRepository->reveal());
        $this->assertInstanceOf(InvoiceService::class, $service);

        return $service;
    }

    public function testGenerateInvoiceOk()
    {
        $this->billRepository->save(Argument::type(Invoice::class))->willReturn(new Invoice)->shouldBeCalled();
        $service = new InvoiceService($this->billRepository->reveal());
        $client = new Client;
        $company = new Company();
        $items = new ArrayCollection();
        $items->add(Item::createFromArray(['price' => '100', 'length' => 1]));
        $invoice = $service->generateInvoice($company, $client, $items);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertCount(1, $invoice->getItem());
        $this->assertGreaterThan(0, $invoice->getTotal());
        $this->assertEquals(Invoice::STATUS_ACTIVE, $invoice->getStatus());
    }

    /**
     * @depends testInstance
     * @param InvoiceService $service
     */
    public function testGenerateInvoiceNotOkByNotItems(InvoiceService $service)
    {
        $this->expectException(InvoiceException::class);
        $client = new Client;
        $items = new ArrayCollection();
        $company = new Company();
        $service->generateInvoice($company, $client, $items);
    }

    public function testDisableOk()
    {
        $invoice = new Invoice();
        $invoice->setStatus(Invoice::STATUS_ACTIVE);
        $this->billRepository->disable(Argument::type(Invoice::class))->willReturn(new Invoice)->shouldBeCalled();
        $service = new InvoiceService($this->billRepository->reveal());
        $this->assertEquals(Invoice::STATUS_ACTIVE, $invoice->getStatus());
        $service->disableInvoice($invoice);
        $this->assertEquals(Invoice::STATUS_DISABLED, $invoice->getStatus());
    }

    /**
     * @depends testInstance
     * @param InvoiceService $service
     */
    public function testDisableNotOkByErrorStatus(InvoiceService $service)
    {
        $this->expectException(InvoiceException::class);
        $this->expectExceptionMessage('the invoice is in status disable');
        $invoice = new Invoice();
        $invoice->setStatus(Invoice::STATUS_DISABLED);
        $service->disableInvoice($invoice);
    }
}
