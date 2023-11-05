<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Utility;

class GeneralUtility
{
    /**
     * Explodes a string and removes whitespace-only values.
     *
     * If $removeEmptyValues is set, then all values that contain only whitespace are removed.
     *
     * Each item will have leading and trailing whitespace removed. However, if the tail items are
     * returned as a single array item, their internal whitespace will not be modified.
     *
     * @param string $delim Delimiter string to explode with
     * @param string $string The string to explode
     * @param bool $removeEmptyValues If set, all empty values will be removed in output
     * @param int $limit If limit is set and positive, the returned array will contain a maximum of limit elements with
     *                   the last element containing the rest of string. If the limit parameter is negative, all components
     *                   except the last -limit are returned.
     * @return list<string> Exploded values
     * @phpstan-return ($removeEmptyValues is true ? list<non-empty-string> : list<string>) Exploded values
     */
    public static function trimExplode(string $delim, string $string, bool $removeEmptyValues = false, int $limit = 0): array
    {
        $result = explode($delim, $string);
        if ($removeEmptyValues) {
            // Remove items that are just whitespace, but leave whitespace intact for the rest.
            $result = array_values(array_filter($result, static fn(string $item): bool => trim($item) !== ''));
        }

        if ($limit === 0) {
            // Return everything.
            return array_map(trim(...), $result);
        }

        if ($limit < 0) {
            // Trim and return just the first $limit elements and ignore the rest.
            return array_map(trim(...), array_slice($result, 0, $limit));
        }

        // Fold the last length - $limit elements into a single trailing item, then trim and return the result.
        $tail = array_slice($result, $limit - 1);
        $result = array_slice($result, 0, $limit - 1);
        if ($tail) {
            $result[] = implode($delim, $tail);
        }
        return array_map(trim(...), $result);
    }
}
