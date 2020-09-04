<?php

namespace App;

use App\Components\Filters\QueryFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    public function validate(Request $request)
	{
		return $request->validate(static::rule());
	}

	public function fillAndValidate($customData = null, $rule = null)
	{
		$rule = $rule ?? static::rule($this);
		$data = $customData ?? request()->all();
		$attributes = method_exists(static::class, 'attributes') ? static::attributes() : [];

		$validatedData = \Validator::make($data, $rule, [], $attributes)->validate();

		return parent::fill($validatedData);
	}

	public static function toKey()
    {
		$classModel = explode('\\', static::class);
		$string     = end($classModel);
		$kebab      = Str::kebab($string);

        return [
            'class' => $string,
            'route' => $kebab,
            'snake' => Str::snake($string),
            'title' => str_replace('-', ' ', $kebab),
        ];
    }

    public function getTableColumns() {
        return $this
            ->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

    public function scopeGetWithPagination()
    {
        request()->validate([
            'paginate' => 'numeric'
        ]);

        $paginate = request()->paginate ?? 10;

        return $this->paginate($paginate);
    }
}
