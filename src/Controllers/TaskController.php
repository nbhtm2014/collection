<?php
/**
 * Creator htm
 * Created by 2020/11/3 16:42
 **/

namespace Szkj\Collection\Controllers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Illuminate\Http\JsonResponse;
use Szkj\Collection\Models\Task;
use Szkj\Collection\Requests\Task\TaskStoreRequest;
use Szkj\Collection\Requests\Task\TaskUpdateRequest;
use Szkj\Rbac\Controllers\BaseController;
use Szkj\Rbac\Exceptions\BadRequestExceptions;

class TaskController extends BaseController
{

    public $client;

    public $host;

    public function __construct()
    {
        $this->client = new Client();
        $this->checkConfig();
        $this->host = config('szkj-collection.assignment-host');
    }

    public function index(Request $request)
    {

    }

    /**
     * @param TaskStoreRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function store(TaskStoreRequest $request)
    {
        $validated = $request->validated();
        $store_array = $this->createStoreArray($validated);
        $body = $this->assignment($store_array);
        if ($this->startCollection($body, $store_array)) {
            return $this->success();
        }
        return $this->error(422);
    }

    /**
     * @param $id
     */
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
    protected function assignment(array $array): array
    {
        $tag = DB::connection(config('database.default'))->table('platforms')->where('id', $array['platform_id'])->first()->tag;
        return $this->$tag($array);
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
                    'dataPush'     => config('szkj-collection.rabbitmq.data-push-queue'),
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

    /**
     * @param array $body
     * @param array $store_array
     * @return bool
     * @throws GuzzleException
     */
    protected function startCollection(array $body, array $store_array): bool
    {

        $res = $this->client->request('POST', $this->host . '/assignment/add-assignment',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json'    => $body,
            ]);
        $res = json_decode($res->getBody(), true);
        if (is_array($res) && $res['code'] == 200) {
            $store_array['system_id'] = $res['data']['id'];
            $store_array['es_id'] = $res['data']['config'][0]['id'];
            $task = Task::query()->create($store_array);
            /**
             * @var Task $task
             */
            $this->modifyAssignment($task);
            return $this->enableAssignment($task);
        }
        return false;
    }

    /**
     * @param Task $task
     * @throws GuzzleException
     */
    protected function modifyAssignment(Task $task)
    {
        $this->client->request('PUT', $this->host . '/assignment/modify-assignment', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json'    => ['id' => $task->system_id, 'priority' => config('szkj-collection.priority')],
        ]);
    }

    /**
     * @param Task $task
     * @return bool
     * @throws GuzzleException
     */
    protected function enableAssignment(Task $task): bool
    {
        $res = $this->client->request('PUT', $this->host . '/assignment/enable-assignment/' . $task->system_id);
        $res = json_decode($res->getBody(), true);
        if (is_array($res) && $res['code'] == 200) {
            $task->status = 1;
            $task->save();
            return true;
        }
        return false;
    }


    protected function checkConfig(){
        if (empty(config('szkj-collection.pcd.province'))){
            throw new BadRequestExceptions(422,'请先配置地区');
        }

        if (empty(config('szkj-collection.elasticsearch-hosts'))){
            throw new BadRequestExceptions(422,'请先配置ES地址');
        }

        if (empty(config('szkj-collection.assignment-host'))){
            throw new BadRequestExceptions(422,'请先配置采集服务器地址');
        }

        if (empty(config('szkj-collection.rabbitmq.data-push-queue'))){
            throw new BadRequestExceptions(422,'请先配置数据推送队列');
        }
    }
}