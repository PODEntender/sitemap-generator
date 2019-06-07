<?php

namespace PODEntender\SitemapGenerator;

class Url
{
    const FREQUENCY_NEVER = 'never';

    const FREQUENCY_ALWAYS = 'always';

    const FREQUENCY_HOURLY = 'hourly';

    const FREQUENCY_DAILY = 'daily';

    const FREQUENCY_WEEKLY = 'weekly';

    const FREQUENCY_MONTHLY = 'monthly';

    const FREQUENCY_YEARLY = 'yearly';

    private $location;

    private $lastModified;

    private $changeFrequency;

    private $priority;

    /**
     * @todo
     * - validate changeFrequency to be one of FREQUENCY_* constants
     * - validate priority to be in range 0.0 to 1.0
     */
    public function __construct(
        string $location,
        ?\DateTimeInterface $lastModified,
        ?string $changeFrequency,
        ?float $priority
    ) {
        $this->location = $location;
        $this->lastModified = $lastModified;
        $this->changeFrequency = $changeFrequency;
        $this->priority = $priority;
    }

    public function location(): string
    {
        return $this->location;
    }

    public function lastModified(): ?\DateTimeInterface
    {
        return $this->lastModified;
    }

    public function changeFrequency(): ?string
    {
        return $this->changeFrequency;
    }

    public function priority(): ?float
    {
        return $this->priority;
    }
}
