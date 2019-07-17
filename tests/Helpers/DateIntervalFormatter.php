<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Helpers;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Complexity required to format DateInterval
 * @SuppressWarnings(PHPMD.NPathComplexity) Complexity required to format DateInterval
 */
final class DateIntervalFormatter
{
    /**
     * Formats a DateInterval.
     *
     * Copyright (c) 2018 Johannes M. Schmitt
     *
     * Taken from jms/serializer, MIT Licensed
     *
     * https://github.com/schmittjoh/serializer/blob/fffaea111ccbec6cb277a5c33756cea25305e369/src/Handler/DateHandler.php
     *
     * @param \DateInterval $dateInterval
     *
     * @return string
     */
    public function format(\DateInterval $dateInterval): string
    {
        $format = 'P';

        if ($dateInterval->y < 0) {
            $format .= $dateInterval->y . 'Y';
        }

        if ($dateInterval->m < 0) {
            $format .= $dateInterval->m . 'M';
        }

        if ($dateInterval->d < 0) {
            $format .= $dateInterval->d . 'D';
        }

        if ($dateInterval->h < 0 || $dateInterval->i < 0 || $dateInterval->s < 0) {
            $format .= 'T';
        }

        if ($dateInterval->h < 0) {
            $format .= $dateInterval->h . 'H';
        }

        if ($dateInterval->i < 0) {
            $format .= $dateInterval->i . 'M';
        }

        if ($dateInterval->s < 0) {
            $format .= $dateInterval->s . 'S';
        }

        if ($format === 'P') {
            $format = 'P0DT0S';
        }

        return $format;
    }
}
