<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\UniqueEmailService;
use App\Entity\User;
use League\Csv\Reader;
use League\Csv\Exception;

class CsvImportusersCommand extends Command
{
    protected static $defaultName = 'app:csv_importusers';

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $checkEmail;

    public function __construct(EntityManagerInterface $em, UniqueEmailService $checkEmail){
        parent::__construct();    
        $this->em = $em;
        $this->checkEmail = $checkEmail;
    }

    protected function configure()
    {
        $this            
            ->setDescription('Use this command to import users from csv file')
            ->addArgument('path', InputArgument::REQUIRED, 'Path required');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $filepath = $input->getArgument('path');        

        $csv = Reader::createFromPath($filepath, 'r');
        $csv->setHeaderOffset(0);
        $header_offset = $csv->getHeaderOffset(0);
        $header = $csv->getHeader();

        $records = $csv->getRecords($header);        
        
        foreach($records as $record){
            $user = $this->checkEmail->isUnique($record['email']);
            $record['role'] = isset($record['role'])? $record['role'] : 1;
            if($user){
                $user->setFirstname($record['firstname']);
                $user->setLastname($record['lastname']);        
                $user->setPassword($record['firstname']);
                $user->setRole($record['role']);
                $this->em->merge($user);
            } else {
                $newuser = new User();
                $newuser->setFirstname($record['firstname']);
                $newuser->setLastname($record['lastname']); 
                $newuser->setEmail($record['email']);       
                $newuser->setPassword($record['firstname']);                
                $newuser->setRole($record['role']);
                $this->em->merge($newuser);
            }        
        }        
        
        $this->em->flush();

        $io->success('Command finished executed');
    }
}
