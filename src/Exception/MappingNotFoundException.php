<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Exception;

class MappingNotFoundException extends \RuntimeException
{
    public static function createNotFoundForClassAndField(string $mapping, string $class, string $field): self
    {
        return new self(
            \sprintf("Le mappage « %s » n'existe pas. La configuration de la classe « %s » est probablement incorrecte car le mappage à utiliser pour le champ « %s » est introuvable.", $mapping, $class, $field)
        );
    }

    public static function createNotFoundForClass(string $mapping, string $class): self
    {
        if ('' === $mapping) {
            return new self(
                \sprintf("Le mappage est introuvable. La configuration de la classe « %s » est probablement incorrecte.", $class)
            );
        }

        return new self(
            \sprintf("Le mappage « %s » n'existe pas. La configuration de la classe « %s » est probablement incorrecte.", $mapping, $class)
        );
    }
}