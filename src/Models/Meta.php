<?php

namespace Freshwork\Metable\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    /**
     * @var array
     */
    protected $dataTypes = ['boolean', 'integer', 'double', 'float', 'string', 'NULL'];

    /**
     * @var array
     */
    protected $fillable = ['key', 'value'];

    public function metable()
    {
        return $this->morphTo('metable');
    }

    /**
    * Set the value and type
    *
    * @param $value
    */
    public function setValueAttribute($value)
    {
        $type = gettype($value);
        if ( is_array($value) ) {
            $this->type = "array";
            $this->attributes['value'] = json_encode($value);
        } elseif ( $value instanceof DateTime ) {
            $this->type = "datetime";
            $this->attributes['value'] = $this->fromDateTime($value);
        } elseif ( $value instanceof Model ) {
            $this->type = "model";
            $this->attributes['value'] = get_class($value) . (! $value->exists ? '' : '#' . $value->getKey());
        } elseif ( is_object($value) ) {
            $this->type = "object";
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->type = in_array($type, $this->dataTypes) ? $type : 'string';
            $this->attributes['value'] = $value;
        }
    }
    public function getValueAttribute($value)
    {
        $type = $this->type ? : 'null';
        switch ($type) {
            case 'array':
                return json_decode($value, true);
            case 'object':
                return json_decode($value);
            case 'datetime':
                return $this->asDateTime($value);
            case 'model': {
                if ( strpos($value, '#') === false ) {
                    return new $value;
                }
                list($class, $id) = explode('#', $value);
                return with(new $class)->findOrFail($id);
            }
        }
        if ( in_array($type, $this->dataTypes) ) {
            settype($value, $type);
        }
        return $value;
    }

}
