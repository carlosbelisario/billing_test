<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Exception\InvoiceException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository implements InvoiceRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function save(Invoice $invoice) : Invoice
    {
        $this->_em->persist($invoice);
        $this->_em->flush();

        return $invoice;
    }

    public function disable(Invoice $invoice): Invoice
    {
        if ($invoice->getStatus() != Invoice::STATUS_DISABLED) {
            throw new InvoiceException('the invoice status must be disable');
        }
        $this->_em->flush();

        return  $invoice;
    }

    public function findByCompany(Company $company, $statusCriteria = null): Collection
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('i, c')
            ->from(Invoice::class, 'i')
            ->join(Company::class ,'c')
        ;
        if ($statusCriteria) {
            $query->where($query->expr()->eq('status', $statusCriteria));
        }

        return $query->getQuery()->getResult();
    }
}
