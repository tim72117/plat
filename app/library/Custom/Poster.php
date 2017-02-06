<?php

namespace Plat\Files\Custom;

use Input;
use DB;
use Auth;
use Carbon\Carbon;
use Files;
use Plat\Files\CommFile;
use Plat\Post;
use Plat\Project\PostFile;

class Poster {

    public $full = false;

    public function open()
    {
        return 'apps.poster';
    }

    public function getPosts()
    {
        $member = Auth::user()->members()->logined()->orderBy('logined_at', 'desc')->first();

        $posts = $member->project->posts->load('files');

        return ['posts' => $posts];
    }

    public function savePost()
    {
        $member = Auth::user()->members()->logined()->orderBy('logined_at', 'desc')->first();
        $context = json_decode(urldecode(base64_decode(Input::get('context'))));

        if (!Input::has('id')) {

            $post = new Post([
                'title' => Input::get('title'),
                'context' => $context,
                'perpetual' => false,
                'publish_at' => Carbon::parse(Input::get('publish_at'))->toDateString(),
                'display_at' => '{"intro":false}',
                'created_by' => Auth::user()->id,
            ]);

            $updated = $member->project->posts()->save($post);

            $post->id = (string)$post->id;

            $method = 'insert';

        }else{

            $post = Post::find(Input::get('id'));

            $updated = $post->update([
                'title' => Input::get('title'),
                'context' => $context,
                'publish_at' => Carbon::parse(Input::get('publish_at'))->toDateString(),
            ]);

            $method = 'update';
        }

        return ['post' => $post, 'method' => $method];
    }

    public function setDisplay()
    {
        $updated = Post::find(Input::get('id'))->update(['display_at' => json_encode(Input::get('display_at'))]);

        return ['updated' => $updated];
    }

    public function deletePost()
    {
        $post = Post::find(Input::get('id'));
        $post->files()->detach();

        return ['deleted' => $post->delete()];
    }

    public function uploadFile()
    {
        $file = new Files(['type' => 3, 'title' => Input::file('file_upload')->getClientOriginalName()]);

        $user = Auth::user();

        $file_upload = new CommFile($file, $user);

        $file_upload->upload(Input::file('file_upload'));

        Post::find(Input::get('post_id'))->files()->attach($file_upload->id());

        return ['files' => Post::find(Input::get('post_id'))->files];
    }

    public function deleteFile()
    {
        $pivot_id = Input::get('file')['pivot']['id'];

        return ['deleted' => PostFile::find($pivot_id)->delete()];
    }

    public function setPerpetual()
    {
        $updated = Post::find(Input::get('id'))->update(['perpetual' => Input::get('perpetual')]);

        return ['updated' => $updated];
    }
}