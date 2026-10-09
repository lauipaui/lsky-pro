<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AlbumController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $albums = $user->albums()->filter($request)->paginate(40);
        $albums->getCollection()->each(function (Album $album) {
            $album->setVisible(['id', 'name', 'intro', 'image_num']);
        });
        return $this->success('success', $albums);
    }

    public function store(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'intro' => 'nullable|string|max:2000',
            ]);
        } catch (ValidationException $e) {
            return $this->fail($e->validator->errors()->first());
        }

        /** @var Album $album */
        $album = $user->albums()->create([
            'name' => $request->input('name'),
            'intro' => $request->input('intro', ''),
        ]);
        $album->setVisible(['id', 'name', 'intro', 'image_num']);
        return $this->success('创建成功', $album);
    }

    public function destroy(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $user->albums()->where('id', $request->route('id'))->delete();
        return $this->success('删除成功');
    }
}
