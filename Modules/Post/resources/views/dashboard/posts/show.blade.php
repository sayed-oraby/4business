@extends('layouts.dashboard.master')

@section('title', __('post::post.posts.show_title'))
@section('page-title', __('post::post.posts.show_title'))

@section('content')
<!--begin::Navbar-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <!--begin::Image-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    @if($post->cover_image_url)
                    <img src="{{ $post->cover_image_url }}" alt="image" style="object-fit: cover;" />
                    @else
                    <div class="symbol-label fs-1 fw-bold bg-light-primary text-primary">
                        <i class="ki-duotone ki-picture fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    @endif
                    <div
                        class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px">
                    </div>
                </div>
            </div>
            <!--end::Image-->
            <!--begin::Wrapper-->
            <div class="flex-grow-1">
                <!--begin::Head-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                        <!--begin::Name-->
                        <div class="d-flex align-items-center mb-2">
                            <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $post->full_name
                                }}</a>
                            <a href="#" class="btn btn-sm btn-light-success fw-bold ms-2 fs-8 py-1 px-3">{{
                                $post->user->email }}</a>
                        </div>
                        <!--end::Name-->
                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                <i class="ki-duotone ki-geolocation fs-4 me-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                                {{ @$post->state->name ?? '-' }}
                            </a>
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                <i class="ki-duotone ki-sms fs-4 me-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                                {{ $post->mobile_number }}
                            </a>
                            {{-- <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                <i class="ki-duotone ki-briefcase fs-4 me-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                                {{ $post->years_of_experience }} Years Exp.
                            </a> --}}
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::User-->
                    <!--begin::Actions-->
                    <div class="d-flex my-4">
                        <a href="{{ route('dashboard.posts.edit', $post->id) }}" class="btn btn-sm btn-light me-2">
                            <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span
                                    class="path2"></span></i>
                            {{ __('post::post.actions.edit') }}
                        </a>
                        @if($post->status === 'pending')
                        <button type="button" class="btn btn-sm btn-success me-2" data-post-action="approve">
                            {{ __('post::post.actions.approve') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" data-post-action="reject">
                            {{ __('post::post.actions.reject') }}
                        </button>
                        @else
                        <span
                            class="badge badge-light-{{ $post->status === 'approved' ? 'success' : 'danger' }} fs-6 px-3 py-2">
                            {{ ucfirst($post->status) }}
                        </span>
                        @endif
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Head-->
                <!--begin::Info-->
                <div class="d-flex flex-wrap flex-stack">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bold counted">{{ $post->postType->name }}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">{{ __('post::post.posts.form.type') }}</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bold counted">{{ $post->package->title }}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">{{ __('post::post.posts.form.package') }}
                                </div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bold counted">{{ $post->category->title ?? '-' }}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">{{ __('post::post.posts.form.category') }}
                                </div>
                            </div>
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Info-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Details-->
        <div class="separator my-5"></div>
        <!--begin::Nav-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 active" data-bs-toggle="tab"
                    href="#kt_post_overview">{{ __('post::post.posts.overview') }}</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10" data-bs-toggle="tab" href="#kt_post_attachments">{{
                    __('post::post.posts.attachments_tab') }}</a>
            </li>
            {{-- <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10" data-bs-toggle="tab" href="#kt_post_job_offers">{{
                    __('post::post.posts.tabs.job_offers') }}</a>
            </li> --}}
        </ul>
        <!--end::Nav-->
    </div>
</div>
<!--end::Navbar-->

<!--begin::Content-->
<div class="tab-content" id="myTabContent">
    <!--begin::Overview Tab-->
    <div class="tab-pane fade show active" id="kt_post_overview" role="tabpanel">
        <div class="card mb-5 mb-xl-10">
            <div class="card-header cursor-pointer">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">{{ $post->title }}</h3>
                </div>
            </div>
            <div class="card-body p-9">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.form.description') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $post->description }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.form.price') }}</label>
                    <div class="col-lg-8 fv-row">
                        <span class="fw-semibold text-gray-800 fs-6">{{ $post->price }} د.ك </span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.form.mobile') }}</label>
                    <div class="col-lg-8 fv-row">
                        <span class="fw-semibold text-gray-800 fs-6">{{ $post->mobile_number }}</span>
                    </div>
                </div>
                {{-- <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.form.gender') }}</label>
                    <div class="col-lg-8 fv-row">
                        <span class="fw-semibold text-gray-800 fs-6">{{ __('post::post.posts.gender_' . $post->gender)
                            }}</span>
                    </div>
                </div> --}}
                {{-- <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.form.birthdate') }}</label>
                    <div class="col-lg-8 fv-row">
                        <span class="fw-semibold text-gray-800 fs-6">{{ $post->birthdate ?
                            $post->birthdate->format('Y-m-d') : '-' }}</span>
                    </div>
                </div> --}}
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.form.is_paid') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $post->is_paid ? __('post::post.posts.yes') :
                            __('post::post.posts.no') }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.dates') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">
                            {{ $post->start_date ? $post->start_date->format('Y-m-d') : '-' }}
                            <span class="text-gray-400 mx-2">{{ __('post::post.posts.to') }}</span>
                            {{ $post->end_date ? $post->end_date->format('Y-m-d') : '-' }}
                        </span>
                    </div>
                </div>

                @if($post->skills->count() > 0)
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.form.skills') }}</label>
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($post->skills as $skill)
                            <span class="badge badge-light-primary fw-bold fs-7">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if($post->cover_image)
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('post::post.posts.form.cover_image') }}</label>
                    <div class="col-lg-8">
                        <div class="card card-bordered w-100 w-lg-25">
                            <!-- Smaller width -->
                            <div class="card-body p-2">
                                <img src="{{ asset('storage/'.$post->cover_image) }}" alt="Cover" class="w-100 rounded">
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!--end::Overview Tab-->

    <!--begin::Attachments Tab-->
    <div class="tab-pane fade" id="kt_post_attachments" role="tabpanel">
        <div class="card mb-5 mb-xl-10">
            <div class="card-header">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">{{ __('post::post.posts.form.attachments') }}</h3>
                </div>
            </div>
            <div class="card-body p-9">
                @if($post->attachments->count() > 0)
                <div class="row g-6" id="attachments-container">
                    @foreach($post->attachments as $attachment)
                    <div class="col-md-6 col-lg-4 col-xl-3 attachment-item" id="attachment-{{ $attachment->id }}">
                        <div class="card h-100 border border-dashed p-0 overflow-hidden position-relative hover-elevate-up transition-300">
                            <!-- Delete Button -->
                            <button type="button" 
                                    class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-2 z-index-1 shadow-sm"
                                    onclick="deleteAttachment('{{ route('dashboard.posts.attachments.destroy', ['post' => $post->id, 'attachment' => $attachment->id]) }}', 'attachment-{{ $attachment->id }}')"
                                    title="Delete Attachment">
                                <i class="ki-duotone ki-trash fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </button>

                            <div class="card-body p-5 d-flex flex-column text-center h-100">
                                @php
                                    $ext = strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                                @endphp

                                <div class="mb-4 bg-light rounded overflow-hidden d-flex justify-content-center align-items-center position-relative cursor-pointer" 
                                     style="height: 200px;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#attachmentModal"
                                     data-file-url="{{ asset('storage/'.$attachment->file_path) }}"
                                     data-file-type="{{ $ext }}"
                                     data-file-title="{{ $attachment->title ?? basename($attachment->file_path) }}">
                                    
                                    @if($isImage)
                                        <img src="{{ asset('storage/'.$attachment->file_path) }}" class="mw-100 mh-100 object-fit-contain" alt="{{ $attachment->title }}">
                                    @else
                                        <i class="ki-duotone ki-file-added fs-5x text-primary">
                                            <span class="path1"></span><span class="path2"></span>
                                        </i>
                                    @endif
                                </div>
                                
                                {{-- <div>
                                     <span class="text-gray-800 fw-bold fs-6 d-block text-truncate mb-1" title="{{ $attachment->title ?? basename($attachment->file_path) }}">
                                        {{ $attachment->title ?? basename($attachment->file_path) }}
                                     </span>
                                     <span class="text-gray-400 fw-semibold fs-7 badge badge-light">{{ strtoupper($attachment->file_type) }}</span>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                    <i class="ki-duotone ki-information fs-2tx text-warning me-4"><span class="path1"></span><span
                            class="path2"></span><span class="path3"></span></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">No Attachments</h4>
                            <div class="fs-6 text-gray-700">This post has no uploaded attachments.</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!--end::Attachments Tab-->

    <!--begin::Job Offers Tab-->
    {{-- <div class="tab-pane fade" id="kt_post_job_offers" role="tabpanel">
        <div class="card mb-5 mb-xl-10">
            <div class="card-header">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">{{ __('post::post.posts.tabs.job_offers') }}</h3>
                </div>
            </div>
            <div class="card-body p-9">
                @if($post->jobOffers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                            <tr>
                                <td class="min-w-150px">{{ __('post::post.posts.job_offers.employer') }}</td>
                                <td class="min-w-100px">{{ __('post::post.posts.job_offers.salary') }}</td>
                                <td class="min-w-100px">{{ __('post::post.posts.job_offers.joining_date') }}</td>
                                <td class="min-w-200px">{{ __('post::post.posts.job_offers.description') }}</td>
                                <td class="min-w-100px">{{ __('post::post.posts.job_offers.status') }}</td>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach($post->jobOffers as $offer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px me-5">
                                            @if($offer->user->avatar_url)
                                            <img src="{{ $offer->user->avatar_url }}" alt="" />
                                            @else
                                            <span class="symbol-label bg-light-primary text-primary fw-bold">{{
                                                substr($offer->user->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">{{
                                                $offer->user->name }}</a>
                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{
                                                $offer->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($offer->salary, 2) }}</td>
                                <td>{{ $offer->joining_date->format('Y-m-d') }}</td>
                                <td>{{ Str::limit($offer->description, 50) }}</td>
                                <td>
                                    <span
                                        class="badge badge-light-{{ $offer->status === 'accepted' ? 'success' : ($offer->status === 'rejected' ? 'danger' : 'warning') }} fs-7 fw-bold">
                                        {{ ucfirst($offer->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                    <i class="ki-duotone ki-briefcase fs-2tx text-primary me-4"><span class="path1"></span><span
                            class="path2"></span></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">{{ __('post::post.posts.messages.no_job_offers') }}</h4>
                            <div class="fs-6 text-gray-700">There are no job offers for this post yet.</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div> --}}
    <!--end::Job Offers Tab-->
</div>
<!--end::Content-->

<!-- Attachment Preview Modal -->
<div class="modal fade" id="attachmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachmentModalLabel">Attachment Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="attachmentPreviewContainer" class="d-flex justify-content-center align-items-center bg-light"
                    style="min-height: 500px;">
                    <!-- Content will be injected here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.PostDetailModule = {
            routes: {
                status: "{{ route('dashboard.posts.status', ['post' => $post->id]) }}",
            },
        };
</script>
<script>
    "use strict";
        
        // Attachment Modal Logic
        const attachmentModal = document.getElementById('attachmentModal');
        if (attachmentModal) {
            attachmentModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const fileUrl = button.getAttribute('data-file-url');
                const fileType = button.getAttribute('data-file-type');
                const fileTitle = button.getAttribute('data-file-title');
                
                const modalTitle = attachmentModal.querySelector('.modal-title');
                const container = attachmentModal.querySelector('#attachmentPreviewContainer');
                
                modalTitle.textContent = fileTitle;
                container.innerHTML = ''; // Clear previous content

                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileType.toLowerCase())) {
                    const img = document.createElement('img');
                    img.src = fileUrl;
                    img.className = 'img-fluid';
                    img.style.maxHeight = '80vh';
                    container.appendChild(img);
                } else if (fileType.toLowerCase() === 'pdf') {
                    const iframe = document.createElement('iframe');
                    iframe.src = fileUrl;
                    iframe.style.width = '100%';
                    iframe.style.height = '80vh';
                    container.appendChild(iframe);
                } else {
                    container.innerHTML = `<div class="text-center p-5">
                        <i class="ki-duotone ki-file fs-5x text-primary mb-4"><span class="path1"></span><span class="path2"></span></i>
                        <p class="fs-4 text-gray-800 mb-4">Preview not available for this file type.</p>
                        <a href="${fileUrl}" target="_blank" class="btn btn-primary">Download File</a>
                    </div>`;
                }
            });
        }

        $(document).on('click', '[data-post-action="approve"]', function() {
            updateStatus('approved');
        });

        $(document).on('click', '[data-post-action="reject"]', function() {
            Swal.fire({
                title: 'Reject Post',
                input: 'textarea',
                inputLabel: 'Reason for rejection',
                inputPlaceholder: 'Enter your reason here...',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('Reason is required');
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus('rejected', result.value);
                }
            });
        });

        function updateStatus(status, reason = null) {
            axios.patch(window.PostDetailModule.routes.status, {
                status: status,
                rejection_reason: reason
            }).then(function(response) {
                Swal.fire({
                    text: response.data.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                }).then(function() {
                    location.reload();
                });
            }).catch(function(error) {
                Swal.fire({
                    text: error.response?.data?.message || "Error updating status.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            });
        }
        function deleteAttachment(url, elementId) {
            Swal.fire({
                title: '{{ __("post::post.actions.delete") }}',
                text: "{{ __('post::post.messages.confirm_delete_attachment') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __("post::post.actions.delete") }}',
                cancelButtonText: '{{ __("post::post.actions.cancel") }}',
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-light"
                },
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(url)
                        .then(function (response) {
                            Swal.fire(
                                '{{ __("post::post.messages.deleted") }}',
                                response.data.message || 'File has been deleted.',
                                'success'
                            );
                            const element = document.getElementById(elementId);
                            if (element) {
                                element.remove();
                            }
                            
                            // Check if no attachments left, show empty state (optional but good UI)
                            if(document.querySelectorAll('.attachment-item').length === 0) {
                                location.reload();
                            }
                        })
                        .catch(function (error) {
                            Swal.fire(
                                '{{ __("Error") }}',
                                error.response?.data?.message || 'Something went wrong.',
                                'error'
                            );
                        });
                }
            });
        }
</script>
@endpush