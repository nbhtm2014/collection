<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:49
 **/

namespace Szkj\Collection\Requests\Task;


use Szkj\Rbac\Requests\BaseRequest;

class TaskStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'title'  => 'required',
            'platform_id' => 'required|exists:platforms,id',
            'keywords' => 'required',
        ];
    }
}