<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:46
 **/

namespace Szkj\Collection\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed system_id
 * @property mixed status
 * @property mixed platform_id
 * @property mixed type
 * Class Task
 * @package Szkj\Collection\Models
 */
class Task extends Model
{
    /**
     * table name
     * @var string
     */
    protected $table = 'tasks';

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

    protected $guarded = [];


    /**
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}