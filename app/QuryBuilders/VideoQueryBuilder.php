<?php

namespace App\QueryBuilders;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoQueryBuilder implements QueryBuilderInterface
{
    public static function apply(Request $filters, $builder = null)
    {
        if ($builder == null) {
            $builder = (new Video)->newQuery();
        }

        // free text search
        if ($filters->query('search')) {
            $freeTextSearch = $filters->query('search');
            $builder->where('username', 'like', "%$freeTextSearch%");
        }

        return $builder;
    }

    public static function applyWithPaginator(Request $filters, $builder = null)
    {
        $builder = self::apply($filters, $builder);

        $perPage = $filters->query('perPage') ? (int) $filters->query('perPage') : 15;

        return $builder->paginate($perPage);
    }
}
