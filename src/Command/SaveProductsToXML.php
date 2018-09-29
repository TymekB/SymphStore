<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 29.09.18
 * Time: 16:21
 */

namespace App\Command;


use App\Repository\ProductRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveProductsToXML extends Command
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository, ?string $name = null)
    {
        parent::__construct($name);
        $this->productRepository = $productRepository;
    }

    protected function configure()
    {
        $this->setName("product:save-to-xml");
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $products = $this->productRepository->findAll();

        $writer = new \XMLWriter();
        $writer->openURI('products.xml');
        $writer->startDocument('1.0','UTF-8');
        $writer->setIndent(4);

        $writer->startElement("offers");
        $writer->writeAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $writer->writeAttribute("version", "1");

        foreach($products as $product) {
            $output->writeln($product->getName().', '.$product->getQuantity());

            $writer->startElement("o");
            $writer->writeAttribute("id", $product->getId());
            $writer->writeAttribute("url", "http://localhost:8000/product/".$product->getSlug());
            $writer->writeAttribute("price", $product->getPrice());
            $writer->writeAttribute("stock", $product->getQuantity());
            $writer->endElement();
            $writer->writeElement("name", $product->getName());
            $writer->writeElement("desc", $product->getDescription());
            $writer->endAttribute();

            $writer->startAttribute("name");

        }

        $writer->endElement();
        $writer->endDocument();
        $writer->flush();
    }
}