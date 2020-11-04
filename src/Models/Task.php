<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:46
 **/

namespace Szkj\Collection\Models;


use Illuminate\Database\Eloquent\Model;
use Szkj\Rbac\Traits\DateTimeFormatter;

class Task extends Model
{
    use DateTimeFormatter;
    /**
     * table name
     * @var string
     */
    protected $table = 'task';

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection =config('database.default');

        $this->setConnection($connection);

        parent::__construct($attributes);
    }
}