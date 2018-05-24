<?php

namespace Fenmob\Gallery\Controllers;

use Fenmob\Gallery\Exceptions\GalleryException;
use Fenmob\Gallery\Facades\Gallery;
use Fenmob\Gallery\Models\File;
use Fenmob\Gallery\Models\FileCategory;
use Fenmob\Gallery\Services\GalleryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GalleryController extends Controller {
    private $galleryService;//图库业务逻辑

    private function success($data = [], $msg = "操作成功") {
        return response()->json([
            'msg'         => $msg,
            'status'      => 1,
            'data'        => $data,
            'status_code' => 1,
        ]);
    }

    private function error($msg = "请求错误", $data = []) {
        return response()->json([
            'msg'         => $msg,
            'status'      => 0,
            'data'        => $data,
            'status_code' => 0,
        ]);
    }

    public function __construct(GalleryService $galleryService) {
        $this->galleryService = $galleryService;
        //添加中间件
        $this->middleware(function ($request, $next) {
            if ($request->user()->shop_id) {
                config(['gallery.shop_id' => $request->user()->user_id]);
                return $next($request);
            } else {
                return $this->error("未登录无法使用该组件");
            }
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $images = File::query()->where('shop_id', 1)
                      ->when($request->get('cate_id') == -1, function ($query) {
                          return $query->onlyTrashed();
                      })
                      ->when($request->get('keyword'), function ($query, $keyword) {
                          return $query->where('name', 'like', "%{$keyword}%");
                      })
                      ->when($request->get('cate_id') != 0, function ($query) {
                          try {
                              $cate = FileCategory::query()
                                                  ->where('shop_id', config('gallery.shop_id'))
                                                  ->findOrFail(\request('cate_id'));
                              if ($cate->pid) {
                                  return $query->where('cate_id_sub', $cate->id);
                              } else {
                                  return $query->where('cate_id_top', $cate->id);
                              }
                          } catch (ModelNotFoundException $e) {
                              return $query;
                          }
                      })
                      ->orderByDesc('sort')
                      ->orderByDesc('created_at')
                      ->paginate();
        if ($request->get('cate_id') != 0) {
            return $this->success([
                'images' => $images,
            ]);
        }
        $cates = FileCategory::query()
                             ->with('child')
                             ->where('shop_id', 1)
                             ->where('pid', 0)
                             ->orderByDesc('sort')
                             ->get(['id', 'name', 'pid', 'file_num']);
        $cates = $cates->isNotEmpty() ? $cates->toArray() : [];
        array_unshift($cates, [
            'id'   => 0,
            'name' => '全部',
        ]);
        array_push($cates, [
            'id'   => '-1',
            'name' => '回收站',
        ]);
        return $this->success([
            'images' => $images,
            'cates'  => $cates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        try {
            $this->galleryService->upload();
            return $this->success();
        } catch (GalleryException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        try {
            \request()->offsetSet('id', $id);
            $this->galleryService->updateFile();
            return $this->success();
        } catch (GalleryException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        try {
            \request()->offsetSet('id', $id);
            $this->galleryService->deleteFile();
            return $this->success();
        } catch (GalleryException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 功能:移动文件
     * 函数名:moveFile
     * 作者:杨慧
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveFile() {
        try {
            $this->galleryService->moveFile();
            return $this->success();
        } catch (GalleryException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 功能:恢复文件
     * 函数名:recoverFile
     * 作者:杨慧
     * @return \Illuminate\Http\JsonResponse
     */
    public function recoverFile() {
        try {
            $this->galleryService->recoverFile();
            return $this->success();
        } catch (GalleryException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 功能:创建分类
     * 函数名:createCategory
     * 作者:杨慧
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCategory(Request $request) {
        try {
            $result = $this->galleryService->createCategory();
            return $this->success($result);
        } catch (GalleryException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 功能:更新分类
     * 函数名:updateCategory
     * 作者:杨慧
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCategory() {
        try {
            $this->galleryService->updateCategory();
            return $this->success();
        } catch (GalleryException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 功能:删除分类
     * 函数名:deleteCategory
     * 作者:杨慧
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCategory() {
        try {
            $this->galleryService->deleteCategory();
            return $this->success();
        } catch (GalleryException $e) {
            return $this->error($e->getMessage());
        }
    }
}
