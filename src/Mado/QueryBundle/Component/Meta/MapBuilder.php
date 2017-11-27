<?php

namespace Mado\QueryBundle\Component\Meta;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @since Class available since Release 2.1.0
 */
class MapBuilder implements RelationDataMapper
{
    private $manager;

    private $map = [];

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function getMap() : array
    {
        $this->rebuildRelationMap();

        return $this->map;
    }

    /** @codeCoverageIgnore */
    public static function relations(ClassMetadata $classMetadata)
    {
        $encoded = json_encode($classMetadata);
        $decoded = json_decode($encoded, true);
        $relations = $decoded['associationMappings'];

        $relMap = [];

        foreach ($relations as $name => $meta) {
            $relMap[$name] = $meta['targetEntity'];
        }

        return $relMap;
    }

    public function rebuildRelationMap()
    {
        $allMetadata = $this->manager
            ->getMetadataFactory()
            ->getAllMetadata();

        foreach ($allMetadata as $singleEntityMetadata) {
            // @codeCoverageIgnoreStart
            $this->map[$singleEntityMetadata->getName()]['relations'] = self::relations($singleEntityMetadata);
            // @codeCoverageIgnoreEnd
        }
    }
}
