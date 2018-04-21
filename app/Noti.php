<?php

namespace App;

use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Elasticsearch\ClientBuilder;

class Noti extends Model
{
    use ElasticquentTrait;

    protected $table = 'notifications';
    protected $guarded = [];



}
