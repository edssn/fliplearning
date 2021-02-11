<?php

declare(strict_types=1);

namespace local_fliplearning\phpml;

interface Estimator
{
    public function train(array $samples, array $targets): void;

    /**
     * @return mixed
     */
    public function predict(array $samples);
}
