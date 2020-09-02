 <div class="row">
    <div class="col-md-6 page_info">
        Showing {{($pagination->currentPage-1)*$pagination->perPage+1}} to {{$pagination->currentPage*$pagination->perPage}}
        of  {{$pagination->total}} entries                    
    </div>
    <div class="col-md-6 pagination_cover">
        {{ $pagination->links}}
    </div>
</div>