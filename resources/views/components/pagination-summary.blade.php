<Style>
    .pagination {
    display: flex;
    justify-content: flex-end;
    margin-top: 1rem;
}

.page-item .page-link {
    border-radius: 6px !important;
    padding: 6px 12px;
    border: 1px solid #dee2e6 !important;
    background-color: #fff !important;
    color: #007bff !important;
}

.page-item.active .page-link {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
}

.page-item.disabled .page-link {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    color: #6c757d !important;
}
</Style>
@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">

        {{ $paginator->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
@endif
