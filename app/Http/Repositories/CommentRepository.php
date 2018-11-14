<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-14
 * Time: 10:08 AM
 */

namespace App\Http\Repositories;

use App\Http\Requests\CommentRequest;
use Prettus\Repository\Eloquent\BaseRepository;

class CommentRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "App\\Comment";
    }

    function getCommentByStation($stations){
        $this->orderBy('created_at', 'desc');
        return $this->findByField('station_id', $stations);
    }

    function saveCommentByStation(CommentRequest $request, $station_id, $user_id){
        $comment = array_merge($request->all(), ["station_id" => $station_id, "user_id" =>$user_id]);
        $this->create($comment);
        return $comment;
    }



}
