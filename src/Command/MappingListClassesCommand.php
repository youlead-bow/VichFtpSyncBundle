<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Command;


use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vich\FtpSyncBundle\Metadata\MetadataReader;

#[AsCommand(
    name: "vich:ftp:sync:mapping:list-classes",
    description: "Recherche des classes ftpsyncable"
)]
class MappingListClassesCommand extends Command
{
    public function __construct(private readonly MetadataReader $metadataReader)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Recherche des classes ftpsyncable.');

        $ftpsyncableClasses = $this->metadataReader->getFtpSyncableClasses();

        foreach ($ftpsyncableClasses as $class) {
            $output->writeln(\sprintf('Trouvée <comment>%s</comment>', $class));
        }

        $output->writeln(\sprintf('Trouvée <comment>%d</comment> classes.', \count((array) $ftpsyncableClasses)));

        return self::SUCCESS;
    }
}