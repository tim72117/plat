<?php
use Plat\Files\CommFile;

return [
    'open' => function() {
        return 'apps.poster';
    },
    'getPosts' => function() {

        $member = Auth::user()->members()->logined()->orderBy('logined_at', 'desc')->first();

        $posts = $member->project->posts->load('files');

        return ['posts' => $posts];

    },
    'savePost' => function() {
        $member = Auth::user()->members()->logined()->orderBy('logined_at', 'desc')->first();
        $context = json_decode(urldecode(base64_decode(Input::get('context'))));

        if (!Input::has('id')) {

            $post = new Plat\Post([
                'title' => Input::get('title'),
                'context' => $context,
                'publish_at' => Carbon\Carbon::parse(Input::get('publish_at'))->toDateString(),
                'display_at' => '{"intro":false}',
                'created_by' => Auth::user()->id,
            ]);

            $updated = $member->project->posts()->save($post);

            $post->id = (string)$post->id;

            $method = 'insert';

        }else{

            $post = Plat\Post::find(Input::get('id'));

            $updated = $post->update([
                'title' => Input::get('title'),
                'context' => $context,
                'publish_at' => Carbon\Carbon::parse(Input::get('publish_at'))->toDateString(),
            ]);

            $method = 'update';
        }

        return ['post' => $post, 'method' => $method];
    },
    'setDisplay' => function() {
        $updated = Plat\Post::find(Input::get('id'))->update(['display_at' => json_encode(Input::get('display_at'))]);

        return ['updated' => $updated];
    },
    'deletePost' => function() {
        $post = Plat\Post::find(Input::get('id'));
        $post->files()->detach();

        return ['deleted' => $post->delete()];
    },
    'uploadFile' => function(){

        $file = new Files(['type' => 3, 'title' => Input::file('file_upload')->getClientOriginalName()]);

        $user = Auth::user();

        $file_upload = new CommFile($file, $user);

        $file_upload->upload(Input::file('file_upload'));

        Plat\Post::find(Input::get('post_id'))->files()->attach($file_upload->id());

        return ['files' => Plat\Post::find(Input::get('post_id'))->files];
    },
    'deleteFile' => function() {

        $pivot_id = Input::get('file')['pivot']['id'];

        return ['deleted' => Plat\Project\PostFile::find($pivot_id)->delete()];
    },
];