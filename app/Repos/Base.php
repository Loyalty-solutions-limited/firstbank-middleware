<?php

namespace App\Repos;

use App\Interfaces\IBase;
use Illuminate\Support\Facades\DB;

class Base implements IBase
{
    protected $table_name;

    public function __construct($table_name)
    {
        $this->table_name = $table_name;
    }

    public function selectAll(array $selects, $limit)
    {
        return  DB::table($this->table_name)->select($selects)->orderByDesc('created_at')->paginate($limit);
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insert($data);
    }

    public function insertGetId(array $data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }

    public function findItem(array $condition)
    {
        return DB::table($this->table_name)->where($condition)->first();
    }

    public function updateItem(array $condition, array $data)
    {
        return DB::table($this->table_name)->where($condition)->update($data);
    }


    public function getAll(array $selects)
    {
        return DB::table($this->table_name)->where($selects)->orderByDesc('created_at')->get();
    }

    public function getItem(array $condition, array $selects)
    {
        return DB::table($this->table_name)->where($condition)->select($selects)->orderByDesc('created_at')->get();
    }

    public function selectItem(array $condition, array $selects)
    {
        return  DB::table($this->table_name)
            ->where($condition)->select($selects)->first();
    }

    public function selectItems(array $condition, array $selects, $limit)
    {
        return DB::table($this->table_name)->where($condition)->select($selects)->orderByDesc('created_at')->paginate($limit);
    }

    public function deleteItem(array $condition)
    {
        return DB::table($this->table_name)->where($condition)->delete();
    }

    public function new_insert($selects)
    {
        return DB::table($this->table_name)->orderByDesc('id')->select($selects)->first();
    }

    public function selectAllOrSingle(array $selects, $id, $limit)
    {
        if (!$id) {
            return $this->selectAll($selects, $limit);
        }

        return $this->selectItem(['id' => $id], $selects);
    }
    public function updateOrInsert(array $conditions, array $merge)
    {
        return DB::table($this->table_name)
        ->updateOrInsert(
           $conditions,
            $merge
        );
    }
}
