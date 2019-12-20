<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use Exception;
use InvalidArgumentException;
use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class Json implements Formatter
{
    /** @var OutputInterface */
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Format the result to the desired format.
     *
     * @param InsightCollection $insightCollection
     * @param string $dir
     * @param array<string> $metrics
     *
     * @throws Exception
     */
    public function format(
        InsightCollection $insightCollection,
        string $dir,
        array $metrics
    ): void {
        $results = $insightCollection->results();

        $data = [
            'summary' => [
                'code' => $results->getCodeQuality(),
                'complexity' => $results->getComplexity(),
                'architecture' => $results->getStructure(),
                'style' => $results->getStyle(),
                'security issues' => $results->getTotalSecurityIssues(),
            ],
        ];
        $data += $this->issues($insightCollection, $metrics);

        $json = json_encode($data);

        if ($json === false) {
            throw new InvalidArgumentException('Failed parsing result to JSON.');
        }

        $this->output->write($json);
    }

    /**
     * Outputs the issues errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param array<string> $metrics
     *
     * @return array<string, array<int, array<string, int|string>>|null>
     */
    private function issues(
        InsightCollection $insightCollection,
        array $metrics
    ): array {
        $data = [];

        foreach ($metrics as $metricClass) {
            $category = explode('\\', $metricClass);
            $category = $category[count($category) - 2];

            if (! isset($data[$category])) {
                $data[$category] = [];
            }

            $current = $data[$category];

            /** @var Insight $insight */
            foreach ($insightCollection->allFrom(new $metricClass()) as $insight) {
                if (! $insight->hasIssue()) {
                    continue;
                }

                if (! $insight instanceof HasDetails) {
                    $current[] = [
                        'title' => $insight->getTitle(),
                        'insightClass' => $insight->getInsightClass(),
                    ];
                    continue;
                }

                /** @var \NunoMaduro\PhpInsights\Domain\Details $detail */
                foreach ($insight->getDetails() as $detail) {
                    $current[] = array_filter([
                        'title' => $insight->getTitle(),
                        'insightClass' => $insight->getInsightClass(),
                        'file' => $detail->hasFile() ? $detail->getFile() : null,
                        'line' => $detail->hasLine() ? $detail->getLine() : null,
                        'function' => $detail->hasFunction() ? $detail->getFunction() : null,
                        'message' => $detail->hasMessage() ? $detail->getMessage() : null,
                    ]);
                }
            }

            $data[$category] = $current;
        }

        return $data;
    }
}
