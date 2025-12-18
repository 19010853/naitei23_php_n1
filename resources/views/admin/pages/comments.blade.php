@php
    use Illuminate\Support\Str;
@endphp

@extends('admin.layouts.app')

@section('page-title', __('common.comments'))

@section('content')
    <div class="table-wrapper">
        <div class="table-head">
            <div class="table-title">{{ __('common.recent_comments') }}</div>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('common.user') }}</th>
                    <th>{{ __('common.object') }}</th>
                    <th>{{ __('common.content') }}</th>
                    <th>{{ __('common.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comments as $comment)
                    <tr>
                        <td>{{ $comment->user->name ?? __('common.anonymous') }}</td>
                        <td>
                            @php
                                $shortType = class_basename($comment->commentable_type);
                            @endphp
                            <span class="chip">{{ $shortType }}</span>
                        </td>
                        <td data-full-content="{{ $comment->body }}">{{ Str::limit($comment->body, 100) }}</td>
                        <td>{{ optional($comment->created_at)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">{{ __('common.no_comments') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

