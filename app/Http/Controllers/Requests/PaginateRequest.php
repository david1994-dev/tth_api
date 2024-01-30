<?php

namespace App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginateRequest extends FormRequest
{
    public int $offset = 0;

    public int $limit = 20;
    public int $lastID = 0;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    /**
     * @return int
     */
    public function offset(): int
    {
        $page = $this->get('page', 1);
        $this->offset = ( $page - 1 ) * $this->limit;

        return $this->offset;
    }

    /**
     * @return int
     */
    public function lastID(): int
    {
        $lastID = $this->get('last_id', 0);
        $this->lastID = $lastID;

        return $this->lastID;
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        $this->limit = $this->get('limit', 20);

        return $this->limit;
    }

    public function order($default = 'id')
    {
        return $this->get('order', $default);
    }

    public function direction($default = 'desc'): string
    {
        $direction = strtolower($this->get('direction', $default));
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        return $direction;
    }
}
