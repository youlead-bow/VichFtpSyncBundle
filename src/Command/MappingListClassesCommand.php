<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vich\FtpSyncBundle\Metadata\MetadataReader;

class MappingListClassesCommand extends Command
{
    public function __construct(private readonly MetadataReader $metadataReader)
    {
        parent::__construct();
    }

    public static function getDefaultName(): string
    {
        return 'vich:ftp:sync:mapping:list-classes';
    }

    protected function configure(): void
    {
        $this
            ->setName('vich:ftp:sync:mapping:list-classes')
            ->setDescription('Recherche des classes ftpsyncable.')
        ;
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