<?php

namespace App\Repository;


use App\Entity\Company;
use App\Entity\Invoice;
use Doctrine\Common\Collections\Collection;

interface InvoiceRepositoryInterface
{
    /**
     * @param Invoice $bill
     * @return Invoice
     */
    public function save(Invoice $invoice) : Invoice;

    /**
     * @param Invoice $invoice
     * @return Invoice
     */
    public function disable(Invoice $invoice) : Invoice;

    /**
     * @param Company $company
     * @return Collection
     */
    public function findByCompany(Company $company) : Collection;
}
