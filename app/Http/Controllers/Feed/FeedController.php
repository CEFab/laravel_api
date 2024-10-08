<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Comment;
use App\Models\Feed;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{

    public function index()
    {
        $feeds = Feed::with('user')->latest()->get();
        return response([
            'feeds' => $feeds
        ], 200);
    }

    public function store(PostRequest $request)
    {
        $request->validated();

        // auth()->user()->feeds()->create([
        //     'content' => $request->content
        // ]);

        Feed::create([
            // 'user_id' => auth()->id(),
            'user_id' => Auth::id(),
            'content' => $request->content
        ]);

        return response([
            'message' => 'success',
        ], 201);
    }

    public function destroy($id)
    {
        // Busca el post por id
        $feed = Feed::find($id);

        //Verificar si el post existe
        if (!$feed) {
            return response([
                'message' => '404 Not found'
            ], 404);
        }

        // Verificar si el usuario autenticado es el creador del post
        if ($feed->user_id !== Auth::id()) {
            return response([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Eliminar el post
        $feed->delete();

        return response([
            'message' => 'success'
        ], 200);
    }


    public function likePost($feed_id)
    {
        // select feed with feed_id
        $feed = Feed::whereId($feed_id)->first();

        if (!$feed) {
            return response([
                'message' => '404 Not found'
            ], 500);
        }

        // Unlike post
        // $unlike_post = Like::where('user_id', auth()->id())->where('feed_id', $feed_id)->delete();
        $unlike_post = Like::where('user_id', Auth::id())->where('feed_id', $feed_id)->delete();
        if ($unlike_post) {
            return response([
                'message' => 'Unliked'
            ], 200);
        }

        // Like post
        $like_post = Like::create([
            // 'user_id' => auth()->id(),
            'user_id' => Auth::id(),
            'feed_id' => $feed_id
        ]);
        if ($like_post) {
            return response([
                'message' => 'liked'
            ], 200);
        }
    }

    public function comment(Request $request, $feed_id)
    {

        $request->validate([
            'body' => 'required'
        ]);

        Comment::create([
            // 'user_id' => auth()->id(),
            'user_id' => Auth::id(),
            'feed_id' => $feed_id,
            'body' => $request->body
        ]);

        return response([
            'message' => 'success'
        ], 201);
    }

    public function getComments($feed_id)
    {
        $comments = Comment::with('feed')->with('user')->whereFeedId($feed_id)->latest()->get();

        return response([
            'comments' => $comments
        ], 200);
    }
}
