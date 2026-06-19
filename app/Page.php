<?php
    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Str;

    class Page extends Model
    {
        protected $table = 'pages';
        public $incrementing = false;
        protected $keyType = 'string';

        protected $fillable = [
            'id',
            'name',
            'slug',
            'status',
            'content'
        ];

        protected $casts = [
            'content' => 'array',
        ];

        protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                if (!$model->id) {
                    $model->id = (string) Str::uuid();
                }
            });
        }
    }
