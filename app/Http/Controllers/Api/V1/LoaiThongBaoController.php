<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Requests\PaginateRequest;
use App\Http\Responses\Api\V1\Response;
use App\Repositories\Interface\LoaiThongBaoRepositoryInterface;

class LoaiThongBaoController extends Controller
{
    private LoaiThongBaoRepositoryInterface $loaiThongBaoRepository;

    public function __construct(LoaiThongBaoRepositoryInterface $loaiThongBaoRepository)
    {
        $this->loaiThongBaoRepository = $loaiThongBaoRepository;
    }

    public function index(PaginateRequest $request)
    {
        $paginate['limit']      = $request->limit();
        $paginate['offset']     = $request->offset();
        $paginate['order']      = $request->order();
        $paginate['direction']  = $request->direction();

        $keyword = $request->get('keyword');

        $filter = [];
        if (!empty($keyword)) {
            $filter['query'] = $keyword;
        }

        $models = $this->loaiThongBaoRepository->allByFilter($filter, $paginate['order'], $paginate['direction']);
        foreach( $models as $key => $model ) {
            $models[$key] = $model->toAPIArray();
        }

        return Response::response(200, $models);
    }
}
