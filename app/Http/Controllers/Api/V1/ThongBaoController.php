<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Requests\PaginateRequest;
use App\Http\Responses\API\V1\Response;
use App\Models\ThongBao;
use App\Models\User;
use App\Repositories\Interface\ThongBaoRepositoryInterface;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    private ThongBaoRepositoryInterface $thongBaoRepository;

    public function __construct(ThongBaoRepositoryInterface $thongBaoRepository)
    {
        $this->thongBaoRepository = $thongBaoRepository;
    }

    public function index(PaginateRequest $request)
    {
        $user = auth()->user();
        $paginate['limit']      = $request->limit();
        $paginate['offset']     = $request->offset();
        $paginate['order']      = $request->order();
        $paginate['direction']  = $request->direction();
        $keyword = $request->get('keyword');

        $filter = [];
        if (!empty($keyword)) {
            $filter['query'] = $keyword;
        }

        $filter['greaterThan'] = [
            'id' => $user->last_notification_id
        ];

        $filter['receive_id'] = [ThongBao::RECEIVE_ALL, $user->id];

        $models = $this->thongBaoRepository->getByFilter($filter, $paginate['order'], $paginate['direction'], $paginate['offset'], $paginate['limit']);
        $models = $this->thongBaoRepository->load($models, 'userRead');
        foreach( $models as $key => $model ) {
            $models[$key] = $model->toAPIArray();
        }

        return Response::response(200, $models);
    }
}
