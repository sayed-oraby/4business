@extends('layouts.dashboard.master')

@section('title', 'طلبات المكاتب العقارية')
@section('page-title', 'طلبات المكاتب العقارية')

@push('styles')
    <style>
        .users-table-card .table thead tr {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .04em;
        }

        .users-table-card .table thead th,
        .users-table-card .table tbody td {
            text-align: center !important;
            vertical-align: middle;
        }

        .users-table-card .table thead th:first-child,
        .users-table-card .table thead th:last-child,
        .users-table-card .table tbody td:first-child,
        .users-table-card .table tbody td:last-child {
            text-align: center !important;
        }
    </style>
@endpush

@section('content')
    <div class="card card-flush shadow-sm mb-10 users-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">طلبات المكاتب العقارية</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="request-search" placeholder="بحث...">
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="officeRequestsTable">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="min-w-150px">الاسم</th>
                            <th class="min-w-150px"> رقم الهاتف </th>
                            <th class="min-w-150px">البريد الإلكتروني</th>
                            <th class="min-w-150px">اسم الشركة</th>
                            <th class="min-w-150px">  العنوان  </th>
                            <th class="min-w-100px">الحالة</th>
                            <th class="min-w-150px">تاريخ الطلب</th>
                            <th class="min-w-150px">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h3 class="fw-bold mb-1">رفض الطلب</h3>
                    <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-2"></i>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <form id="rejectionForm" class="mt-5">
                        <input type="hidden" name="user_id" id="rejectionUserId">
                        <div class="mb-5">
                            <label class="form-label required">سبب الرفض</label>
                            <textarea name="rejection_reason" class="form-control form-control-solid" rows="4" required></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                            <button type="button" class="btn btn-danger" onclick="submitRejection()">تأكيد الرفض</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let officeRequestsTable;

    $(document).ready(function() {
        officeRequestsTable = $('#officeRequestsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('dashboard.users.office-requests.data') }}",
            columns: [
                { data: 'name', name: 'name', className: 'text-gray-800 fw-bold' },
                { data: 'mobile', name: 'mobile', className: 'text-gray-600' },
                { data: 'email', name: 'email', className: 'text-gray-600' },
                { data: 'company_name', name: 'company_name', className: 'text-gray-800' },
                { 
                    data: 'address', 
                    name: 'address',
                    render: function(data) {
                        return data ? `<span class="badge badge-light-primary">${data}</span>` : '<span class="text-muted">-</span>';
                    }
                },
                { 
                    data: 'office_request_status', 
                    name: 'office_request_status',
                    render: function(data) {
                        const colors = {
                            'pending': 'warning',
                            'approved': 'success',
                            'rejected': 'danger'
                        };
                        const labels = {
                            'pending': 'قيد الانتظار',
                            'approved': 'مقبول',
                            'rejected': 'مرفوض'
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
                        let actions = '';
                        
                        if (row.office_request_status === 'pending') {
                            actions += `
                                <button onclick="updateStatus(${row.id}, 'approved')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="قبول">
                                    <i class="ki-duotone ki-check fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                                <button onclick="openRejectionModal(${row.id})" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm me-1" title="رفض">
                                    <i class="ki-duotone ki-cross fs-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                            `;
                        }

                        actions += `
                            <button onclick="deleteRequest(${row.id})" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="حذف الطلب">
                                <i class="ki-duotone ki-trash fs-2 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        `;

                        return actions;
                    }
                }
            ],
            language: {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "أظهر _MENU_ مدخلات",
                "sZeroRecords": "لم يعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
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
        $('#request-search').on('keyup', function() {
            officeRequestsTable.search(this.value).draw();
        });
    });

    function updateStatus(userId, status) {
        Swal.fire({
            title: status === 'approved' ? 'هل أنت متأكد من قبول الطلب؟' : 'تأكيد الإجراء',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، تأكيد',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                performUpdate(userId, status);
            }
        });
    }

    function openRejectionModal(userId) {
        $('#rejectionUserId').val(userId);
        $('#rejectionForm')[0].reset();
        $('#rejectionModal').modal('show');
    }

    function submitRejection() {
        const userId = $('#rejectionUserId').val();
        const reason = $('#rejectionForm textarea[name="rejection_reason"]').val();

        if (!reason.trim()) {
            toastr.error('يرجى إدخال سبب الرفض');
            return;
        }

        performUpdate(userId, 'rejected', reason);
        $('#rejectionModal').modal('hide');
    }

    function performUpdate(userId, status, rejectionReason = null) {
        $.ajax({
            url: `{{ url('dashboard/users/office-requests') }}/${userId}/status`,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status,
                rejection_reason: rejectionReason
            },
            success: function(response) {
                toastr.success(response.message);
                officeRequestsTable.ajax.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'حدث خطأ ما');
            }
        });
    }

    function deleteRequest(userId) {
        Swal.fire({
            title: 'هل أنت متأكد من حذف هذا الطلب؟',
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
                    url: `{{ url('dashboard/users/office-requests') }}/${userId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        officeRequestsTable.ajax.reload();
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
