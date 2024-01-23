<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return with(new static())->getTable();
    }

    /**
     * @return string[]
     */
    public function getEditableColumns(): array
    {
        return $this->fillable;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }
}
