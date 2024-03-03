<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportUsers implements FromCollection
{

    protected $app;

    public function __construct(string $app)
    {
        $this->app = $app;
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return User::where('app', 'LIKE', '%' . $this->app . '%')->get();
    }
}