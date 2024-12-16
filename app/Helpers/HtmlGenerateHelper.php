<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class HtmlGenerateHelper
{
    public static function generateTable($dataSource)
    {
        // Check if the data source is empty
        if (empty($dataSource)) {
            return "<p>Data source is empty.</p>";
        }

        // Ensure the data source is an array of arrays
        if ($dataSource instanceof Collection || is_array($dataSource)) {
            $dataSource = json_decode(json_encode($dataSource), true);
        } else {
            return "<p>Invalid data source format.</p>";
        }

        // Ensure the data source is not empty
        if (empty($dataSource) || !isset($dataSource[0]) || !is_array($dataSource[0])) {
            return "<p>Invalid data source format.</p>";
        }

        // Get the column names from the first record
        $columns = array_keys($dataSource[0]);
        $html = "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        $html .= "<thead><tr>";

        // Generate table headers
        foreach ($columns as $col) {
            $html .= "<th>" . htmlspecialchars($col) . "</th>";
        }
        $html .= "</tr></thead><tbody>";

        // Populate table rows with data
        foreach ($dataSource as $row) {
            $html .= "<tr>";
            foreach ($columns as $col) {
                // Use an empty string if the column value is missing
                $html .= "<td>" . htmlspecialchars($row[$col] ?? '') . "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</tbody></table>";
        return $html;
    }
}
