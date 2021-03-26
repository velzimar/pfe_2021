<?php

namespace App\Listeners;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TokenUpdater extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:update-users-tokens';
    private $container;
    private $rep;
    public function __construct(ContainerInterface $container, UserRepository $rep)
    {
        parent::__construct();
        $this->container = $container;
        $this->rep = $rep;
    }
    protected function configure()
    {
        $this->setDescription('Changer les tokens des utilisateurs')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Changer les tokens des utilisateurs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->container->get('doctrine')->getManager();
        $users = $this->rep->findByRoleNot("ROLE_CLIENT","ROLE_SUPER");
        foreach ($users as $user){
            $user->setToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
            $em->flush();
        }
        // Hacemos lo que sea
        $output->writeln('Changer les tokens des utilisateurs');
        $em->flush();
        return 1;
    }
}