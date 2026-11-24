{{-- One shared layout for every printed document (TRACE365, 2026-11-23, plan #9 R-8). --}}
@include('support::pdfs.document', ['document' => \Webkul\Sale\Support\OrderDocument::data($record)])
