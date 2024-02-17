<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Requests\PaginateRequest;
use App\Http\Responses\API\V1\Response;
use App\Models\ThongBao;
use App\Models\ThongBaoUser;
use App\Models\User;
use App\Repositories\Interface\ThongBaoRepositoryInterface;
use App\Repositories\Interface\ThongBaoUserRepositoryInterface;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    private ThongBaoRepositoryInterface $thongBaoRepository;
    private ThongBaoUserRepositoryInterface $thongBaoUserRepository;

    public function __construct(
        ThongBaoRepositoryInterface $thongBaoRepository,
        ThongBaoUserRepositoryInterface $thongBaoUserRepository
    ) {
        $this->thongBaoRepository = $thongBaoRepository;
        $this->thongBaoUserRepository = $thongBaoUserRepository;
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

        $filter['last_id'] = $request->lastID();
        $filter['category'] = $request->get('category', '');
        $filter['created_at_from'] = $request->get('created_at_from');
        $filter['created_at_to'] = $request->get('created_at_to');
        $models = $this->thongBaoRepository->getNotifications($user, $filter, $paginate['order'], $paginate['direction'], $paginate['offset'], $paginate['limit']);
        $models = $this->thongBaoRepository->load($models, ['userRead', 'loaiThongBao']);
        foreach( $models as $key => $model ) {
            $models[$key] = $model->toAPIArray();
        }

        return Response::response(200, $models);
    }

    public function getMore(PaginateRequest $request)
    {
        $user = auth()->user();
        $paginate['order']      = $request->order();
        $paginate['direction']  = $request->direction();

        $keyword = $request->get('keyword');

        $filter = [];
        if (!empty($keyword)) {
            $filter['query'] = $keyword;
        }

        $filter['last_id'] = $request->lastID();
        $filter['category'] = $request->get('category', '');
        $filter['created_at_from'] = $request->get('created_at_from');
        $filter['created_at_to'] = $request->get('created_at_to');

        $models = $this->thongBaoRepository->getMore($user, $filter, $paginate['order'], $paginate['direction']);
        $models = $this->thongBaoRepository->load($models, ['userRead', 'loaiThongBao']);

        foreach( $models as $key => $model ) {
            $models[$key] = $model->toAPIArray();
        }

        return Response::response(200, $models);
    }

    public function read(Request $request)
    {
        $user = auth()->user();
        $id = $request->get('id', 0);

        if( !is_numeric($id) || ($id <= 0) ) {
            return Response::response(40001);
        }

        $model = $this->thongBaoRepository->findById($id);
        if( empty($model) ) {
            return Response::response(20004);
        }

        if ($model->receive_id != 0 && $model->receive_id != $user->id) {
            return Response::response(40301);
        }

        $isRead = $this->thongBaoUserRepository->findByUserIdAndThongBaoIdAndStatus($user->id, $model->id, ThongBaoUser::STATUS_DA_DOC);
        if ($isRead) {
            return Response::response(200);
        }

        $isSuccess = $this->thongBaoUserRepository->updateOrCreate([
            'user_id' => $user->id,
            'thong_bao_id' => $model->id,
        ], ['status' => ThongBaoUser::STATUS_DA_DOC]);

        if (!$isSuccess) {
            return Response::response(50002);
        }

        return Response::response(200);
    }
}
