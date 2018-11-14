<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\CommentRepository;
use App\Http\Repositories\StationRepository;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{
    private $commentRepository;
    private $stationRepository;

    public function __construct(CommentRepository $commentRepository, StationRepository $stationRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->stationRepository = $stationRepository;
    }

    public function showAllStationsComments(int $station_id)
    {
        $this->validateIfStationExist($station_id);
        return CommentResource::collection($this->commentRepository->with(['user'])->getCommentByStation($station_id));
    }

    public function store(CommentRequest $request,int $station_id)
    {
        $this->validateIfStationExist($station_id);
        $user_id = Auth::user()->id;
        $comment = $this->commentRepository->saveCommentByStation($request, $station_id, $user_id);
        return response()->json($comment, 201);
    }

    private function validateIfStationExist($station_id){
        $station = $this->stationRepository->findWhere(['id' => $station_id]);
        if(count($station) == 0)
        {
            throw new ModelNotFoundException();
        }
    }


}
