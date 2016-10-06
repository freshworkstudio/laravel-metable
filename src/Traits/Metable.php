<?php
namespace Freshwork\Metable\Traits;



use Freshwork\Metable\Models\Meta;

trait Metable
{
    public function meta()
    {
        return $this->morphMany(Meta::class, 'metable');
    }



    public function addMeta($key, $value)
    {
        $this->meta()->create([
            'key'       => $key,
            'value'     => $value
        ]);

        return $this;
    }

    public function getAllMeta()
    {
        $all = [];
        foreach($this->meta as $result)
        {
            $all[$result['key']][$result['id']] = $result['value'];
        }
        return collect($all);
    }

    public function loadMeta()
    {
        $this->metadata = $this->getAllMeta();

        return $this;
    }

    public function getMeta($key, $single  = true, $cacheAll= false)
    {
        $results = null;

        $results = ($cacheAll) ? $this->meta->where('key', $key) : $this->meta()->where('key', $key)->get();

        $return = [];
        foreach($results as $result)
        {
            $return[$result['id']] = $result['value'];
        }
        $return = collect($return);

        return ($single) ? $return->first() : $return;
    }



    public function removeMeta($key)
    {
        $this->meta()->where('key', $key)->delete();

        return $this;
    }

    public function scopeWhereMeta($query, $key, $value)
    {
        return $query->whereHas('meta', function($query) use ($key, $value) {
            $query->where('key', $key)->where('value', $value);
        });

    }

    public function scopeOrWhereMeta($query, $key, $value)
    {
        return $query->orWhereHas('meta', function($query) use ($key, $value) {
            $query->where('key', $key)->where('value', $value);
        });

    }
}