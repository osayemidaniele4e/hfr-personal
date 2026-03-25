<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HFExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    private $columnheader;
    private $raws;
 
    public function __construct($data, $columns)
    {
        $this->raws = $data;
        $this->columnheader = $columns;
    }
 
    public function headings(): array
    {
        return $this->columnheader;
    }

    public function collection()
    {
        return collect($this->raws);
    }
 
      
}
