<?php

namespace App\Http\Controllers\Apis;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostsResource;
use App\Models\Post;
use App\Http\Controllers\BaseCrudController;
use App\Models\PostComment;
use Illuminate\Http\Request;

class PostController extends BaseCrudController
{
    public function __construct()
    {
        $this->middleware(["permissions:manage_users|delete_posts"])->only(["delete"]);
    }

    protected function getMainModel()
    {
        return Post::class;
    }

    protected function storeRequest()
    {
        return PostRequest::class;
    }

    protected function editRequest()
    {
        return PostRequest::class;
    }

    protected function resource():?string{
        return PostsResource::class;
    }

    protected function fieldsSearchLike():array{
        return ["title","content"];
    }

    protected function fieldsFiles(): array{
        return ["image"];
    }

    protected function withRelations(): array{
        return ["user"];
    }

    protected function resolveDataUpdate($data,$objModel)
    {
        $objModel->canProcess("update_posts");
        return $data;
    }

    ######################################### MAIN-FUNCTIONS #########################################

    public function index(callable $callback = null){
        return parent::index(function ($query){
            $user_id = auth()->id();
            return $query->withCount(["comments"])->whereNot("user_id",$user_id);
        });
    }

    public function getMyPosts(){
        return parent::index(function ($query){
            $user_id = auth()->id();
            return $query->withCount(["comments"])->where("user_id",$user_id);
        });
    }

    public function find($id){
        $post = Post::with(["comments" => function($q){
            return $q->with(["user"]);
        },"user"])->withCount(["comments"])->findOrFail($id);
        $post = PostResource::make($post);
        return $this->responseSuccess(compact("post"));
    }

    public function destroyPost($post_id){
        $post = Post::query()->findOrFail($post_id);
        $post->canProcess("delete_posts");
        $post->delete();
        return $this->responseSuccess([],"Post deleted successfully.");
    }

    public function addComment(Request $request,$post_id){
        $request->validate([
            "comment" => ["required","string"],
        ]);
        $comment = $request->comment;
        $user_id = auth()->id();
        $post = Post::query()->findOrFail($post_id);
        $comment = PostComment::query()->create(compact("comment","post_id","user_id"));
        return $this->responseSuccess(compact("comment"),"Post deleted successfully.");
    }

    #########################################/ MAIN-FUNCTIONS /#########################################
}
