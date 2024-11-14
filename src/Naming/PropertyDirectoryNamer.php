<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Naming;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Vich\FtpSyncBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Exception\NameGenerationException;
use Vich\UploaderBundle\Mapping\PropertyMapping as VichPropertyMapping;
use Vich\UploaderBundle\Naming\ConfigurableInterface as VichConfigurableInterface;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface as VichDirectoryNamerInterface;
use Vich\UploaderBundle\Util\Transliterator;

class PropertyDirectoryNamer implements DirectoryNamerInterface, ConfigurableInterface, VichDirectoryNamerInterface, VichConfigurableInterface
{
    private ?string $propertyPath = null;

    private bool $transliterate = false;

    private readonly PropertyAccessorInterface $propertyAccessor;

    public function __construct(?PropertyAccessorInterface $propertyAccessor, private readonly Transliterator $transliterator)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param array $options Options for this namer. The following options are accepted:
     *                       - property: path to the property used to name the file. Can be either an attribute or a method.
     *                       - transliterate: whether the filename should be transliterated or not
     *
     * @throws \InvalidArgumentException
     */
    public function configure(array $options): void
    {
        if (empty($options['property'])) {
            throw new \InvalidArgumentException('Option "property" is missing or empty.');
        }

        $this->propertyPath = $options['property'];
        $this->transliterate = isset($options['transliterate']) ? (bool) $options['transliterate'] : $this->transliterate;
    }

    public function directoryName(object|array $object, PropertyMapping|VichPropertyMapping $mapping): string
    {
        if (empty($this->propertyPath)) {
            throw new \LogicException('The property to use can not be determined. Did you call the configure() method?');
        }

        try {
            $name = $this->propertyAccessor->getValue($object, $this->propertyPath);
            if($name instanceof \BackedEnum){
                $name = $name->value;
            }

        } catch (NoSuchPropertyException $e) {
            throw new NameGenerationException(\sprintf('Directory name could not be generated: property %s does not exist.', $this->propertyPath), $e->getCode(), $e);
        }

        if (empty($name)) {
            throw new NameGenerationException(\sprintf('Directory name could not be generated: property %s is empty.', $this->propertyPath));
        }

        if ($this->transliterate) {
            $name = $this->transliterator->transliterate($name);
        }

        return $name;
    }
}