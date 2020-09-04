<?php
namespace App\Components\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilters
{
    protected $request;

    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply semua filter nya disini
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        foreach ($this->request->all() as $name => $value) {
            if (!method_exists($this, $name) || $value === null) {
                continue;
            }
            if (is_array($value)) {
                $this->$name($value);
            } else if (strlen($value)) {
                $this->$name($value);
            } else {
                $this->$name();
            }
        }
        return $this->builder;
    }

    /**
     * get per page request
     * @return mixed
     */
    public function perPage () {
        return $this->request->get('per_page');
    }

    /**
     * Check jika user mau pilih semua data
     * @param $key
     * @return bool
     */
    public function requestAllData ($key) {
        return $key == 'all';
    }

    public function get($key)
    {
        return $this->request->get($key);
    }

}
