<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-14
 * Time: 10:08 AM
 */

namespace App\Http\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

class ClientRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "Laravel\\Passport\\Client";
    }
}