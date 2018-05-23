<?php
/**
 * Created by PhpStorm.
 * User: yanghui
 * Date: 2018/5/23
 * Time: 13:51
 */

namespace Fenmob\Gallery\Services;


use Fenmob\Gallery\Exceptions\GalleryException;
use Fenmob\Gallery\Facades\Gallery;
use Fenmob\Gallery\Models\File;
use Fenmob\Gallery\Models\FileCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class GalleryService {
    protected $shop_id;

    public function __construct() {
        $this->shop_id = config('gallery.shop_id');
    }

    /**
     * 功能:
     * 函数名:validate
     * 作者:杨慧
     * @throws GalleryException
     */
    private function validate() {
        if (request()->hasFile(config('gallery.file_field'))) {
            return;
        }
        throw new GalleryException('上传文件不存在');
    }

    /**
     * 功能:
     * 函数名:upload
     * 作者:杨慧
     * @return array
     * @throws GalleryException
     */
    public function upload() {
        $this->validate();
        try {
            $result = Gallery::uploader()->upload();
        } catch (\Exception $e) {
            throw new GalleryException($e->getMessage());
        }
        $file     = request()->file(config('gallery.file_field'));
        $filename = $file->getBasename("." . $file->getClientOriginalExtension());
        $file     = File::query()->create([
            'shop_id'      => config('gallery.shop_id'),
            'name'         => $filename,
            'old_name'     => $filename,
            'path'         => $result['path'],
            'sort'         => 0,
            'show_gallery' => 1,
        ]);
        //如果在某个分类上传图片
        if ($cate_id = request('cate_id')) {
            try {
                $category          = FileCategory::query()
                                                 ->where('shop_id', config('gallery.shop_id'))
                                                 ->findOrFail($cate_id);
                $file->cate_id_top = $category->pid ?: $category->id;
                $file->cate_id_sub = $category->id;
                $category->files()->save($file);
                $category->increment('file_num');
            } catch (ModelNotFoundException $e) {

            }
        }
        return [
            'id'   => $file->id,
            'path' => $file->path,
        ];
    }

    /**
     * 功能:
     * 函数名:updateFile
     * 作者:杨慧
     * @throws GalleryException
     */
    public function updateFile() {
        try {
            $file = File::query()->where('shop_id', $this->shop_id)->findOrFail(request('id'));
            $file->fillable([
                'name', 'sort',
            ])->fill([
                'name' => request('name'),
                'sort' => request('sort'),
            ])->saveOrFail();
        } catch (ModelNotFoundException $e) {
            throw new GalleryException("文件不存在");
        } catch (\Throwable $e) {
            throw new GalleryException("数据库错误");
        }
    }

    /**
     * 功能:
     * 函数名:categoryValidate
     * 作者:杨慧
     * @throws GalleryException
     */
    private function categoryValidate() {
        $validator = \Validator::make(request()->all(), [
            'name' => [
                'required',
                Rule::unique('file_category')->ignore(request('id'))->where(function ($query) {
                    return $query->where('shop_id', config('gallery.shop_id'));
                }),
            ],
        ], [
            'name.required' => '分类名不能为空',
            'name.unique'   => '分类名不能重复',
        ]);
        if ($validator->fails()) {
            throw new GalleryException($validator->errors()->first());
        }
    }

    /**
     * 功能:创建分类
     * 函数名:createCategory
     * 作者:杨慧
     * @throws GalleryException
     */
    public function createCategory() {
        //新增分类ID重置为0
        request()->offsetSet('id', 0);
        $this->categoryValidate();
        $data            = request()->all();
        $data['shop_id'] = config('gallery.shop_id');
        $category        = FileCategory::query()->create($data);
        return [
            'id'   => $category->id,
            'name' => $category->name,
        ];
    }

    /**
     * 功能:更新分类
     * 函数名:updateCategory
     * 作者:杨慧
     * @throws GalleryException
     */
    public function updateCategory() {
        $this->categoryValidate();
        $condition = [
            'shop_id' => config('gallery.shop_id'),
        ];
        $data      = [
            'name' => request('name'),
            'pid'  => request('pid', 0),
        ];
        try {
            $cate = FileCategory::query()->where($condition)->findOrFail(request('id'));
            File::query()->where($condition)->where([
                'filetable_id'   => $cate->id,
                'filetable_type' => FileCategory::class,
            ])->update([
                'cate_id_top' => request('pid', 0) ?: $cate->id,
                'cate_id_sub' => $cate->id,
            ]);
            $cate->fill($data)->save();
        } catch (ModelNotFoundException $e) {
            throw new GalleryException("不存在该分类");
        }
    }

    /**
     * 功能:移动文件
     * 函数名:moveFile
     * 作者:杨慧
     * @throws GalleryException
     */
    public function moveFile() {
        try {
            $files = File::query()->where('shop_id', config('gallery.shop_id'))->findOrFail(request('id'));
            $cate  = FileCategory::query()
                                 ->where('shop_id', config('gallery.shop_id'))
                                 ->findOrFail(request('cate_id'));

            if (is_array(request('id'))) {
                foreach ($files as &$file) {
                    $file->cate_id_top = $cate->pid ?: $cate->id;
                    $file->cate_id_sub = $cate->id;
                }
            } else {
                $files->cate_id_top = $cate->pid ?: $cate->id;
                $files->cate_id_sub = $cate->id;
                $files              = [$files];
            }

            $cate->files()->saveMany($files);
        } catch (ModelNotFoundException $e) {
            throw new GalleryException("文件或分类不存在");
        }
    }

    /**
     * 功能:恢复已上删除文件
     * 函数名:recoverFile
     * 作者:杨慧
     * @throws GalleryException
     */
    public function recoverFile() {
        try {
            $files = File::query()->onlyTrashed()->findOrFail(request('ids', 0));
            $files->restore();
        } catch (ModelNotFoundException $e) {
            throw new GalleryException("删除的文件未找到");
        }
    }

    /**
     * 功能:删除文件
     * 函数名:deleteFile
     * 作者:杨慧
     * @throws GalleryException
     */
    public function deleteFile() {
        try {
            $files = File::query()->where('shop_id', config('gallery.shop_id'))->findOrFail(request('ids'));
            $files->delete();
        } catch (ModelNotFoundException $e) {
            throw new GalleryException("文件不存在");
        }
    }

    /**
     * 功能:删除分类
     * 函数名:deleteCategory
     * 作者:杨慧
     * @throws GalleryException
     */
    public function deleteCategory() {
        try {
            $cate = FileCategory::query()->where('shop_id', config('gallery.shop_id'))->findOrFail(request('cate_id'));
            //如果是二级分类
            if ($cate->pid) {
                File::query()
                    ->withTrashed()
                    ->where('shop_id', config('gallery.shop_id'))
                    ->where('cate_id_sub', $cate->id)
                    ->update([
                        'cate_id_sub' => 0,
                    ]);
            } else {
                File::query()
                    ->withTrashed()
                    ->where('shop_id', config('gallery.shop_id'))
                    ->where('cate_id_top', $cate->id)
                    ->update([
                        'cate_id_sub' => 0,
                        'cate_id_top' => 0,
                    ]);
            }
            $cate->delete();
        } catch (ModelNotFoundException $e) {
            throw new GalleryException("分类不存在");
        }
    }
}