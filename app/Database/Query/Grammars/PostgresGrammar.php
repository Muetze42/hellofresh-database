<?php

namespace App\Database\Query\Grammars;

use Illuminate\Database\Query\Grammars\PostgresGrammar as Grammar;
use Override;

class PostgresGrammar extends Grammar
{
    /**
     * Get the format for database stored dates.
     */
    #[Override]
    public function getDateFormat(): string
    {
        return 'Y-m-d H:i:s.u';
    }
}
