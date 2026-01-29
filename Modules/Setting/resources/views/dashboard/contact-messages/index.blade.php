@extends('layouts.dashboard.master')

@section('title', 'رسائل اتصل بنا')
@section('page-title', 'رسائل اتصل بنا')

@push('styles')
    <style>
        .contact-messages-card .table thead tr {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .04em;
        }

        .contact-messages-card .table thead th,
        .contact-messages-card .table tbody td {
            text-align: center !important;
            vertical-align: middle;
        }

        .contact-messages-card .table thead th:first-child,
        .contact-messages-card .table thead th:last-child,
        .contact-messages-card .table tbody td:first-child,
        .contact-messages-card .table tbody td:last-child {
            text-align: center !important;
        }

        .message-preview {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@section('content')
    <!-- Stats Cards -->
    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-xl-3">
            <div class="card stat-card bg-light-primary border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex flex-column flex-grow-1">
                        <span class="text-primary fw-bold fs-2">{{ $stats['total'] }}</span>
                        <span class="text-gray-600 fw-semibold">إجمالي الرسائل</span>
                    </div>
                    <i class="ki-duotone ki-message-text-2 fs-3x text-primary opacity-50">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card stat-card bg-light-warning border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex flex-column flex-grow-1">
                        <span class="text-warning fw-bold fs-2">{{ $stats['pending'] }}</span>
                        <span class="text-gray-600 fw-semibold">قيد الانتظار</span>
                    </div>
                    <i class="ki-duotone ki-time fs-3x text-warning opacity-50">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card stat-card bg-light-info border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex flex-column flex-grow-1">
                        <span class="text-info fw-bold fs-2">{{ $stats['read'] }}</span>
                        <span class="text-gray-600 fw-semibold">تم القراءة</span>
                    </div>
                    <i class="ki-duotone ki-eye fs-3x text-info opacity-50">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card stat-card bg-light-success border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex flex-column flex-grow-1">
                        <span class="text-success fw-bold fs-2">{{ $stats['replied'] }}</span>
                        <span class="text-gray-600 fw-semibold">تم الرد</span>
                    </div>
                    <i class="ki-duotone ki-check-circle fs-3x text-success opacity-50">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card card-flush shadow-sm mb-10 contact-messages-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">رسائل اتصل بنا</h3>
            </div>
            <div class="card-toolbar gap-3">
                <!-- Status Filter -->
                <select class="form-select form-select-solid w-150px" id="status-filter">
                    <option value="">جميع الحالات</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="read">تم القراءة</option>
                    <option value="replied">تم الرد</option>
                    <option value="closed">مغلق</option>
                </select>
                <!-- Search -->
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="message-search" placeholder="بحث...">
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="contactMessagesTable">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="min-w-50px">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                </div>
                            </th>
                            <th class="min-w-120px">الاسم</th>
                            <th class="min-w-150px">البريد الإلكتروني</th>
                            <th class="min-w-120px">رقم الجوال</th>
                            <th class="min-w-100px">الموضوع</th>
                            <th class="min-w-200px">الرسالة</th>
                            <th class="min-w-80px">الحالة</th>
                            <th class="min-w-100px">التاريخ</th>
                            <th class="min-w-100px">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Message Modal -->
    <div class="modal fade" id="viewMessageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h3 class="fw-bold mb-1">تفاصيل الرسالة</h3>
                    <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-2"></i>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="mt-5">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-gray-500">الاسم</label>
                                <p class="fw-bold text-gray-800" id="modal-name"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-gray-500">البريد الإلكتروني</label>
                                <p class="fw-bold text-gray-800" id="modal-email"></p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-gray-500">رقم الجوال</label>
                                <p class="fw-bold text-gray-800" id="modal-phone"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-gray-500">الموضوع</label>
                                <p class="fw-bold text-gray-800" id="modal-subject"></p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-gray-500">الرسالة</label>
                            <div class="bg-light-primary rounded p-4" id="modal-message"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-gray-500">تاريخ الإرسال</label>
                                <p class="fw-bold text-gray-800" id="modal-date"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-gray-500">الحالة</label>
                                <p id="modal-status"></p>
                            </div>
                        </div>
                        <div class="separator my-5"></div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <label class="form-label text-gray-500">تغيير الحالة</label>
                                <select class="form-select form-select-solid w-200px" id="modal-status-select">
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="read">تم القراءة</option>
                                    <option value="replied">تم الرد</option>
                                    <option value="closed">مغلق</option>
                                </select>
                            </div>
                            <div class="d-flex align-items-end gap-3">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                                <button type="button" class="btn btn-primary" onclick="updateMessageStatus()">حفظ الحالة</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let contactMessagesTable;
    let currentMessageId = null;

    $(document).ready(function() {
        contactMessagesTable = $('#contactMessagesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.contact-messages.data') }}",
                data: function(d) {
                    d.status = $('#status-filter').val();
                }
            },
            columns: [
                { 
                    data: 'id', 
                    orderable: false, 
                    searchable: false,
                    render: function(data) {
                        return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input row-checkbox" type="checkbox" value="${data}">
                        </div>`;
                    }
                },
                { data: 'name', name: 'name', className: 'text-gray-800 fw-bold' },
                { data: 'email', name: 'email', className: 'text-gray-600' },
                { 
                    data: null, 
                    name: 'phone', 
                    className: 'text-gray-600',
                    render: function(data) {
                        return data.country_code + data.phone;
                    }
                },
                { 
                    data: 'subject', 
                    name: 'subject',
                    render: function(data) {
                        return data ? `<span class="badge badge-light-primary">${data}</span>` : '<span class="text-muted">-</span>';
                    }
                },
                { 
                    data: 'message', 
                    name: 'message',
                    render: function(data) {
                        const preview = data.length > 50 ? data.substring(0, 50) + '...' : data;
                        return `<div class="message-preview text-gray-600" title="${data}">${preview}</div>`;
                    }
                },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        const colors = {
                            'pending': 'warning',
                            'read': 'info',
                            'replied': 'success',
                            'closed': 'secondary'
                        };
                        const labels = {
                            'pending': 'قيد الانتظار',
                            'read': 'تم القراءة',
                            'replied': 'تم الرد',
                            'closed': 'مغلق'
                        };
                        return `<span class="badge badge-light-${colors[data] || 'secondary'}">${labels[data] || data}</span>`;
                    }
                },
                { 
                    data: 'created_at', 
                    name: 'created_at',
                    render: function(data) {
                        return `<div class="badge badge-light fw-bold">${new Date(data).toLocaleDateString('ar-EG')}</div>`;
                    }
                },
                { 
                    data: null, 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button onclick="viewMessage(${row.id})" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="عرض">
                                <i class="ki-duotone ki-eye fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </button>
                            <button onclick="deleteMessage(${row.id})" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="حذف">
                                <i class="ki-duotone ki-trash fs-2 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        `;
                    }
                }
            ],
            language: {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "أظهر _MENU_ مدخلات",
                "sZeroRecords": "لا توجد رسائل",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ رسالة",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 رسالة",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ رسالة)",
                "sSearch": "ابحث:",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
                }
            }
        });

        // Search handler
        $('#message-search').on('keyup', function() {
            contactMessagesTable.search(this.value).draw();
        });

        // Status filter handler
        $('#status-filter').on('change', function() {
            contactMessagesTable.ajax.reload();
        });

        // Select all checkbox
        $('#select-all').on('change', function() {
            $('.row-checkbox').prop('checked', this.checked);
        });
    });

    function viewMessage(messageId) {
        currentMessageId = messageId;
        
        $.ajax({
            url: `{{ url('dashboard/contact-messages') }}/${messageId}`,
            type: 'GET',
            success: function(response) {
                const msg = response.message;
                $('#modal-name').text(msg.name);
                $('#modal-email').text(msg.email);
                $('#modal-phone').text(msg.country_code + msg.phone);
                $('#modal-subject').text(msg.subject || '-');
                $('#modal-message').text(msg.message);
                $('#modal-date').text(new Date(msg.created_at).toLocaleString('ar-EG'));
                $('#modal-status-select').val(msg.status);
                
                const colors = {
                    'pending': 'warning',
                    'read': 'info',
                    'replied': 'success',
                    'closed': 'secondary'
                };
                const labels = {
                    'pending': 'قيد الانتظار',
                    'read': 'تم القراءة',
                    'replied': 'تم الرد',
                    'closed': 'مغلق'
                };
                $('#modal-status').html(`<span class="badge badge-light-${colors[msg.status]}">${labels[msg.status]}</span>`);
                
                $('#viewMessageModal').modal('show');
                contactMessagesTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'حدث خطأ ما');
            }
        });
    }

    function updateMessageStatus() {
        const status = $('#modal-status-select').val();
        
        $.ajax({
            url: `{{ url('dashboard/contact-messages') }}/${currentMessageId}/status`,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                toastr.success(response.message || 'تم تحديث الحالة بنجاح');
                $('#viewMessageModal').modal('hide');
                contactMessagesTable.ajax.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'حدث خطأ ما');
            }
        });
    }

    function deleteMessage(messageId) {
        Swal.fire({
            title: 'هل أنت متأكد من حذف هذه الرسالة؟',
            text: "لا يمكن التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('dashboard/contact-messages') }}/${messageId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message || 'تم حذف الرسالة بنجاح');
                        contactMessagesTable.ajax.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'حدث خطأ ما');
                    }
                });
            }
        });
    }

    function bulkDelete() {
        const selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            toastr.warning('يرجى تحديد رسالة واحدة على الأقل');
            return;
        }

        Swal.fire({
            title: `هل أنت متأكد من حذف ${selectedIds.length} رسالة؟`,
            text: "لا يمكن التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ route('dashboard.contact-messages.bulk-delete') }}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        contactMessagesTable.ajax.reload();
                        $('#select-all').prop('checked', false);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'حدث خطأ ما');
                    }
                });
            }
        });
    }
</script>
@endpush
