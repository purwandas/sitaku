<?php

namespace App\Components\Filters;

use Carbon\Carbon;

class SalesFilter extends QueryFilters
{
	public function sales($value)
	{
		return is_array($value) ? $this->builder->whereIn('sales.date', $value) : $this->builder->where('sales.date', $value);
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function date($value)
	{
		$value = explode(' ~ ', $value);
		$begin = Carbon::parse($value[0])->format('Y-m-d');
		$end   = Carbon::parse($value[1])->format('Y-m-d');
		return $this->builder->whereBetween('sales.date', [$begin, $end]);
	}

	public function supplier_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('suppliers.id', $value) : $this->builder->where('sales.id', $value);
	}

}