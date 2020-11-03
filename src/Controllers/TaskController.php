<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:42
 **/

namespace Szkj\Collection\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Szkj\Collection\Requests\Task\TaskStoreRequest;
use Szkj\Collection\Requests\Task\TaskUpdateRequest;
use Szkj\Rbac\Controllers\BaseController;
use Szkj\Rbac\Exceptions\BadRequestExceptions;

class TaskController extends BaseController
{
    public function index(Request $request)
    {

    }

    public function store(TaskStoreRequest $request)
    {
        $validated = $request->validated();
        $store_array = $this->createStoreArray($validated);
        $body = $this->assignment($store_array);
        return $this->success($body);
    }

    public function show($id)
    {

    }

    public function update(TaskUpdateRequest $request, $id)
    {

    }

    public function destroy($id)
    {

    }

    /**
     * @param array $validated
     * @return array
     */
    protected function createStoreArray(array $validated): array
    {
        $pcd = config('szkj-collection.pcd');
        $pcd_str = '';
        foreach ($pcd as $k => $v) {
            if (empty($v)) {
                unset($pcd[$k]);
            } else {
                $pcd_str .= $v . ',';
            }
        }
        $validated['user_id'] = auth()->user()->id;
        $validated['pcd'] = trim($pcd_str, ',');
        return $validated;
    }

    /**
     * @param array $array
     * @return array
     */
    protected function assignment(array $array) : array
    {
        $tag = DB::connection(config('database.default'))->table('platforms')->where('id', $array['platform_id'])->first()->tag;
        $body = $this->$tag($array);
        return $body;
    }

    /**
     * @param array $validated
     * @return array
     */
    protected function items(array $validated): array
    {
        $pcd = explode(',', $validated['pcd']);

        return [
            "description" => $validated['title'],
            "title"       => $validated['title'],
            "config"      => [
                [
                    'dataPush'     => config('szkj-collection.rabbitmq.data-push'),
                    "description"  => "地区&关键词采集商品, 支持平台[淘宝, 天猫, 阿里巴巴]",
                    "props"        => [
                        "body" => [
                            "pcd"           => [
                                "hint"  => "地区",
                                "name"  => "pcd",
                                "value" => array_values($pcd),
                            ],
                            "chkViolations" => [
                                "hint"  => "是否检查违规",
                                "name"  => "chkViolations",
                                "value" => config('szkj-collection.check-violation'),
                            ],
                            "keyword"       => [
                                "hint"  => "关键词",
                                "name"  => "keyword",
                                "value" => $validated['keywords'],
                            ],
                            "platform"      => [
                                "hint"  => "采集平台",
                                "name"  => "platform",
                                "value" => $validated['platform_id'],
                            ],
                        ],
                    ],
                    "businessType" => [
                        "value" => 4,
                        "name"  => "商品采集(地区&关键词)",
                    ],
                ],
            ],
            "periodType"  => "4",
        ];
    }

    protected function wechat(string $keywords)
    {

    }
}