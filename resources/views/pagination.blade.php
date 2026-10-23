@if($paginator->hasPages())
<nav data-cy="pagination" aria-label="Paginação" class="actions" style="background:transparent;color:inherit;padding:1rem 0">
    @if($paginator->onFirstPage())
        <span aria-disabled="true">Anterior</span>
    @else
        <a data-cy="pagination-previous" class="btn secondary" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
    @endif
    <span data-cy="pagination-current">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
    @if($paginator->hasMorePages())
        <a data-cy="pagination-next" class="btn secondary" href="{{ $paginator->nextPageUrl() }}" rel="next">Próxima</a>
    @else
        <span aria-disabled="true">Próxima</span>
    @endif
</nav>
@endif
