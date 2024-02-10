<?php

namespace App\Contracts\Commands;

use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

trait TableHelpersTrait
{
    protected function centeredTableCell(?string $value): TableCell
    {
        return new TableCell(
            (string) $value,
            ['style' => new TableCellStyle(['align' => 'center'])]
        );
    }

    protected function alignRightTableCell(?string $value): TableCell
    {
        return new TableCell(
            (string) $value,
            ['style' => new TableCellStyle(['align' => 'right'])]
        );
    }
}
