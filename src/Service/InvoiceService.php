<?php

namespace App\Service;


use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\Client;
use App\Entity\Item;
use App\Exception\InvoiceException;
use App\Repository\InvoiceRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class InvoiceService
{
    /**
     * @var InvoiceRepositoryInterface $invoiceRepository
     */
    private $invoiceRepository;

    /**
     * Company constructor.
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @param Company $company
     * @param Client $client
     * @param ArrayCollection $items
     * @return Invoice
     */
    public function generateInvoice(Company $company, Client $client, ArrayCollection $items) : Invoice
    {
        $bill = new Invoice;
        $bill->setCompany($company)
            ->setClient($client)
            ->setStatus(Invoice::STATUS_ACTIVE)
        ;
        if ($items->isEmpty()) {
            throw new InvoiceException('the invoice must have at least one item');
        }
        /** @var Item $item */
        foreach ($items as $item) {
            $bill->addItem($item);
        }
        $bill->calculateSubtotal()
            ->calculateIva()
        ;
        $this->invoiceRepository->save($bill);

        return $bill;
    }

    public function disableInvoice(Invoice $invoice)
    {
        if ($invoice->getStatus() == Invoice::STATUS_DISABLED) {
            throw new InvoiceException('the invoice is in status disable');
        }
        $invoice->setStatus(Invoice::STATUS_DISABLED);
        $this->invoiceRepository->disable($invoice);

        return $invoice;
    }

    public function listInvoices(Company $company) : Collection
    {
        return $this->invoiceRepository->findByCompany($company);
    }
}
