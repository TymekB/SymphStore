<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.09.18
 * Time: 18:08
 */

namespace App\Command;

use App\ShoppingProcess\Cart\ProductReservator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeOldProductReservations extends Command
{
    /**
     * @var ProductReservator
     */
    private $reservator;
    /**
     * @var int
     */
    private $time;

    public function __construct(int $time, ProductReservator $reservator, ?string $name = null)
    {
        parent::__construct($name);
        $this->reservator = $reservator;
        $this->time = $time;
    }

    public function configure()
    {
        $this->setName("product-reservations:purge-old");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('purging...');


        $this->reservator->purgeOld($this->time);

        $output->writeln('done');
    }


}