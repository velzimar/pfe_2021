<?php

namespace App\Listeners;

use App\Repository\ProductRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductPrioritySetter extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:update-product-priorities';
    private $container;
    private $rep;
    public function __construct(ContainerInterface $container, ProductRepository $rep)
    {
        parent::__construct();
        $this->container = $container;
        $this->rep = $rep;
    }
    protected function configure()
    {
        $this->setDescription('Fixer les priorités des produits')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Fixer les priorités des produits');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->container->get('doctrine')->getManager();
        $products = $this->rep->findAll();
        foreach ($products as $product){
            $product->setPriority(0);
            $em->flush();
        }
        // Hacemos lo que sea
        $output->writeln('Fixer les priorités des produits');
        $em->flush();
        return 1;
    }
}