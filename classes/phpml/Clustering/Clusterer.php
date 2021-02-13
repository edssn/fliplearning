<?php

declare(strict_types=1);

namespace local_fliplearning\phpml\Clustering;

interface Clusterer
{
    public function cluster(array $samples): array;
}
