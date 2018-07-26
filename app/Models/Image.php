<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Created by PhpStorm.
 * User: xiexiang
 * Date: 2018/7/26
 * Time: 下午2:47
 */

class Image extends Model{

    use softDeletes;

    /**
     * @return mixed
     */
    static public function getAll()
    {
        return (new Image())->orderBy('created_at','DESC')->get();
    }

    /**
     * @param $data
     * @return mixed
     */
    static public function insertImage(array $data){
        return Image::insert([
            'src' => $data['src'],
            'description' => $data['description'],
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now()
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    static public function getDetail($id){
        return Image::find($id);
    }

}