@extends('layouts.app')
@section('title', 'Notifications - AstroGuide')

@section('content')

<style>
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-top: 10px;
    }

    .dataTables_info {
        float: left;
        font-size: 14px;
        color: #333;
    }

    .dataTables_paginate {
        float: right;
    }

    .pagination {
        display: flex;
        justify-content: flex-end;
        padding-left: 0;
        list-style: none;
        margin: 0;
    }

    .pagination .page-item {
        display: inline-block;
        margin: 0 2px;
    }

    .pagination .page-link {
        padding: 6px 12px;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: black;
        font-size: 16px;
        text-decoration: none;
        text-align: center;
        border-radius: 4px;
        transition: background-color 0.3s ease-in-out;
    }

    .pagination .page-link:hover {
        background-color: #f1f1f1;
        border-color: #e5cd7f;
    }

    .pagination .page-item.active .page-link {
        background-color: #e5cd7f;
        color: white;
        border-color: #e5cd7f;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #e9ecef;
        pointer-events: none;
        border-color: #dee2e6;
    }

    .single-notification {
        cursor: pointer;
    }

    .unread-notification {
        background-color: #fff8e1;
        font-weight: bold;
        border-left: 5px solid #ffc107;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 text-gray-800">Notifications</h1>
        <button type="button" class="btn btn-primary" onclick="markCompleted()">MARK ALL AS READ</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Notification List</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse ($notificationList as $notification)
                @php $isRead = $notification->is_read_by_admin; @endphp
                <div class="col-md-12 mb-4">
                    <div class="p-3 border rounded shadow-sm {{ $isRead ? 'bg-white' : 'unread-notification single-notification' }}"
                        data-id="{{ $notification->id }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="d-flex justify-content-center align-items-center rounded-circle bg-light mr-3"
                                    style="width: 40px; height: 40px;">
                                    @if($isRead)
                                    <i class="fa fa-check-circle text-success" title="Read"></i>
                                    @else
                                    <i class="fa fa-envelope text-warning" title="Unread"></i>
                                    @endif
                                </div>
                                <strong>{{ $notification->title_admin }}</strong>
                            </div>
                            <div class="text-muted small d-flex align-items-center">
                                <i class="fa fa-clock mr-1"></i>
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="mb-0">{{ $notification->message_to_admin }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-md-12 text-center text-muted">No notifications found.</div>
                @endforelse

                {{--<div class="pagination-container">
                    <div class="dataTables_info" role="status" aria-live="polite">
                        Showing {{ $notificationList->firstItem() }} to {{ $notificationList->lastItem() }} of {{ $notificationList->total() }} entries
            </div>
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    <li class="paginate_button page-item {{ $notificationList->onFirstPage() ? 'disabled' : '' }}">
                        <a href="{{ $notificationList->previousPageUrl() }}" class="page-link">Previous</a>
                    </li>

                    @foreach ($notificationList->getUrlRange(1, $notificationList->lastPage()) as $page => $url)
                    <li class="paginate_button page-item {{ $page == $notificationList->currentPage() ? 'active' : '' }}">
                        <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                    </li>
                    @endforeach

                    <li class="paginate_button page-item {{ $notificationList->hasMorePages() ? '' : 'disabled' }}">
                        <a href="{{ $notificationList->nextPageUrl() }}" class="page-link">Next</a>
                    </li>
                </ul>
            </div>
        </div>--}}

        @php
        $totalPages = $notificationList->lastPage();
        $currentPage = $notificationList->currentPage();
        $pageLinks = [];

        // Always show first 2 pages
        for ($i = 1; $i <= 2; $i++) {
            $pageLinks[]=$i;
            }

            // Show pages around current (2 before, current, 2 after)
            for ($i=$currentPage - 2; $i <=$currentPage + 2; $i++) {
            if ($i> 2 && $i < $totalPages - 1) {
                $pageLinks[]=$i;
                }
                }

                // Always show last 2 pages
                for ($i=$totalPages - 1; $i <=$totalPages; $i++) {
                if ($i> 2) {
                $pageLinks[] = $i;
                }
                }

                // Remove duplicates and sort
                $pageLinks = array_unique($pageLinks);
                sort($pageLinks);
                @endphp

                <div class="pagination-container">
                    <div class="dataTables_info" role="status" aria-live="polite">
                        Showing {{ $notificationList->firstItem() }} to {{ $notificationList->lastItem() }} of {{ $notificationList->total() }} entries
                    </div>
                    <div class="dataTables_paginate paging_simple_numbers">
                        <ul class="pagination">

                            {{-- Previous Button --}}
                            <li class="paginate_button page-item {{ $notificationList->onFirstPage() ? 'disabled' : '' }}">
                                <a href="{{ $notificationList->previousPageUrl() }}" class="page-link">Previous</a>
                            </li>

                            {{-- Dynamic Page Links --}}
                            @php $lastPage = 0; @endphp
                            @foreach ($pageLinks as $page)
                            @if ($lastPage + 1 < $page)
                                <li class="paginate_button page-item disabled"><span class="page-link">...</span></li>
                                @endif

                                <li class="paginate_button page-item {{ $page == $currentPage ? 'active' : '' }}">
                                    <a href="{{ $notificationList->url($page) }}" class="page-link">{{ $page }}</a>
                                </li>

                                @php $lastPage = $page; @endphp
                                @endforeach

                                {{-- Next Button --}}
                                <li class="paginate_button page-item {{ $notificationList->hasMorePages() ? '' : 'disabled' }}">
                                    <a href="{{ $notificationList->nextPageUrl() }}" class="page-link">Next</a>
                                </li>
                        </ul>
                    </div>
                </div>



    </div>
</div>
</div>
</div>

<script>
    function markCompleted() {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to mark all notifications as read?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, mark all",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('notification.markAllRead') }}", {
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    if (res.success) {
                        Swal.fire("Marked!", "All notifications are marked as read.", "success").then(() => {
                            location.reload();
                        });
                    }
                });
            }
        });
    }

    $(document).on('click', '.single-notification', function() {
        let notifId = $(this).data('id');
        Swal.fire({
            title: "Mark as read?",
            text: "Do you want to mark this notification as read?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('notification.markRead') }}", {
                    _token: "{{ csrf_token() }}",
                    id: notifId
                }, function(res) {
                    if (res.success) {
                        Swal.fire("Marked!", "Notification marked as read.", "success").then(() => {
                            location.reload();
                        });
                    }
                });
            }
        });
    });
</script>
@endsection