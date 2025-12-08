@extends('layout')
@section('content')

@if (session()->has('message'))
<div class="alert alert-success" role="alert">    
    {{session('message')}}
</div>
@endif

@if (session()->has('error'))
<div class="alert alert-danger" role="alert">    
    {{session('error')}}
</div>
@endif

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Comment moderation</h4>
        <p class="text-muted mb-0">Comments awaiting review</p>
    </div>
    <div class="card-body">
        @if($comments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Author</th>
                            <th>Article</th>
                            <th>Comment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comments as $comment)
                        <tr>
                            <td>
                                <small>{{ $comment->created_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $comment->user->name ?? 'Неизвестен' }}</strong><br>
                                <small class="text-muted">{{ $comment->user->email ?? 'Не указан' }}</small>
                            </td>
                            <td>
                                <a href="{{ route('article.show', $comment->article->id) }}" target="_blank">
                                    {{ Str::limit($comment->article->title, 30) }}
                                </a>
                            </td>
                            <td>
                                <div style="max-width: 300px;">
                                    {{ Str::limit($comment->text, 100) }}
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group"  style="gap: 10px;">
                                    <form action="{{ route('comment.approve', $comment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('Одобрить этот комментарий?')">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('comment.reject', $comment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Отклонить и удалить этот комментарий?')">
                                            Discard
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $comments->links() }}
            </div>
        @else
            <div class="alert alert-info">
                <h5>No comments for moderation</h5>
                <p class="mb-0">All comments have been verified. New comments will appear here after they are added by users.</p>
            </div>
        @endif
    </div>
</div>

@endsection

